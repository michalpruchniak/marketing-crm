<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::resource('clients', ClientController::class)->only([
        'index',
        'create',
        'store',
        'show',
        'update',
        'destroy',
    ]);

    Route::post('clients/{client}/credentials', [ClientCredentialController::class, 'store'])
        ->name('clients.credentials.store');
    Route::get('clients/{client}/credentials/{credential}/reveal', [ClientCredentialController::class, 'reveal'])
        ->name('clients.credentials.reveal');
    Route::delete('clients/{client}/credentials/{credential}', [ClientCredentialController::class, 'destroy'])
        ->name('clients.credentials.destroy');

    Route::resource('users', UserController::class)->except(['show', 'edit']);
    Route::patch('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::delete('users/{user}/ban', [UserController::class, 'unban'])->name('users.unban');
});

require __DIR__.'/settings.php';
