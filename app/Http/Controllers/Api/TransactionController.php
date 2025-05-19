<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(): JsonResponse
    {
        $transactions = Transaction::with(['user', 'book'])->get();
        return response()->json(['data' => $transactions]);
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'type' => 'required|in:borrow,return',
            'due_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book = Books::findOrFail($request->book_id);

        if ($request->type === 'borrow' && !$book->is_available) {
            return response()->json(['message' => 'Book is not available for borrowing'], 400);
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'type' => $request->type,
            'borrowed_at' => now(),
            'due_date' => $request->due_date,
            'status' => 'active'
        ]);

        if ($request->type === 'borrow') {
            $book->update(['is_available' => false]);
        }

        return response()->json(['data' => $transaction], 201);
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json(['data' => $transaction->load(['user', 'book'])]);
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|in:borrow,return',
            'due_date' => 'sometimes|required|date|after:today',
            'status' => 'sometimes|required|in:pending,active,completed,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('type') && $request->type === 'return') {
            $transaction->returned_at = now();
            $transaction->status = 'completed';
            
            // Calculate fine if returned late
            if (Carbon::parse($transaction->due_date)->isPast()) {
                $daysLate = Carbon::now()->diffInDays($transaction->due_date);
                $transaction->fine_amount = $daysLate * 1.00; // $1 per day late
            }

            $transaction->book->update(['is_available' => true]);
        }

        $transaction->update($request->all());
        return response()->json(['data' => $transaction->load(['user', 'book'])]);
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        if ($transaction->type === 'borrow' && !$transaction->returned_at) {
            $transaction->book->update(['is_available' => true]);
        }
        
        $transaction->delete();
        return response()->json(null, 204);
    }

    /**
     * Get user's active transactions.
     */
    public function userTransactions(): JsonResponse
    {
        $transactions = Transaction::with('book')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();
            
        return response()->json(['data' => $transactions]);
    }
} 