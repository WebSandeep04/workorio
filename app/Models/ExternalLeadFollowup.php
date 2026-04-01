<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalLeadFollowup extends Model
{
    protected $fillable = ['lead_id', 'comment', 'user_id'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
