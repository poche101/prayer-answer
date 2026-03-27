<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\PraiseReportController;
use App\Http\Controllers\Api\PrayerReportController;

/*
|--------------------------------------------------------------------------
| Public Routes (Views)
|--------------------------------------------------------------------------
*/

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Using the 'welcome' view for both, but passing a 'type' variable
Route::get('/submit-praise-report', function () {
    return view('welcome', ['type' => 'praise']);
})->name('praise.report.submit');

Route::get('/submit-prayer-report', function () {
    return view('welcome', ['type' => 'prayer']);
})->name('prayer.report.submit');

/*
|--------------------------------------------------------------------------
| Public API Endpoints (Submissions)
|--------------------------------------------------------------------------
| We use 'submit-' in the URL to clearly distinguish these from the
| Admin Management Resource routes.
*/
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/api/submit-praise', [PraiseReportController::class, 'store'])->name('public.praise.store');
    Route::post('/api/submit-prayer', [PrayerReportController::class, 'store'])->name('public.prayer.store');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // The Dashboard View
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout Action
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Internal Dashboard APIs (Admin Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api-v1')->group(function () {
        // These handle GET (index), GET (show), and DELETE (destroy)
        Route::apiResource('prayer-reports', PrayerReportController::class)->except(['store']);
        Route::apiResource('praise-reports', PraiseReportController::class)->except(['store']);

        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });
    });
});
