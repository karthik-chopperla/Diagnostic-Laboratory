<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['dob' => 'date', 'is_merged' => 'boolean', 'consent_given' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function corporate() { return $this->belongsTo(Corporate::class); }
    public function familyHead() { return $this->belongsTo(Patient::class, 'family_head_id'); }
    public function familyMembers() { return $this->hasMany(Patient::class, 'family_head_id'); }
    public function documents() { return $this->hasMany(PatientDocument::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function samples() { return $this->hasMany(Sample::class); }
    public function labOrders() { return $this->hasMany(LabOrder::class); }
    public function labReports() { return $this->hasMany(LabReport::class); }
}

