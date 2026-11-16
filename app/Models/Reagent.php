<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reagent extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['expiry_date' => 'date'];
    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
}

