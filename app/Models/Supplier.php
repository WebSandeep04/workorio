<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'mobile',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'pincode',
        'gst_number',
        'pan_number',
        'website',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'status',
        'remarks',
    ];
}
