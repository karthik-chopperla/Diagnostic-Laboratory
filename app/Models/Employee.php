<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['joined_at' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function attendances() { return $this->hasMany(EmployeeAttendance::class); }
}

