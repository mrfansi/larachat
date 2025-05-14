<?php

use App\Livewire\Chat\GroupInvitation;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Chat routes
    Route::view('chat', 'chat.index')
        ->name('chat');

    // Group invitation route
    Route::get('group/invitation/{code?}', GroupInvitation::class)
        ->name('groups.invitation');
});

require __DIR__.'/auth.php';
