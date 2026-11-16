<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_central' => 'boolean', 'is_active' => 'boolean'];

    public function patients() { return $this->hasMany(Patient::class); }
    public function employees() { return $this->hasMany(Employee::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function samples() { return $this->hasMany(Sample::class); }
    public function analyzers() { return $this->hasMany(Analyzer::class); }
}
