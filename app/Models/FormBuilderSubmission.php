<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormBuilderSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_builder_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}


