<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignmentLog extends Model
{
    protected $fillable = [
        'asset_assignment_id',
        'previous_employee_id',
        'new_employee_id',
        'updated_by'
    ];

    public function assignment()
    {
        return $this->belongsTo(AssetAssignment::class, 'asset_assignment_id');
    }

    public function previousEmployee()
    {
        return $this->belongsTo(Employee::class, 'previous_employee_id');
    }

    public function newEmployee()
    {
        return $this->belongsTo(Employee::class, 'new_employee_id');
    }
}
