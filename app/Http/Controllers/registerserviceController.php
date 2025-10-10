<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRegistration;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegisterServiceController extends Controller
{
    public function index()
    {
        return view('registerservice');
    }

    public function store(Request $request)
    {
        // dd($request->file('attachment'));

        $validated = $request->validate([
            'department'       => 'required|string',
            'shipment'         => 'nullable|string',
            'box_number'       => 'nullable|string',
            'ul'               => 'nullable|string',
            'supplier'         => 'nullable|string',
            'AT_number'        => 'nullable|string',
            'zone'             => 'required|string',
            'reason'           => 'required|string',
            'task_name'        => 'nullable|string',
            'assigned_users'   => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'extra_info'       => 'nullable|string',
            'attachment'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'public');
            $validated['attachment'] = $path;
        } else {
            $validated['attachment'] = null;
        }

        // Create a Task record
        $task = Task::create([
            'department'    => $validated['department'],
            'shipment'      => $validated['shipment'] ?? null,
            'attachment' => $validated['attachment'] ?? null,
            'box_number'    => $validated['box_number'] ?? null,
            'ul'            => $validated['ul'] ?? null,
            'supplier'      => $validated['supplier'] ?? null,
            'AT_number'     => $validated['AT_number'] ?? null,
            'reason'        => $validated['task_name'] ?? $validated['reason'],
            'zone'          => $validated['zone'],
            'extra_info'    => $validated['extra_info'] ?? null,
            'createdUserID' => auth()->id(),
        ]);

        // Log the activity
        \App\Helpers\ActivityLogger::log(
            'created',
            $task,
            $task->id,
            [
                'name'         => $task->reason ?? null,
                'department'   => $task->departmentModel?->name ?? $task->department,
                'assigned_to'  => $task->assignedUser?->name ?? 'Unassigned',
                'time_spent'   => $task->time_spent,
            ]
        );

        // Attach users
        if (auth()->user()->role !== 'operator' && $request->filled('assigned_users')) {
            $task->users()->attach($request->assigned_users);
        } else {
            $task->users()->attach(auth()->id());
        }

        return redirect()
            ->route('registerservice')
            ->with('success', 'Service hours registered successfully.');
    }

    public function overview(Request $request)
    {
        $user = auth()->user();

        $query = $user->role === 'operator'
            ? $user->tasks()->where('validated', false)->with('users')
            : Task::with('users');

        // Filters
        if ($request->filled('validated')) {
            $query->where('validated', $request->validated === '1');
        }

        if ($request->filled('invoiced')) {
            $query->where('invoiced', $request->invoiced === '1');
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Order newest to oldest
        $query->orderBy('created_at', 'desc');

        $registrations = $query->get();

        return view('registerservice.overview', compact('registrations'));
    }



    public function show(Task $task){
        $task->load('users');
        return view('registerservice.task_users', compact('task'));
    }

    public function validateTask(Task $task)
    {
        if (!in_array(auth()->user()->role, ['teamleader', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $allDone = $task->users->every(fn($u) => $u->pivot->started_at && $u->pivot->stopped_at);

        if (!$allDone) {
            return redirect()->back()->with('error', 'Cannot validate. All users must have started and stopped their task.');
        }

        $task->update(['validated' => true]);

        \App\Helpers\ActivityLogger::log(
            'validated',
            $task,
            $task->id,
            ['validated'  => true,'department' => $task->departmentModel?->name ?? $task->department,]
        );
        return redirect()->back()->with('success', 'Task validated successfully!');
    }

    public function validateUser(Request $request, Task $task, User $user)
    {
        if (!in_array(auth()->user()->role, ['teamleader', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $pivot = $task->users()->where('user_id', $user->id)->first()->pivot;

        if (!$pivot->started_at) {
            return redirect()->back()->with('error', 'Cannot validate user. They have not started the task.');
        }

        // Only allow validation if they have both start and stop times
        if ($pivot->started_at && $pivot->stopped_at) {
            $task->users()->updateExistingPivot($user->id, [
                'validated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'User\'s time validated successfully!');
        }

        return redirect()->back()->with('error', 'Cannot validate user. They are still active or no stop time recorded.');
    }


    public function activeTasks(){
        $tasks = Task::with('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('registerservice.activetasks', compact('tasks'));
    }

    public function startTask(Task $task){
        $user = auth()->user();

        if (in_array($user->role, ['teamleader', 'admin'])) {
            $task->users()
                ->where('role', 'operator')
                ->syncWithoutDetaching(
                    $task->users->where('role', 'operator')->pluck('id')->all(), 
                    ['started_at' => now()]
                );
            return redirect()->back()->with('success', 'Task started for all assigned operators!');
        } else {
            $task->users()->syncWithoutDetaching([
                $user->id => ['started_at' => now()]
            ]);
            return redirect()->back()->with('success', 'Task started at ' . now());
        }
    }

    public function stopTask(Task $task){
        $user = auth()->user();

        if (in_array($user->role, ['teamleader', 'admin'])) {
            $assignedOperators = $task->users()->where('role', 'operator')->get();
            $stoppedAt = now();
            
            foreach ($assignedOperators as $operator) {
                $pivot = $operator->pivot;
                if ($pivot->started_at && !$pivot->stopped_at) {
                    $seconds = Carbon::parse($pivot->started_at)->diffInSeconds($stoppedAt);
                    $task->users()->updateExistingPivot($operator->id, [
                        'stopped_at' => $stoppedAt,
                        'time_spent' => $seconds
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Task stopped for all assigned operators!');
        } else {
            $pivot = $task->users()->where('user_id', $user->id)->first()->pivot;
            if (!$pivot->started_at) {
                return redirect()->back()->with('error', 'Cannot stop a task that has not been started.');
            }
            $stoppedAt = now();
            $seconds = Carbon::parse($pivot->started_at)->diffInSeconds($stoppedAt);
            $task->users()->updateExistingPivot($user->id, [
                'stopped_at' => $stoppedAt,
                'time_spent' => $seconds
            ]);
            return redirect()->back()->with('success', 'Task stopped at ' . $stoppedAt);
        }
    }


    public function startUserTask(Task $task, User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role === 'operator' && $authUser->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$task->users->contains($user->id)) {
            return redirect()->back()->with('error', 'User not assigned to this task.');
        }

        $task->users()->syncWithoutDetaching([
            $user->id => ['started_at' => now()]
        ]);

        return redirect()->back()->with('success', 'Task started for ' . $user->name);
    }

    public function stopUserTask(Task $task, User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role === 'operator' && $authUser->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$task->users->contains($user->id)) {
            return back()->with('error', 'User not assigned to this task.');
        }

        $pivot = $task->users()->where('user_id', $user->id)->first()->pivot;

        if (!$pivot->started_at) {
            return back()->with('error', 'User has not started the task.');
        }

        $stoppedAt = now();
        $seconds = Carbon::parse($pivot->started_at)->diffInSeconds($stoppedAt);

        $task->users()->updateExistingPivot($user->id, [
            'stopped_at' => $stoppedAt,
            'time_spent' => $seconds,
        ]);

        // Log the change
        \App\Helpers\ActivityLogger::log(
        'time stopped',
        $task,
        $task->id,
            [
                'time_stopped' => $task->$stoppedAt,
            ]
        );

        return back()->with('success', 'Task stopped for ' . $user->name);
    }


    public function updateTime(Request $request, Task $task, User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'time_spent' => 'required|integer|min:0',
        ]);

        $pivot = $task->users()->where('user_id', $user->id)->first()->pivot;

        $oldTime = $pivot->time_spent;
        $seconds = $request->time_spent * 60;

        $pivot->time_spent = $seconds;
        if ($pivot->started_at) {
            $pivot->stopped_at = Carbon::parse($pivot->started_at)->addSeconds($seconds);
        } else {
            $pivot->stopped_at = null;
        }
        $pivot->save();

        \App\Helpers\ActivityLogger::log(
            'updated',
            $task,
            $task->id,
            [
                'field' => 'time_spent',
                'old'   => $oldTime,
                'new'   => $seconds,
                'department' => $task->departmentModel?->name ?? $task->department,
            ]
        );
        return back()->with('success', 'Time updated successfully.');
    }


    public function invoiceTask(Task $task)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        if (!$task->validated || $task->invoiced) {
            return back()->with('error', 'This task cannot be invoiced.');
        }

        // Load department relation
        $task->load('users', 'departmentModel');

        $totalSeconds = 0;
        foreach ($task->users as $user) {
            if ($user->pivot->started_at && $user->pivot->stopped_at) {
                $totalSeconds += \Carbon\Carbon::parse($user->pivot->started_at)
                    ->diffInSeconds(\Carbon\Carbon::parse($user->pivot->stopped_at));
            }
        }
        $totalMinutes = ceil($totalSeconds / 60);
        $hours = $totalMinutes / 60; 

        // 45% rule for 'promo %' 
        if (strtolower($task->department) === 'promo %') {
            $hours = $hours * 0.45;
        }

        $rate = $task->departmentModel?->rate ?? 40.00;
        $totalPrice = $hours * $rate;

        $task->update([
            'invoiced' => true,
            'invoiced_at' => now(),
        ]);

        \App\Helpers\ActivityLogger::log(
            'invoiced',
            $task,
            $task->id,
            [
                'validated' => $task->validated,
                'invoiced'  => true,
                'rate'      => $rate,
                'totalPrice'=> $totalPrice,
                'department' => $task->departmentModel?->name ?? $task->department,
            ]
        );

        // Generate PDF
        $pdf = Pdf::loadView('invoices.task', [
            'task' => $task,
            'hours' => $hours,
            'minutes' => $totalMinutes,
            'rate' => $rate,
            'totalPrice' => $totalPrice,
        ]);

        return $pdf->download("invoice_task_{$task->id}.pdf");
    }

    
    public function bulkInvoice(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $from = Carbon::parse($request->from_date)->startOfDay();
        $to   = Carbon::parse($request->to_date)->endOfDay();

        $tasks = Task::where('validated', true)
            ->where('invoiced', false)
            ->whereBetween('created_at', [$from, $to])
            ->with('users')
            ->get();

        // dd($request->from_date, $request->to_date, $tasks->pluck('id'));
        if ($tasks->isEmpty()) {
            return back()->with('error', 'No tasks available to invoice in this period.');
        }


        foreach ($tasks as $task) {
            $task->update([
                'invoiced' => true,
                'invoiced_at' => now(),
            ]);


            \App\Helpers\ActivityLogger::log(
                'invoiced',
                $task,
                $task->id,
                [
                    'validated' => $task->validated,
                    'invoiced'  => true,
                ]
            );
        }

        $pdf = Pdf::loadView('invoices.bulk', [
            'tasks' => $tasks,
            'from' => $request->from_date,
            'to'   => $request->to_date,
        ]);

        return $pdf->download("bulk_invoice_{$request->from_date}_to_{$request->to_date}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'operator') {
            $query = $user->tasks()->where('validated', false)->with('users');
        } else {
            $query = Task::with('users');
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->whereBetween('service_registrations.started_at', [
                    $request->from_date,
                    \Carbon\Carbon::parse($request->to_date)->endOfDay()
                ]);
            });
        }
        if ($request->filled('validated')) {
            $query->where('validated', $request->validated);
        }
        if ($request->filled('invoiced')) {
            $query->where('invoiced', $request->invoiced);
        }

        $registrations = $query->get();

        $response = new StreamedResponse(function () use ($registrations) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Department', 'Shipment', 'Box#', 'U.L.', 'Supplier',
                'AT#', 'Zone', 'Reason', 'Time Spent (HH:MM:SS)',
                'Validated', 'Invoiced', 'Assigned Users'
            ]);

            foreach ($registrations as $registration) {
                $totalSeconds = 0;
                foreach ($registration->users as $user) {
                    $p = $user->pivot;
                    if ($p->started_at && $p->stopped_at) {
                        $totalSeconds += \Carbon\Carbon::parse($p->started_at)
                            ->diffInSeconds(\Carbon\Carbon::parse($p->stopped_at));
                    }
                }

                fputcsv($handle, [
                    $registration->department,
                    $registration->shipment,
                    $registration->box_number,
                    $registration->ul,
                    $registration->supplier,
                    $registration->AT_number,
                    $registration->zone,
                    $registration->reason,
                    $totalSeconds > 0 ? gmdate('H:i:s', $totalSeconds) : '',
                    $registration->validated ? 'Yes' : 'No',
                    $registration->invoiced ? 'Yes' : 'No',
                    $registration->users->pluck('name')->implode(', '),
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="service_hours_export.csv"');

        return $response;
    }

    public function destroy(Task $task)
    {
        // Only admin or teamleader can delete
        if (!in_array(auth()->user()->role, ['admin', 'teamleader'])) {
            abort(403, 'Unauthorized action.');
        }

        // Optional: delete attachment if exists
        if ($task->attachment && \Storage::disk('public')->exists($task->attachment)) {
            \Storage::disk('public')->delete($task->attachment);
        }

        $task->delete();

        // Log the deletion
        \App\Helpers\ActivityLogger::log(
            'deleted',
            $task,
            $task->id,
            ['department' => $task->department, 'reason' => $task->reason]
        );

        return redirect()
            ->route('overview')
            ->with('success', 'Task deleted successfully.');
    }

}
