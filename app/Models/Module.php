<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'service_id',];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function customerProjectModules()
    {
        return $this->hasMany(CustomerProjectModule::class);
    }
}
