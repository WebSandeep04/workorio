<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'name',
        'working_hours',
        'description',];

    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }
}
