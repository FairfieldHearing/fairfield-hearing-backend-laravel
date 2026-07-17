<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'form_submission_id',
        'assigned_user_id',
        'full_name',
        'mobile_number',
        'email',
        'hearing_problem',
        'location_id',
        'preferred_day_time',
        'message',
        'status',
        'logs',
    ];

    protected $casts = [
        'logs' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function formSubmission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
