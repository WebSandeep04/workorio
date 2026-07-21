<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['effective_from' => 'date'];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function structure() { return $this->belongsTo(SalaryStructure::class, 'salary_structure_id'); }
}
