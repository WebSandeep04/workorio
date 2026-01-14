<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'role_name', 
        'description', 
        'is_custom', 
        'created_by',
        'permissions_data'
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'permissions_data' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Removed tenant() relationship - no longer needed with separate databases
}
