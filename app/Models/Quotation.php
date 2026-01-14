<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number',
        'customer_type',
        'customer_id',
        'payment_term_id',
        'project_timeline',
        'total_amount',
        'status',
        'version',
        'file_path',
        'data',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function revisions()
    {
        return $this->hasMany(QuotationRevision::class);
    }
}
