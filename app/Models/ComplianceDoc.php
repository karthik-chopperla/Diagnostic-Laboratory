<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceDoc extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['valid_until' => 'date'];
    public function branch() { return $this->belongsTo(Branch::class); }
}

