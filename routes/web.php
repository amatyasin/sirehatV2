<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReferralApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([\Filament\Http\Middleware\Authenticate::class])->group(function () {
    Route::get('/admin/rujukan', [ReferralController::class, 'index'])->name('referrals.index');
    
    Route::prefix('api')->group(function () {
        Route::get('/referrals', [ReferralApiController::class, 'index']);
        Route::get('/referrals/recap-school', [ReferralApiController::class, 'recapSchool']);
        Route::get('/referrals/recap-class', [ReferralApiController::class, 'recapClass']);
        Route::get('/referrals/dashboard', [ReferralApiController::class, 'dashboard']);
        Route::get('/referrals/export', [ReferralApiController::class, 'export']);
        Route::get('/referrals/options', [ReferralApiController::class, 'options']);
        Route::get('/referrals/{id}', [ReferralApiController::class, 'show']);
        Route::put('/referrals/{id}/status', [ReferralApiController::class, 'updateStatus']);
    });
});
