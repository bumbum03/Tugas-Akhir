<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VillaController;
use App\Http\Controllers\villaFltsController;
use App\Http\Controllers\villaImageController;
use App\Http\Controllers\bookingController;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('reset-pass', [AuthController::class, 'resetPass'])->withoutMiddleware('auth');
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
                
            });
            Route::get('villa', [VillaController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('villa', [VillaController::class, 'index']);
            Route::post('villa/store', [VillaController::class, 'store']);
            Route::post('villa/city', [VillaController::class, 'showByCity'])->withoutMiddleware(['auth', 'verified']);
            Route::post('villa/{uuid}/toggle-status', [VillaController::class, 'toggleStatus'])->withoutMiddleware(['auth', 'verified']);
            Route::apiResource('villa', VillaController::class)->except(['index', "store"])->withoutMiddleware(['auth', 'verified']);

            Route::get('villaFasilitas', [villaFltsController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('villaFasilitas', [villaFltsController::class, 'index']);
            Route::post('villaFasilitas/store', [villaFltsController::class, 'store']);
            Route::apiResource('villaFasilitas', villaFltsController::class)->except(['index', "store"]);

            Route::get('villa_image', [villaImageController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('villa_image', [villaImageController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::post('villa_image/store', [villaImageController::class, 'store']);
            Route::apiResource('villa_image', villaImageController::class)->except(['index', "store"]);

            Route::get('booking_room', [bookingController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::get('booking_room/{uuid}', [bookingController::class, 'show'])->withoutMiddleware(['auth', 'verified']);
            Route::post('booking_room', [bookingController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::post('booking_room/store', [bookingController::class, 'store']);
            Route::get('booking_room/update-status/{uuid}', [bookingController::class, 'updateStatus'])->withoutMiddleware(['auth', 'verified']);
            Route::get('booking_room/status/{uuid}', [bookingController::class, 'status'])->withoutMiddleware(['auth', 'verified']);
            Route::post('booking/history', [bookingController::class, 'history'])->withoutMiddleware(['auth', 'verified']);
            Route::post('booking_room/{uuid}/change-status', [bookingController::class, 'changeStatus'])->withoutMiddleware(['auth', 'verified']);
            Route::apiResource('booking_room', bookingController::class)
                ->except(['index', 'store'])->withoutMiddleware(['auth', 'verified']);
    });
});
