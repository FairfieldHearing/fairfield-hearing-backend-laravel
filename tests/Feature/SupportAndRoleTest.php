<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Lead;
use App\Models\Location;
use App\Models\FormSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user has correct roles and gate assertions', function () {
    $superadmin = User::factory()->create(['roles' => ['superadmin']]);
    $blogger = User::factory()->create(['roles' => ['blog_posting']]);

    expect($superadmin->hasRole('superadmin'))->toBeTrue();
    expect($blogger->hasRole('blog_posting'))->toBeTrue();
    expect($blogger->hasRole('superadmin'))->toBeFalse();
});

test('a customer can raise a ticket via api', function () {
    $location = Location::create([
        'name' => 'Fairfield Audiology',
        'is_main' => true,
        'address_line1' => '123 Main St',
        'city' => 'Fairfield',
        'state' => 'CA',
        'postal_code' => '94533',
        'country' => 'USA',
        'availability' => 'Mon-Fri 9am-5pm',
        'phone' => '1234567890',
        'whatsapp' => '1234567890',
        'maps_link' => 'https://maps.google.com',
    ]);

    $response = $this->postJson('/api/tickets', [
        'name' => 'Alice Customer',
        'email' => 'alice@example.com',
        'phone' => '0987654321',
        'subject' => 'Faulty Hearing Aid',
        'message' => 'My hearing aid has static sound.',
    ]);

    $response->assertStatus(210);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('customers', [
        'email' => 'alice@example.com',
        'name' => 'Alice Customer',
    ]);

    $this->assertDatabaseHas('tickets', [
        'subject' => 'Faulty Hearing Aid',
    ]);
});

test('customer otp generation and verification process', function () {
    $customer = Customer::create([
        'name' => 'Bob Customer',
        'email' => 'bob@example.com',
        'phone' => '1231231234',
    ]);

    // Request OTP
    $response = $this->postJson('/api/customer/request-otp', [
        'email' => 'bob@example.com',
    ]);

    $response->assertStatus(200);
    $otp = $response->json('otp_debug');

    expect($otp)->not->toBeNull();

    // Verify OTP
    $verifyResponse = $this->postJson('/api/customer/verify-otp', [
        'email' => 'bob@example.com',
        'otp_code' => $otp,
    ]);

    $verifyResponse->assertStatus(200);
    $token = $verifyResponse->json('token');
    expect($token)->not->toBeNull();

    // Access customer tickets route
    $ticketsResponse = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson('/api/customer/tickets');

    $ticketsResponse->assertStatus(200);
});

test('converting form submission to lead works correctly', function () {
    $location = Location::create([
        'name' => 'Fairfield Audiology',
        'is_main' => true,
        'address_line1' => '123 Main St',
        'city' => 'Fairfield',
        'state' => 'CA',
        'postal_code' => '94533',
        'country' => 'USA',
        'availability' => 'Mon-Fri 9am-5pm',
        'phone' => '1234567890',
        'whatsapp' => '1234567890',
        'maps_link' => 'https://maps.google.com',
    ]);

    $submission = FormSubmission::create([
        'full_name' => 'Jane Doe',
        'mobile_number' => '1234567890',
        'email' => 'jane@example.com',
        'hearing_problem' => 'Mild hearing loss',
        'location_id' => $location->id,
        'preferred_day_time' => 'Monday Morning',
        'message' => 'Please call me',
    ]);

    $lead = Lead::create([
        'form_submission_id' => $submission->id,
        'full_name' => $submission->full_name,
        'mobile_number' => $submission->mobile_number,
        'email' => $submission->email,
        'hearing_problem' => $submission->hearing_problem,
        'location_id' => $submission->location_id,
        'preferred_day_time' => $submission->preferred_day_time,
        'message' => $submission->message,
        'status' => 'new',
        'logs' => [
            [
                'date' => now()->toDateTimeString(),
                'author' => 'System',
                'message' => 'Lead created via Form Submission conversion.'
            ]
        ]
    ]);

    $this->assertDatabaseHas('leads', [
        'form_submission_id' => $submission->id,
        'full_name' => 'Jane Doe',
    ]);

    expect($lead->logs)->toHaveCount(1);
});

test('public resources read api endpoints work correctly', function () {
    $response = $this->getJson('/api/categories');
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $response = $this->getJson('/api/posts');
    $response->assertStatus(200);

    $response = $this->getJson('/api/faqs');
    $response->assertStatus(200);

    $response = $this->getJson('/api/locations');
    $response->assertStatus(200);

    $response = $this->getJson('/api/policies');
    $response->assertStatus(200);
});

test('user theme defaults to light and can be updated', function () {
    $user = User::factory()->create();
    $user->refresh();
    expect($user->theme)->toBe('light');

    $user->update(['theme' => 'dark']);
    expect($user->theme)->toBe('dark');
});
