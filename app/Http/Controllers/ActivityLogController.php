<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    /**
     * Show the activity logs.
     */
    public function index()
    {
        // Load logs with user relationship, newest first, paginate 20 per page
        $logs = ActivityLog::with('user')->latest()->paginate(20);

        // Send to the Blade view
        return view('auth.index', compact('logs'));
    }
}
