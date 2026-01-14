<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionStatus extends Model
{
    protected $table = 'subscription_status';

    protected $fillable = [
        'status_name',
    ];
}
