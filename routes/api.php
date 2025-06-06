<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BooksController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Dashboard stats (public)
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/dashboard/recent-borrowings', [DashboardController::class, 'getRecentBorrowings']);
Route::get('/dashboard/popular-books', [DashboardController::class, 'getPopularBooks']);

//books
Route::get('/books', [BooksController::class, 'index']);
Route::apiResource('books', BooksController::class);

//users
Route::get('/user', [AuthController::class, 'user']);
Route::get('/users', function () {
    return response()->json([
        'data' => \App\Models\User::all()
    ]);
});
// Add full CRUD for users
Route::apiResource('users', UserController::class);

// Test connection
Route::get('/test-connection', function () {
    return response()->json([
        'message' => 'Backend is connected successfully!',
        'timestamp' => now()
    ]);
});

// Protected routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    //Route::get('/user', [AuthController::class, 'user']);

    // User profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/profile/borrowing-history', [UserController::class, 'borrowingHistory']);
    Route::get('/profile/current-borrowings', [UserController::class, 'currentBorrowings']);
    Route::get('/profile/overdue-books', [UserController::class, 'overdueBooks']);
    Route::get('/profile/fine-history', [UserController::class, 'fineHistory']);

    // Book CRUD

    // Route::apiResource('books', BooksController::class)->except(['index']);

    // Transactions CRUD
    Route::apiResource('transactions', TransactionController::class);
    Route::get('/user/transactions', [TransactionController::class, 'userTransactions']);
});
