<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Books;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_BORROWED = 'borrowed';
    const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'user_id',
        'book_id',
        'type',
        'status',
        'borrow_date',
        'due_date',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    /**
     * User who borrowed the book.
     */

    /**
     * Book that was borrowed.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Books::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Calculate fine if overdue ($5 per day).
     */
    public function calculateFine(): float
    {
        if ($this->status === self::STATUS_BORROWED && now()->greaterThan($this->due_date)) {
            $daysLate = now()->diffInDays($this->due_date);
            return $daysLate * 5;
        }

        return 0;
    }
}