<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProjectRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_project_id',
        'user_id',
        'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(CustomerProject::class, 'customer_project_id');
    }
}
