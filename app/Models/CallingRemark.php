<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallingRemark extends Model
{
    protected $fillable = [
        'calling_id',
        'user_id',
        'remark',
        'next_follow_up_date',
    ];

    public function calling()
    {
        return $this->belongsTo(Calling::class);
    }
}


