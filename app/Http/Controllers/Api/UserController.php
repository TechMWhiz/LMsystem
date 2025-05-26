<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user()->load('transactions.book');
        return response()->json(['data' => $user]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify current password if changing password
        if ($request->has('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Update other fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json(['data' => $user]);
    }

    /**
     * Get user's borrowing history.
     */
    public function borrowingHistory(): JsonResponse
    {
        $user = Auth::user();
        $history = $user->transactions()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $history]);
    }

    /**
     * Get user's current borrowed books.
     */
    public function currentBorrowings(): JsonResponse
    {
        $user = Auth::user();
        $borrowings = $user->transactions()
            ->with('book')
            ->where('type', 'borrow')
            ->where('status', 'active')
            ->get();

        return response()->json(['data' => $borrowings]);
    }

    /**
     * Get user's overdue books.
     */
    public function overdueBooks(): JsonResponse
    {
        $user = Auth::user();
        $overdue = $user->transactions()
            ->with('book')
            ->where('type', 'borrow')
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->get();

        return response()->json(['data' => $overdue]);
    }

    /**
     * Get user's fine history.
     */
    public function fineHistory(): JsonResponse
    {
        $user = Auth::user();
        $fines = $user->transactions()
            ->with('book')
            ->where('fine_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $fines]);
    }

    /**
     * Display a listing of the users.
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'sometimes|required|string|min:8',
                'role' => 'sometimes|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->has('password')) {
                $userData['password'] = Hash::make($request->password);
            } else {
                // Generate a random password if none provided
                $userData['password'] = Hash::make(Str::random(10));
            }

            if ($request->has('role')) {
                $userData['role'] = $request->role;
            }

            $user = User::create($userData);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['data' => $user]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
} 