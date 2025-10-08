<?php
// app/Http/Controllers/DepartmentController.php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Supplier;
use App\Models\Zone;
use App\Models\Reason;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $suppliers   = Supplier::all();
        $zones = Zone::all();
        $reasons = Reason::all();

        return view('departments.index', compact('departments', 'suppliers', 'zones', 'reasons'));
    }

    public function store(Request $request)
    {
        if ($request->has('rate')) {
            // Department create
            $validated = $request->validate([
                'name' => 'required|string|unique:departments,name',
                'rate' => 'required|numeric',
            ]);
            $department = Department::create($validated);
            // Audit log
            \App\Helpers\ActivityLogger::log(
                'created',
                $department,
                $department->id,
                [
                    'name'  => $department->name,
                    'rate'  => $department->rate,
                    'field' => 'department',
                ]);
            return back()->with('success', 'Department added.');
        } else {
            // Supplier create
            $validated = $request->validate([
                'name' => 'required|string|unique:suppliers,name',
            ]);
            $supplier = Supplier::create($validated);
            // Audit log
            \App\Helpers\ActivityLogger::log(
                'created',
                $supplier,
                $supplier->id,
                [
                    'name'  => $supplier->name,
                    'field' => 'supplier',
                ]);
            return back()->with('success', 'Supplier added.');
        }
    }

    public function storeZone(Request $request)
    {
        $request->validate([
            'zoneName' => 'required|string|unique:zones,zoneName',
        ]);

        Zone::create($request->only('zoneName'));
        return back()->with('success', 'Zone added.');
    }

    public function storeReason(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|unique:reasons,reason',
        ]);

        // Create the reason
        $reason = Reason::create($request->only('reason'));
        // Log the creation
        \App\Helpers\ActivityLogger::log(
            'created',
            $reason,
            $reason->id,
            [
                'name'  => $reason->reason,
                'field' => 'reason',
            ]);
        return back()->with('success', 'Reason added.');}


    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|unique:departments,name,'.$department->id,
            'rate' => 'required|numeric',
        ]);

        $department->update($request->only('name','rate'));
        return back()->with('success', 'Department updated.');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|unique:suppliers,name,'.$supplier->id,
        ]);

        $supplier->update($request->only('name'));
        return back()->with('success', 'Supplier updated.');
    }

    public function updateZone(Request $request, Zone $zone)
    {
        $request->validate([
            'zoneName' => 'required|string|unique:zones,zoneName,'.$zone->id,
        ]);

        $zone->update($request->only('zoneName'));
        return back()->with('success', 'Zone updated.');
    }

    public function updateReason(Request $request, Reason $reason)
    {
        $request->validate([
            'reason' => 'required|string|unique:reasons,reason,' . $reason->id,
        ]);

        $reason->update($request->only('reason'));

        // Optional: log it
        \App\Helpers\ActivityLogger::log(
            'updated',
            $reason,
            $reason->id,
            [
                'name'  => $reason->reason,
                'field' => 'reason',
            ]
        );
        return back()->with('success', 'Reason updated.');
    }


    public function destroyDepartment(Department $department)
    {
        $name = $department->name; 
        $id   = $department->id;

        $department->delete();

        \App\Helpers\ActivityLogger::log(
            'deleted',
            $department,
            $id,
            [
                'name'  => $name,
                'field' => 'department',
            ]
        );
        return back()->with('success', 'Department deleted.');
    }

    public function destroySupplier(Supplier $supplier)
    {              
        $name = $supplier->name; 
        $id   = $supplier->id;

        $supplier->delete();

        \App\Helpers\ActivityLogger::log(
            'deleted',
            $supplier,
            $id,
            [
                'name'  => $name,
                'field' => 'supplier',
            ]
        );

        return back()->with('success', 'Supplier deleted.');
    }

    public function destroyZone(Zone $zone)
    {
        $zoneName = $zone->zoneName; 
        $id       = $zone->id;

        $zone->delete();

        \App\Helpers\ActivityLogger::log(
            'deleted',
            $zone,
            $id,
            [
                'name'  => $zoneName,
                'field' => 'zone',
            ]
        );

        return back()->with('success', 'Zone deleted.');
    }

    public function destroyReason(Reason $reason)
    {
        // Log before deleting
        \App\Helpers\ActivityLogger::log(
            'deleted',
            $reason,
            $reason->id,
            [
                'name'  => $reason->reason,
                'field' => 'reason',
            ]
        );

        // delete
        $reason->delete();

        return back()->with('success', 'Reason deleted.');
    }

    public function updateDepartmentRate(Request $request, Department $department)
    {
        $oldRate = $department->rate;
        $department->update(['rate' => $request->rate]);

        \App\Helpers\ActivityLogger::log(
            'updated',
            $department,
            $department->id,
            [
                'field' => 'rate',
                'old'   => $oldRate,
                'new'   => $request->rate,
            ]
        );

        return back()->with('success', 'Department rate updated.');
    }

}
