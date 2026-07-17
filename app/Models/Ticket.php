<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'customer_id',
        'form_submission_id',
        'assigned_user_id',
        'subject',
        'message',
        'status',
        'secure_token',
        'replies',
    ];

    protected $casts = [
        'replies' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function formSubmission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
