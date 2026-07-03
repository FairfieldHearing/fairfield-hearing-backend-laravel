<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/submissions', [\App\Http\Controllers\Api\FormSubmissionController::class, 'store']);

// Support Tickets endpoints
Route::post('/tickets', [\App\Http\Controllers\Api\TicketController::class, 'store']);
Route::get('/tickets/token/{token}', [\App\Http\Controllers\Api\TicketController::class, 'showByToken']);
Route::post('/customer/request-otp', [\App\Http\Controllers\Api\TicketController::class, 'requestOtp']);
Route::post('/customer/verify-otp', [\App\Http\Controllers\Api\TicketController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customer/tickets', [\App\Http\Controllers\Api\TicketController::class, 'index']);
    Route::post('/customer/tickets/{ticket}/reply', [\App\Http\Controllers\Api\TicketController::class, 'reply']);
});

// Public read-only resources endpoints for Frontend
Route::get('/categories', [\App\Http\Controllers\Api\PublicResourceController::class, 'categories']);
Route::get('/posts', [\App\Http\Controllers\Api\PublicResourceController::class, 'posts']);
Route::get('/posts/{slug}', [\App\Http\Controllers\Api\PublicResourceController::class, 'post']);
Route::get('/faqs', [\App\Http\Controllers\Api\PublicResourceController::class, 'faqs']);
Route::get('/locations', [\App\Http\Controllers\Api\PublicResourceController::class, 'locations']);
Route::get('/locations/{id}', [\App\Http\Controllers\Api\PublicResourceController::class, 'location']);
Route::get('/policies', [\App\Http\Controllers\Api\PublicResourceController::class, 'policies']);
Route::get('/policies/{slug}', [\App\Http\Controllers\Api\PublicResourceController::class, 'policy']);
Route::get('/team', [\App\Http\Controllers\Api\PublicResourceController::class, 'team']);
Route::get('/team/{slug}', [\App\Http\Controllers\Api\PublicResourceController::class, 'teamMember']);
