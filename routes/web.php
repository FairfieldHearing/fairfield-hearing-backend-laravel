<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::login')->name('login');

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::livewire('/', 'pages::dashboard');
    Route::livewire('/categories', 'pages::categories.index');
    Route::livewire('/posts', 'pages::posts.index');
    Route::livewire('/faqs', 'pages::faqs.index');
    Route::livewire('/locations', 'pages::locations.index');
    Route::livewire('/policies', 'pages::policies.index');
    Route::livewire('/submissions', 'pages::submissions.index');
    Route::livewire('/leads', 'pages::leads.index');
    Route::livewire('/tickets', 'pages::tickets.index');
    Route::livewire('/staff', 'pages::users.index');
    Route::livewire('/password', 'pages::password');
});
