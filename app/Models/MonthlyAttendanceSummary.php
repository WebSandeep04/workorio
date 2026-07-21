<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyAttendanceSummary extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['total_cycles' => 'array', 'late_logs' => 'array'];
    public function employee() { return $this->belongsTo(Employee::class); }
}
