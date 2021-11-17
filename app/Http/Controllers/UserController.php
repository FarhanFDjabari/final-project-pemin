<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;

class UserController extends Controller
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

    // TODO: Create user logic
    public function users()
    {
        try {
            $users = User::all();
            if ($users) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully get all user',
                    'data' => [
                        'users' => $users
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed get all user'
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }
}
