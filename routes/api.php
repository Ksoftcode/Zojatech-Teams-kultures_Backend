<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SearchController;
use App\Http\Controllers\FileUploadController;


use App\Http\Controllers\PasswordResetController;



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
Route::post('/passwordReset', [ AuthController::class,'passwordReset']);  
Route::post('/CodeCheck', [AuthController::class, 'codeCheck']); 
Route::post('/PasswordResetOpt', [AuthController::class, 'PasswordResetOpt']);
Route::post('/search', [SearchController::class, 'search']); 
Route::post('/upload', [FileUploadController::class, 'fileupload']); 



 





