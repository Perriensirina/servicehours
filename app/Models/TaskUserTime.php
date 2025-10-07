<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TaskUserTime extends Model
{
    protected $fillable = ['task_id', 'user_id', 'started_at', 'stopped_at', 'time_spent'];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateDuration()
    {
        if ($this->started_at && $this->stopped_at) {
            return Carbon::parse($this->started_at)->diffInSeconds(Carbon::parse($this->stopped_at));
        }
        return 0;
    }
}
