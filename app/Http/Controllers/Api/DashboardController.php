<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function getStats()
    {
        try {
            $stats = [
                'totalBooks' => Books::count(),
                'totalUsers' => User::count(),
                'booksBorrowed' => Transaction::where('status', 'active')->count(),
                'dueReturns' => Transaction::where('due_date', '<=', Carbon::today())
                                           ->where('status', 'active')->count()
            ];
    
            return response()->json($stats, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }    
}