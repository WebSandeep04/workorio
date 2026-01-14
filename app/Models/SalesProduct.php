<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $table = 'sales_products';
    protected $fillable = ['product_name',];

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'products_id');
    }
}
