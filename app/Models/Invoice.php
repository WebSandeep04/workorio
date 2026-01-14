<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'sales_record_id',
        'customer_id',
        'product_id',
        'invoice_number',
        'amount',
        'due_date',
        'status',
        'notes',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime'
    ];

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class);
    }

    public function followups()
    {
        return $this->hasMany(InvoiceFollowup::class);
    }
    
    public function product()
    {
        return $this->belongsTo(SalesProduct::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

