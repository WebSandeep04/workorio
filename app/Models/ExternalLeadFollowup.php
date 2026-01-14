<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalLeadFollowup extends Model
{
    protected $fillable = ['lead_id', 'comment'];
}
