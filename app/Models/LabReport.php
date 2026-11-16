<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabReport extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['approved_at' => 'datetime', 'delivered_at' => 'datetime', 'delivered_via' => 'array'];

    public function labOrder() { return $this->belongsTo(LabOrder::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}

