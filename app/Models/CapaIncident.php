<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapaIncident extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['due_date' => 'date'];
    public function branch() { return $this->belongsTo(Branch::class); }
}

