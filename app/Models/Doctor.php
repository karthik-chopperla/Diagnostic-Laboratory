<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['portal_enabled' => 'boolean'];

    public function appointments() { return $this->hasMany(Appointment::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function samples() { return $this->hasMany(Sample::class); }
}

