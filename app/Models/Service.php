<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'name',
        'description',];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function customerServices()
    {
        return $this->hasMany(CustomerProject::class);
    }
}


