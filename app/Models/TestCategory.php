<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCategory extends Model
{
    protected $guarded = ['id'];
    public function department() { return $this->belongsTo(Department::class); }
    public function tests() { return $this->hasMany(Test::class, 'category_id'); }
}

