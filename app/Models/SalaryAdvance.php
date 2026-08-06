<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function deductions()
    {
        return $this->hasMany(SalaryAdvanceDeduction::class, 'salary_advance_id');
    }

    public function remainingBalance()
    {
        $paidAmount = $this->deductions()->sum('amount');
        return max(0, $this->amount - $paidAmount);
    }
}
