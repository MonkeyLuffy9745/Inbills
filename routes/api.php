<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\AuthToken;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', function (Request $request) {
    return response()->json(['user' => $request->user()]);
})->middleware(AuthToken::class);
Route::middleware(AuthToken::class)->post('/logout', [AuthController::class, 'logout']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::middleware(AuthToken::class)->post('/send-mail', [MessageController::class, 'sendToClient']);

Route::middleware(AuthToken::class)->get('/clients', [ClientController::class, 'index']);
Route::middleware(AuthToken::class)->get('/clients/{id}', [ClientController::class, 'show']);
Route::middleware(AuthToken::class)->post('/clients', [ClientController::class, 'store']);
Route::middleware(AuthToken::class)->put('/clients/{id}', [ClientController::class, 'update']);
Route::middleware(AuthToken::class)->delete('/clients/{id}', [ClientController::class, 'destroy']);

Route::middleware(AuthToken::class)->get('/invoices', [InvoiceController::class, 'index']);
Route::middleware(AuthToken::class)->get('/invoices/{id}', [InvoiceController::class, 'show']);
Route::middleware(AuthToken::class)->post('/invoices', [InvoiceController::class, 'store']);
Route::middleware(AuthToken::class)->put('/invoices/{id}', [InvoiceController::class, 'update']);
Route::middleware(AuthToken::class)->delete('/invoices/{id}', [InvoiceController::class, 'destroy']);

Route::middleware(AuthToken::class)->get('/invoice-items', [InvoiceItemController::class, 'index']);
Route::middleware(AuthToken::class)->get('/invoice-items/{id}', [InvoiceItemController::class, 'show']);
Route::middleware(AuthToken::class)->post('/invoice-items', [InvoiceItemController::class, 'store']);
Route::middleware(AuthToken::class)->put('/invoice-items/{id}', [InvoiceItemController::class, 'update']);
Route::middleware(AuthToken::class)->delete('/invoice-items/{id}', [InvoiceItemController::class, 'destroy']);
