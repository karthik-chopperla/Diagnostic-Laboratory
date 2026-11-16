<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corporate extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['contract_start' => 'date', 'contract_end' => 'date'];

    public function patients() { return $this->hasMany(Patient::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}

