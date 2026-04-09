<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallingList extends Model
{
    protected $fillable = [
        'name',
        'total_records',
    ];

    public function callings()
    {
        return $this->hasMany(Calling::class, 'list_id');
    }
}
