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
     * Borrow or Return a Book.
     */
    public function handleTransaction(Request $request, Books $book): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:borrow,return',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->type === 'borrow') {
            return $this->borrowBook($book);
        } else {
            return $this->returnBook($book);
        }
    }

    /**
     * Borrow a Book.
     */
    private function borrowBook(Books $book): JsonResponse
    {
        if (!$book->is_available) {
            return response()->json(['message' => 'Book is not available for borrowing'], 400);
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'active'
        ]);

        $book->update(['is_available' => false]);

        return response()->json(['message' => 'Book borrowed successfully', 'transaction' => $transaction]);
    }

    /**
     * Return a Book.
     */
    private function returnBook(Books $book): JsonResponse
    {
        $transaction = Transaction::where('user_id', Auth::id())
                                  ->where('book_id', $book->id)
                                  ->where('status', 'active')
                                  ->first();

        if (!$transaction) {
            return response()->json(['message' => 'You have not borrowed this book'], 400);
        }

        // Check if overdue and calculate fine
        $fine = 0;
        if (now()->greaterThan($transaction->due_date)) {
            $daysLate = now()->diffInDays($transaction->due_date);
            $fine = $daysLate * 1.00; // Fine rate: $1 per day
        }

        $transaction->update([
            'status' => 'completed',
            'return_date' => now(),
            'fine_amount' => $fine
        ]);

        $book->update(['is_available' => true]);

        return response()->json(['message' => 'Book returned successfully', 'fine' => $fine]);
    }
}