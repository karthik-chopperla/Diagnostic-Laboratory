<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyzerLog extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['logged_at' => 'datetime'];
    public function analyzer() { return $this->belongsTo(Analyzer::class); }
}

