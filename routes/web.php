<?php

use App\Livewire\Inquiries\ScenarioBuilder;
use App\Livewire\Quotes\ReviewQuote;
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

Route::get('quotes/{quote}/review', ReviewQuote::class)
    ->middleware(['auth'])
    ->name('quotes.review');

require __DIR__.'/auth.php';
