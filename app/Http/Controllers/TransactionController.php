<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Error;
use Illuminate\Http\Request;

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

            $transactions = $user->role == 'admin' ? Transaction::all() : Transaction::where('user_id', $user->id)->get;

            if ($transactions) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully getting list of transactions from database',
                    'data' => ['transactions' => $transactions]
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

    public function getTransaction($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if ($transaction) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sucessfully get transaction',
                    'data' => ['transaction' => $transaction]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed get transaction'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => true,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }
}
