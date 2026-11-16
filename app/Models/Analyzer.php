<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyzer extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'last_calibrated_at' => 'date',
        'next_calibration_at' => 'date',
        'last_maintenance_at' => 'date',
        'next_maintenance_at' => 'date',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function logs() { return $this->hasMany(AnalyzerLog::class); }
    public function qcRecords() { return $this->hasMany(QcRecord::class); }
}

