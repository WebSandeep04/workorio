<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceDeduction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function advance()
    {
        return $this->belongsTo(SalaryAdvance::class, 'salary_advance_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
