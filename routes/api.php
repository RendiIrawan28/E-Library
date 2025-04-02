<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
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

// Route::get('/books', [BookController::class, 'index']);
// Route::delete('/books/{id}', [BookController::class, 'destroy']);
// Route::get('/books/{id}', [BookController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
