<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'hearing_problem' => ['nullable', 'string', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
            'preferred_day_time' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ]);

        $submission = \App\Models\FormSubmission::create($validated);

        return response()->json([
            'message' => 'Request submitted successfully!',
            'submission' => $submission->load('location'),
        ], 201);
    }
}
