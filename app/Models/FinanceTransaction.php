<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['transaction_date' => 'date'];
    public function branch() { return $this->belongsTo(Branch::class); }
}

