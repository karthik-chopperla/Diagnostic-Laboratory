<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['fasting_required' => 'boolean', 'is_active' => 'boolean', 'branch_availability' => 'array'];

    public function category() { return $this->belongsTo(TestCategory::class, 'category_id'); }
    public function department() { return $this->belongsTo(Department::class); }
    public function parameters() { return $this->hasMany(TestParameter::class)->orderBy('sort_order'); }
    public function packages() { return $this->belongsToMany(TestPackage::class, 'package_test'); }
}

