<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['resolved_at' => 'datetime'];
    public function patient() { return $this->belongsTo(Patient::class); }
}

