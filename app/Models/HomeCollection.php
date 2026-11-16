<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCollection extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['scheduled_date' => 'date', 'picked_up_at' => 'datetime', 'checklist' => 'array'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function phlebotomist() { return $this->belongsTo(Employee::class, 'phlebotomist_id'); }
}

