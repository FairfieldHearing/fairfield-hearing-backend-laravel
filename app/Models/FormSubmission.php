<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'full_name',
        'mobile_number',
        'email',
        'hearing_problem',
        'location_id',
        'preferred_day_time',
        'message',
        'exchange_estimate_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function exchangeEstimate()
    {
        return $this->belongsTo(ExchangeEstimate::class, 'exchange_estimate_id');
    }
}
