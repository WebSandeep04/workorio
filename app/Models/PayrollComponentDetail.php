<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollComponentDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function detail() { return $this->belongsTo(PayrollDetail::class, 'payroll_detail_id'); }
    public function salaryComponent() { return $this->belongsTo(SalaryComponent::class, 'salary_component_id'); }
}
