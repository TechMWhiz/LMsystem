<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function getStats()
    {
        try {
            $stats = [
                'totalBooks' => Books::count(),
                'totalUsers' => User::count(),
                'activeLoans' => Borrowing::where('status', 'active')->count()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}