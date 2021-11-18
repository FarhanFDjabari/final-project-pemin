<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
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

    // TODO: Create transaction logic
    public function getTransactions(Request $request)
    {
        try {
            $user = $request->auth;

            $transactions = $user->role == 'admin' ? Transaction::all() : Transaction::where('user_id', $user->id)->get();

            $transactionList = [];
            foreach ($transactions as $t) {
                $book = Book::where('id', $t->book_id)->first();

                $transactionList[] = array(
                    'id' => $t->id,
                    'book' => [
                        'title' => $book->title,
                        'author' => $book->author
                    ],
                    'deadline' => $t->deadline,
                    'created_at' => $t->created_at,
                    'updated_at' => $t->updated_at
                );
            }

            if ($transactions) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully getting list of transactions from database',
                    'data' => ['transactions' => $transactionList]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed getting list of transactions from database'
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function getTransaction(Request $request, $transactionId)
    {

        try {
            $transaction = Transaction::where('id', $transactionId)->first();
            if ($transaction) {
                $book = Book::find($transaction->book_id);
                $user = User::find($transaction->user_id);
                if ($request->auth->role == 'user' && $request->auth->id == $transaction->user_id) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully get transaction',
                        'data' => [
                            'transaction' => [
                                'book' => [
                                    'title' => $book->title,
                                    'author' => $book->author,
                                    'description' => $book->description,
                                    'synopsis' => $book->synopsis,
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at,
                            ]
                        ],
                    ]);
                } else if ($request->auth->role == 'admin') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Sucessfully get transaction',
                        'data' => [
                            'transaction' => [
                                'user' => [
                                    'name' => $user->name,
                                    'email' => $user->email
                                ],
                                'book' => [
                                    'title' => $book->title,
                                    'author' => $book->author,
                                    'description' => $book->description,
                                    'synopsis' => $book->synopsis
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at
                            ],
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden'
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction Not Found'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => true,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }

    public function createTransaction(Request $request)
    {
        if ($request->auth->role == 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $bookId = $request->book_id;

        try {
            $book = Book::where('id', $bookId)->first();
            if ($book) {
                $transaction = new Transaction([
                    'user_id' => $request->auth->id,
                    'book_id' => $bookId,
                    'deadline' => date('Y-m-d H:i:s', time() + 7 * 24 * 60 * 60),
                ]);


                $transaction->save();

                if ($transaction) {
                    $user = User::find($request->auth->id);
                    $book = Book::find($request->book_id);
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully insert new transaction',
                        'data' => [
                            'transaction' => [
                                'id' => $transaction->id,
                                'user' => [
                                    'name' => $user->name,
                                    'email' => $user->email
                                ],
                                'book' => [
                                    'title' => $book->title,
                                    'author' => $book->author
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at,
                            ]
                        ]
                    ], 201);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed insert new transaction'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Book id not found'
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], $e->getCode());
        }
    }

    public function updateTransaction(Request $request)
    {
        if ($request->auth->role == 'admin') {
            try {
                $transaction = Transaction::where('id', $request->transactionId)->first();
                if ($transaction) {
                    $transaction->update(["deadline" => $request->deadline]);
                    $book = Book::where('id', $transaction->book_id)->first();
                    $user = User::where('id', $transaction->user_id)->first();
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully update transaction',
                        'data' => [
                            'transaction' => [
                                'id' => $transaction->id,
                                'user' => [
                                    'name' => $user->name,
                                    'email' => $user->email
                                ],
                                'book' => [
                                    'title' => $book->title,
                                    'author' => $book->author,
                                    'description' => $book->description,
                                    'synopsis' => $book->synopsis,
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at,
                            ]
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Transaction Not Found',
                    ], 404);
                }
            } catch (Error $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server',
                ], $e->getCode());
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }
    }
}
