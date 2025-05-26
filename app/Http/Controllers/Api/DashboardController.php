<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'totalBooks' => Books::count(),
                'totalUsers' => User::count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecentBorrowings(): JsonResponse
    {
        try {
            $recentBorrowings = Transaction::with(['user', 'book'])
                ->latest()
                ->take(5)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $recentBorrowings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch recent borrowings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPopularBooks(): JsonResponse
    {
        try {
            $popularBooks = Books::withCount('transactions')
                ->orderBy('transactions_count', 'desc')
                ->take(5)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $popularBooks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch popular books',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}