<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Books extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_date',
        'description',
        'is_available',
        'price',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_available' => 'boolean',
        'price' => 'decimal:2'
    ];
    /**
     * Get the transactions for the book.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'book_id');
    }
}