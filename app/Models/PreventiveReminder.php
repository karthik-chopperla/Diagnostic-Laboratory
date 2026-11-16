<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreventiveReminder extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'due_date' => 'date',
        'last_sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

