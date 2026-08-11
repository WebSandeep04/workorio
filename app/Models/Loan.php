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
        $paidPrincipal = $this->installments()->where('status', 'paid')->sum('principal_component');
        // Fallback for old loans where principal_component might be 0
        if ($paidPrincipal == 0 && $this->installments()->where('status', 'paid')->sum('amount') > 0) {
             $paidPrincipal = $this->installments()->where('status', 'paid')->sum('amount');
        }
        return $this->amount - $paidPrincipal;
    }

    public function remainingTotalPayable()
    {
        $paidAmount = $this->installments()->where('status', 'paid')->sum('amount');
        return $this->total_payable > 0 ? ($this->total_payable - $paidAmount) : ($this->amount - $paidAmount);
    }

    public function outstandingInterest()
    {
        $paidInterest = $this->installments()->where('status', 'paid')->sum('interest_component');
        return $this->total_interest - $paidInterest;
    }
}
