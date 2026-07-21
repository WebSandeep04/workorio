<?php

$models = [
    'SalaryComponent' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass SalaryComponent extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n}\n",
    'SalaryStructure' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass SalaryStructure extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    public function components() { return \$this->belongsToMany(SalaryComponent::class, 'salary_structure_components')->withPivot('value', 'formula'); }\n}\n",
    'SalaryStructureComponent' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass SalaryStructureComponent extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n}\n",
    'MonthlyAttendanceSummary' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass MonthlyAttendanceSummary extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    protected \$casts = ['total_cycles' => 'array', 'late_logs' => 'array'];\n    public function employee() { return \$this->belongsTo(Employee::class); }\n}\n",
    'PayrollSetting' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass PayrollSetting extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n}\n",
    'StatutoryRule' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass StatutoryRule extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n}\n",
    'EmployeeSalary' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass EmployeeSalary extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    protected \$casts = ['effective_from' => 'date'];\n    public function employee() { return \$this->belongsTo(Employee::class); }\n    public function structure() { return \$this->belongsTo(SalaryStructure::class, 'salary_structure_id'); }\n}\n",
    'Payroll' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass Payroll extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    public function details() { return \$this->hasMany(PayrollDetail::class); }\n}\n",
    'PayrollDetail' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass PayrollDetail extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    public function payroll() { return \$this->belongsTo(Payroll::class); }\n    public function employee() { return \$this->belongsTo(Employee::class); }\n    public function components() { return \$this->hasMany(PayrollComponentDetail::class); }\n}\n",
    'PayrollComponentDetail' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass PayrollComponentDetail extends Model\n{\n    use HasFactory;\n    protected \$guarded = [];\n    public function detail() { return \$this->belongsTo(PayrollDetail::class, 'payroll_detail_id'); }\n    public function component() { return \$this->belongsTo(SalaryComponent::class, 'salary_component_id'); }\n}\n"
];

foreach($models as $name => $content) {
    file_put_contents('d:/DontDelete/laravel/leadmanagement (akrati ui work)/app/Models/' . $name . '.php', $content);
}

echo "Models updated successfully.\n";
