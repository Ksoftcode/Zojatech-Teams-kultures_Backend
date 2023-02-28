<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgetPassword', [AuthController::class, 'forgetPassword']);
Route::post('/passwordReset', [AuthController::class, 'passwordReset']);
Route::post('email/verification-notification', [AuthController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/CodeCheck', [AuthController::class, 'codeCheck']);
Route::post('/PasswordResetOpt', [AuthController::class, 'PasswordResetOpt']);
Route::post('/search', [AuthController::class, 'search']);
Route::post('/upload', [AuthController::class, 'fileupload']);
Route::post('/update/{id}', [AuthController::class, 'update']);
Route::post('/paystack-pay', [AuthController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [AuthController::class, 'handleGatewayCallback']);
Route::post('/filter', [SearchController::class, 'filter']);
