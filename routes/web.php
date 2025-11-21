<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', 'welcome');

Volt::route('welcome', 'welcome')->name('welcome');

Route::middleware([])
    ->prefix('/{country:code}')
    ->group(function () {
        Volt::route('meetups', 'meetups.index')->name('meetups.index');
        Volt::route('map', 'meetups.map')->name('meetups.map');
        Volt::route('meetup/{meetup:slug}', 'meetups.landingpage')->name('meetups.landingpage');
        Volt::route('meetup/{meetup:slug}/event/{event}', 'meetups.landingpage-event')->name('meetups.landingpage-event');
    });

Route::middleware(['auth'])
    ->prefix('/{country:code}')
    ->group(function () {
        Volt::route('dashboard', 'dashboard')->name('dashboard');
        Volt::route('meetup-edit/{meetup}', 'meetups.edit')->name('meetups.edit');
        Volt::route('meetup/{meetup}/events/create', 'meetups.create-edit-events')->name('meetups.events.create');
        Volt::route('meetup/{meetup}/events/{event}/edit', 'meetups.create-edit-events')->name('meetups.events.edit');
    });

Route::middleware(['auth'])
    ->group(function () {
        Route::redirect('settings', 'settings/profile');

        Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
        Volt::route('settings/password', 'settings.password')->name('settings.password');
        Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    });

require __DIR__.'/auth.php';
