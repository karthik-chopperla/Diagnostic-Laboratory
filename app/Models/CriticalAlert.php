<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriticalAlert extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['acknowledged_at' => 'datetime', 'notified_pathologist' => 'boolean', 'notified_technician' => 'boolean', 'notified_doctor' => 'boolean'];

    public function labOrder() { return $this->belongsTo(LabOrder::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
}

