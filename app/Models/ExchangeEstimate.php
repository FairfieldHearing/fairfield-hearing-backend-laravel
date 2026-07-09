<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExchangeEstimate extends Model
{
    protected $fillable = [
        'session_id',
        'unique_hash',
        'hearing_aid_model_id',
        'want_exchange',
        'old_brand',
        'old_model',
        'old_price_band',
        'old_age_band',
        'old_condition_band',
        'calculated_value',
        'final_price',
    ];

    protected $casts = [
        'want_exchange' => 'boolean',
        'calculated_value' => 'integer',
        'final_price' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->unique_hash)) {
                $model->unique_hash = 'ex-' . strtolower(Str::random(8));
            }
        });
    }

    public function hearingAidModel()
    {
        return $this->belongsTo(HearingAidModel::class, 'hearing_aid_model_id');
    }
}
