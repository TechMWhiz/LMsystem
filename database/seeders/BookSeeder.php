<?php

namespace Database\Seeders;

use App\Models\Books;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some predefined books
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '978-0743273565',
                'published_date' => '1925-04-10',
                'description' => 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.',
                'available_copies' => 5,
                'price' => 19.99,
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '978-0446310789',
                'published_date' => '1960-07-11',
                'description' => 'The story of racial injustice and the loss of innocence in the American South.',
                'available_copies' => 3,
                'price' => 15.99,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '978-0451524935',
                'published_date' => '1949-06-08',
                'description' => 'A dystopian social science fiction novel and cautionary tale.',
                'available_copies' => 4,
                'price' => 12.99,
            ],
        ];

        foreach ($books as $book) {
            Books::create($book);
        }

        // Create additional random books
        Books::factory(10)->create();
    }
} 