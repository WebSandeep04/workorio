<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAssignmentLog extends Model
{
    protected $table = 'lead_assignment_logs';

    protected $fillable = [
        'sales_record_id',
        'from_user_id',
        'to_user_id',
        'assigned_by',
        'remark',
    ];

    public function lead()
    {
        return $this->belongsTo(SalesRecord::class, 'sales_record_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
