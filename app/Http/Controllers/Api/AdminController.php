<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Books;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get all users.
     */
    public function users(): JsonResponse
    {
        $users = User::with('transaction')->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Create a new user.
     */
    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['data' => $user], 201);
    }

    /**
     * Update a user.
     */
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['name', 'email', 'role']));
        return response()->json(['data' => $user]);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get all transactions.
     */
    public function transactions(): JsonResponse
    {
        $transactions = Transaction::with(['user', 'book'])->get();
        return response()->json(['data' => $transactions]);
    }

    /**
     * Get dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_books' => Books::count(),
            'total_transactions' => Transaction::count(),
            'active_borrowings' => Transaction::where('type', 'borrow')
                ->where('status', 'active')
                ->count(),
            'overdue_books' => Transaction::where('type', 'borrow')
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->count(),
            'total_fines' => Transaction::where('fine_amount', '>', 0)
                ->sum('fine_amount'),
        ];

        return response()->json(['data' => $stats]);
    }
} 