<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Books;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index(): JsonResponse
    {
        $books = Books::all();
        return response()->json(['data' => $books]);
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books',
            'published_date' => 'required|date',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $books = Books::create($request->all());
        return response()->json(['data' => $books], 201);
    }

    /**
     * Display the specified book.
     */
   public function show($id)
{
    $books = Books::find($id);

    if (!$books) {
        return response()->json(['message' => 'Book not found'], 404);
    }

    return response()->json($books);
}

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Books $book): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|unique:books,isbn,' . $book->id,
            'published_date' => 'sometimes|required|date',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book->update($request->all());
        return response()->json(['data' => $book]);
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Books $book): JsonResponse
    {
        $book->delete();
        return response()->json(null, 204);
    }
}
