<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['order_date' => 'date', 'expected_date' => 'date'];
    public function branch() { return $this->belongsTo(Branch::class); }
}

