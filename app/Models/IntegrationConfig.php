<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationConfig extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['last_sync_at' => 'datetime'];
}

