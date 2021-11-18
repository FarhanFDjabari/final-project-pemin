<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        try {
            $books = Book::all();

            if ($books) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully getting list of books from database',
                    'data' => ['books' => $books]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed getting list of books from database'
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function insertBook(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'author' => 'required',
            'year' => 'required',
            'synopsis' => 'required',
            'stock' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $author = $request->input('author');
        $year = $request->input('year');
        $synopsis = $request->input('synopsis');
        $stock = $request->input('stock');

        try {
            $book = Book::create([
                'title' => $title,
                'description' => $description,
                'author' => $author,
                'year' => $year,
                'synopsis' => $synopsis,
                'stock' => $stock
            ]);

            if ($book) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully insert new book',
                    'data' => ['book' => $book]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed insert new book',
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], $e->getCode());
        }
    }

    public function getBookById($bookId)
    {

        try {
            $book = Book::find($bookId);

            if ($book) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sucessfully get book detail',
                    'data' => ['book' => $book]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed get book detail'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => true,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }

    public function updateBook(Request $request, $bookId)
    {
        try {
            $book = Book::where('id', $bookId);
            if ($book->exists()) {
                $book->update($request->all());
                return response()->json([
                    'success' => true,
                    'message' => 'Book updated',
                    'data' => ['book' => Book::find($bookId)]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update book'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ]);
        }
    }

    public function deleteBook($bookId)
    {
        try {
            $book = Book::where('id', $bookId);
            if ($book->exists()) {
                $book->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully delete book'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Delete book failed'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }
}
