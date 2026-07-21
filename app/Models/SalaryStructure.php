<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function components() { return $this->belongsToMany(SalaryComponent::class, 'salary_structure_components')->withPivot('value', 'formula'); }
}
