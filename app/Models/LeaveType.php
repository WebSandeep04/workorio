<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_paid',
        'color_code',
        'status',
    ];

    public function rules()
    {
        return $this->hasMany(EmploymentTypeLeaveRule::class);
    }
}
