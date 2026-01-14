<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcategoryUserAccess extends Model
{
    protected $table = 'subcategory_user_access';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'user_id'
    ];

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    /**
     * Get the subcategory
     */
    public function subcategory()
    {
        return $this->belongsTo(DocumentSubcategory::class, 'subcategory_id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
