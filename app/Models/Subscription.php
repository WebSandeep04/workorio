<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sales_record_id',
        'product_id',
        'user_id',
        'subscription_name',
        'start_date',
        'status',
        'amount',
        'billing_type',
        'is_recurring',
        'recurrence_type',
        'alert_before_days',
        'recurrence_interval',
        'recurrence_days_of_week',
        'recurrence_day_of_month',
        'recurrence_months',
        'recurrence_end_date',
        'notes',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'recurrence_days_of_week' => 'array',
        'recurrence_months' => 'array',
        'alert_before_days' => 'integer',
        'start_date' => 'date',
        'recurrence_end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Subscription belongs to a customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship: Subscription belongs to a sales record
     */
    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class);
    }

    /**
     * Relationship: Subscription belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(SalesProduct::class, 'product_id');
    }

    /**
     * Relationship: Subscription assigned to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Subscription created by a user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    /**
     * Relationship: Get the latest history record for the subscription
     */
    public function latestHistory()
    {
        return $this->hasOne(SubscriptionHistory::class)->latestOfMany();
    }

    /**
     * Relationship: Get all history records for the subscription
     */
    public function histories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
