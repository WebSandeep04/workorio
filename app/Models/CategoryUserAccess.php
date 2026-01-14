<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryUserAccess extends Model
{
    protected $table = 'category_user_access';

    protected $fillable = [
        'category_id',
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
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
