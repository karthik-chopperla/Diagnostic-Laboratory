<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['scheduled_at' => 'datetime', 'sms_sent' => 'boolean', 'whatsapp_sent' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function test() { return $this->belongsTo(Test::class); }
}

