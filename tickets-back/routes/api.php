<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Middleware;


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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('throttle');
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('throttle');
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum', 'checkAdmin']], function () {
    Route::get('index', [UserController::class, 'index'])->withoutMiddleware('throttle');
    Route::post('store', [UserController::class, 'store'])->withoutMiddleware('throttle');
    Route::get('show/{user}', [UserController::class, 'show'])->withoutMiddleware('throttle');
    Route::put('update/{user}', [UserController::class, 'update'])->withoutMiddleware('throttle');
    Route::delete('destroy/{id}', [UserController::class, 'destroy'])->withoutMiddleware('throttle');
});

Route::group(['prefix' => 'status', 'middleware' => ['auth:sanctum', 'checkAdmin']], function () {
    Route::post('store', [StatusController::class, 'store'])->withoutMiddleware('throttle');
    Route::put('update/{book}', [StatusController::class, 'update'])->withoutMiddleware('throttle');
    Route::delete('destroy/{book}', [StatusController::class, 'destroy'])->withoutMiddleware('throttle');
    Route::get('index', [StatusController::class, 'index'])->withoutMiddleware('throttle');
    Route::get('show/{book}', [StatusController::class, 'show'])->withoutMiddleware('throttle');
});

Route::group(['prefix' => 'tickets'], function () {
    Route::get('index/{id?}', [TicketController::class, 'index'])->middleware('auth:sanctum')->withoutMiddleware('throttle');
    Route::post('store', [TicketController::class, 'store'])->middleware('auth:sanctum')->withoutMiddleware('throttle');
    Route::get('show/{id}', [TicketController::class, 'show'])->withoutMiddleware('throttle');
    Route::put('update/{tikect}', [TicketController::class, 'update'])->middleware('auth:sanctum')->withoutMiddleware('throttle');
    Route::delete('destroy/{tikect}', [TicketController::class, 'destroy'])->middleware('auth:sanctum')->withoutMiddleware('throttle');
});
