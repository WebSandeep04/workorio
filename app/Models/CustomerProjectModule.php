<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProjectModule extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'customer_project_id',
        'module_id',
        'status',
        'start_date',
        'end_date',
        'description',];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function customerProject()
    {
        return $this->belongsTo(CustomerProject::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
