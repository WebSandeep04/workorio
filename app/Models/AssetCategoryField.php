<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategoryField extends Model
{
    protected $fillable = ['asset_category_id', 'name', 'type', 'options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
}
