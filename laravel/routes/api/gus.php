<?php

use App\Http\Controllers\Api\GusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GUS API Routes
|--------------------------------------------------------------------------
|
| Routes for integration with Główny Urząd Statystyczny (GUS) API
| for fetching company data by NIP number
|
*/

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/company/{nip}', [GusController::class, 'getCompanyByNip'])
        ->where('nip', '[0-9]{10}')
        ->name('gus.company');
});