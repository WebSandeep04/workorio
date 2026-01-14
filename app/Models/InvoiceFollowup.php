<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceFollowup extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_id',
        'notes',
        'next_followup_date'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'next_followup_date' => 'date'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

