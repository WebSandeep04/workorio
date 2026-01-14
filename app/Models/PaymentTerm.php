<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'advance_percentage',
        'design_dev_percentage',
        'completion_percentage',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'advance_percentage' => 'integer',
        'design_dev_percentage' => 'integer',
        'completion_percentage' => 'integer'
    ];

    /**
     * Scope for active payment terms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get formatted payment terms for display
     */
    public function getFormattedTermsAttribute()
    {
        return [
            'Advance on project confirmation' => $this->advance_percentage . '%',
            'Upon design & development approval' => $this->design_dev_percentage . '%',
            'Upon completion of development before deployment' => $this->completion_percentage . '%'
        ];
    }
}