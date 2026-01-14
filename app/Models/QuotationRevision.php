<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  QuotationRevision extends Model
{
    use HasFactory;

    protected $table = 'quotation_revisions';

    // Disable updated_at since the table only has created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'quotation_id',
        'version',
        'file_path',
        'data',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function  quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
