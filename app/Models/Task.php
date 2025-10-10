<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'department',
        'shipment',
        'box_number',
        'ul',
        'supplier',
        'AT_number',
        'elapsed_time',
        'reason',
        'zone',
        'extra_info',
        'createdUserID',
        'validated',
        'invoiced',
        'invoiced_at',
        'attachment',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'service_registrations', 'task_id', 'user_id')
                ->withPivot(['started_at', 'stopped_at'])
                ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceRegistrations()
    {
        return $this->hasMany(ServiceRegistration::class);
    }

    protected $casts = [
    'started_at' => 'datetime',
    'stopped_at'   => 'datetime',];

    public function getStatusAttribute(){
        if ($this->validated && $this->invoiced) {
            return '💰 Invoiced';
        }
        if ($this->validated && !$this->invoiced) {
            return '✅ Validated';
        }
        // Check if any user has started
        $hasStarted = $this->users()->whereNotNull('service_registrations.started_at')->exists();
        if (!$hasStarted) {
            return '🚫 Not Started';
        }
        return '⏳ Active';
    }

    public function timeLogs()
    {
        return $this->hasMany(TaskUserTime::class);
    }

    public function departmentModel()
    {
        return $this->belongsTo(Department::class, 'department', 'name');
        
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

}
