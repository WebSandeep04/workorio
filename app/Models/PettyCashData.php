<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashData extends Model
{
    protected $table = 'petty_cash_datas';
    
    protected $fillable = [
        'expense_id',
        'department_id',
        'price',
        'is_approved',
        'attachment',
        'remark',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
