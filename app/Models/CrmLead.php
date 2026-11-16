<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'follow_up_date' => 'date',
        'last_contacted_at' => 'datetime',
    ];
    public function branch() { return $this->belongsTo(Branch::class); }
}

