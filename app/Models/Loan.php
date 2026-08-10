<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id');
    }

    public function remainingBalance()
    {
        $paidAmount = $this->installments()->where('status', 'paid')->sum('amount');
        return $this->amount - $paidAmount;
    }
}
