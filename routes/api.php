<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BooksController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // User profile routes
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/profile/borrowing-history', [UserController::class, 'borrowingHistory']);
    Route::get('/profile/current-borrowings', [UserController::class, 'currentBorrowings']);
    Route::get('/profile/overdue-books', [UserController::class, 'overdueBooks']);
    Route::get('/profile/fine-history', [UserController::class, 'fineHistory']);

    // Books routes
    Route::apiResource('books', BooksController::class);

    // Transactions routes
    Route::apiResource('transactions', TransactionController::class);
    Route::get('/user/transactions', [TransactionController::class, 'userTransactions']);

    // Admin routes
    Route::middleware('admin')->group(function () {
        // Book management
        Route::post('/books', [BooksController::class, 'store']);
        Route::put('/books/{book}', [BooksController::class, 'update']);
        Route::delete('/books/{book}', [BooksController::class, 'destroy']);
        
        // User management
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);
        
        // Transaction management
        Route::get('/admin/transactions', [AdminController::class, 'transactions']);
        
        // Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    });
});

Route::get('/test-connection', function () {
    return response()->json([
        'message' => 'Backend is connected successfully!',
        'timestamp' => now()
    ]);
});