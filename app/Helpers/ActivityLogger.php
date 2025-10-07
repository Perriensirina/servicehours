<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $model, $modelId = null, $changes = [])
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => $action,
            'model'   => class_basename($model),
            'model_id'=> $modelId,
            'changes' => $changes,
        ]);
    }
}
