<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['tat_due_at' => 'datetime', 'completed_at' => 'datetime', 'verified_at' => 'datetime', 'approved_at' => 'datetime', 'is_repeat' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function sample() { return $this->belongsTo(Sample::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function test() { return $this->belongsTo(Test::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function technician() { return $this->belongsTo(Employee::class, 'assigned_technician_id'); }
    public function results() { return $this->hasMany(TestResult::class); }
    public function criticalAlerts() { return $this->hasMany(CriticalAlert::class); }
    public function report() { return $this->hasOne(LabReport::class); }
}

