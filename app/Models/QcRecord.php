<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcRecord extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['expiry_date' => 'date', 'recorded_at' => 'datetime'];
    public function branch() { return $this->belongsTo(Branch::class); }
    public function analyzer() { return $this->belongsTo(Analyzer::class); }
    public function department() { return $this->belongsTo(Department::class); }
}

