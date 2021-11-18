<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Illuminate\Http\Request;

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
                ], 200);
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

    public function getUserById(Request $request, $userId)
    {
        try {
            $userRequest = $request->auth;

            $user = $userRequest->role == 'admin' ? User::find($userId)
                : User::find($userRequest->id);

            if ($user) {
                if ($user->role != 'user') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully get user',
                        'data' => ['user' => $user]
                    ], 200);
                } else if ($user->id != $userId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden'
                    ], 403);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully get user',
                        'data' => ['user' => $user]
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function updateUser(Request $request, $userId)
    {
        try {
            $user = User::where('id', $userId)->first();
            if ($user) {
                if ($request->auth->id == $userId) {
                    $user->update($request->all());
                    $user->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'User updated',
                        'data' => ['user' => $user]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden',
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user'
                ], 404);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], $e->getCode());
        }
    }

    public function deleteUser(Request $request, $userId)
    {
        try {
            $user = User::where('id', $userId);
            if ($user->exists()) {
                if ($request->auth->id == $userId) {
                    $user->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully delete user'
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
                    'message' => 'Delete user failed'
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
