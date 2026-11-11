<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function details() { return $this->hasMany(PayrollDetail::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
