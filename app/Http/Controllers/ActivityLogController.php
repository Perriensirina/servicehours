<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use App\Models\ActivityLog;   

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ActivityLog::query()->with('user');

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->get();

        return view('auth.index', compact('logs'));
    }

}




