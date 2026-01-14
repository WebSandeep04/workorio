<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_builders';

    protected $fillable = [
        'name',
        'fields',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'db_database',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fields' => 'array',
    ];
}


