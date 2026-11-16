<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['paid_at' => 'datetime'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}

