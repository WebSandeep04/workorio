<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPenalty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function payrollDetail()
    {
        return $this->belongsTo(PayrollDetail::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
