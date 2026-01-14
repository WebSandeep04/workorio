<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worklog extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'work_date',
        'entry_type_id',
        'entry_type_name',
        'customer_id',
        'customer_name',
        'service_id',
        'service_name',
        'module_id',
        'module_name',
        'customer_project_id',
        'customer_project_name',
        'hours',
        'minutes',
        'description',
        'status',
        'user_id',];

    protected $casts = [
        'work_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function customerProject()
    {
        return $this->belongsTo(CustomerProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get total minutes for this worklog entry
    public function getTotalMinutesAttribute()
    {
        return ($this->hours * 60) + $this->minutes;
    }

    // Get formatted time
    public function getFormattedTimeAttribute()
    {
        return sprintf('%02d:%02d', $this->hours, $this->minutes);
    }
}
