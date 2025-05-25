<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        // Create some sample transactions
        foreach ($users as $user) {
            // Create 2-3 borrow transactions per user
            $numBorrows = rand(2, 3);
            for ($i = 0; $i < $numBorrows; $i++) {
                $book = $books->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 30));
                $dueDate = $borrowDate->copy()->addDays(14);
                $returnDate = rand(0, 1) ? $dueDate->copy()->addDays(rand(1, 5)) : null;
                $isOverdue = $returnDate ? $returnDate->gt($dueDate) : $dueDate->lt(now());
                $fineAmount = $isOverdue ? rand(1, 10) : 0;

                Transaction::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'type' => 'borrow',
                    'status' => $returnDate ? 'completed' : 'active',
                    'borrow_date' => $borrowDate,
                    'due_date' => $dueDate,
                    'return_date' => $returnDate,
                    'fine_amount' => $fineAmount,
                ]);

                if ($returnDate) {
                    Transaction::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'type' => 'return',
                        'status' => 'completed',
                        'borrow_date' => $borrowDate,
                        'due_date' => $dueDate,
                        'return_date' => $returnDate,
                        'fine_amount' => $fineAmount,
                    ]);
                }
            }
        }
    }
} 