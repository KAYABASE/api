<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'api'])->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['tenant_validation', \Stancl\Tenancy\Middleware\InitializeTenancyByRequestData::class, 'guest', 'api'])->group(function () {
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedController::class, 'store'])->name('login');
});

Route::middleware(['tenant_validation', \Stancl\Tenancy\Middleware\InitializeTenancyByRequestData::class, 'api'])->group(function () {
    Route::delete('logout', [\App\Http\Controllers\Auth\AuthenticatedController::class, 'destroy'])->name('logout');
});
