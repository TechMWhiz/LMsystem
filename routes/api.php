<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BooksController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Books routes
Route::apiResource('books', BooksController::class);

//Route::middleware('auth:sanctum')->group(function () {


    //Route::apiResource('books', BooksController::class);

//});