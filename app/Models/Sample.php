<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['collected_at' => 'datetime', 'received_at' => 'datetime', 'disposal_date' => 'date', 'is_stat' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function collector() { return $this->belongsTo(Employee::class, 'collector_id'); }
    public function transferredToBranch() { return $this->belongsTo(Branch::class, 'transferred_to_branch_id'); }
    public function labOrders() { return $this->hasMany(LabOrder::class); }
}

