<?php
// header("Access-Control-Allow-Origin:localhost:3000");

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\VerifyUserEmail;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------
| API Routes
|--------------------------------------------------------------

/**   Email Verification */
Route::group(
    [
        'prefix' => '/verify',
        'middleware' => ['guest']
    ],
    function () {
        Route::get('/{userId}', [VerifyUserEmail::class, 'verifyMail'])->name('verification.notice');
        Route::get('/verified', function () {
            return view('verified')->name('verified');
        });
    }
);

/**  Guest Route  */
Route::group(['middleware' => ['guest']], function () {
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

/** Logout */
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.sanctum');

/** Listings */
Route::apiResource('listing', ListingsController::class)->middleware([
    'auth:sanctum'
]);
