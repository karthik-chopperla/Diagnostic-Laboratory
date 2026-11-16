<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $guarded = ['id'];
    public function labOrder() { return $this->belongsTo(LabOrder::class); }
    public function parameter() { return $this->belongsTo(TestParameter::class, 'test_parameter_id'); }
}

