<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmCampaign extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'campaign_name', 'name');
    }
}

