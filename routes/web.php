<?php

use App\Livewire\Inquiries\ScenarioBuilder;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('inquiries/{inquiry}/scenario-builder', ScenarioBuilder::class)
    ->middleware(['auth'])
    ->name('inquiries.scenario-builder');

require __DIR__.'/auth.php';
