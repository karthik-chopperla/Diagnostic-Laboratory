<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPackage extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_corporate' => 'boolean', 'is_active' => 'boolean'];

    public function tests() { return $this->belongsToMany(Test::class, 'package_test'); }
}

