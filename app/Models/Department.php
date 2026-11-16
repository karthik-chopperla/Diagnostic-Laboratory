<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    public function categories() { return $this->hasMany(TestCategory::class); }
    public function tests() { return $this->hasMany(Test::class); }
    public function labOrders() { return $this->hasMany(LabOrder::class); }
    public function analyzers() { return $this->hasMany(Analyzer::class); }
    public function employees() { return $this->hasMany(Employee::class); }
}

