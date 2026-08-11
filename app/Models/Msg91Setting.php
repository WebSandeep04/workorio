<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Msg91Setting extends Model
{
    protected $fillable = [
        'auth_key',
        'whatsapp_number',
        'whatsapp_namespace',
    ];
}
