<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorklogApproval extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'worklog_id',
        'approved_by',
        'status', // approved | rejected
        'rating', // met | below | exceeded (nullable for rejected)
        'remark',];

    public function worklog()
    {
        return $this->belongsTo(Worklog::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}


