<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
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

    protected function jwt(User $user)
    {
        $payload = [
            'sub' => $user->email,
            'iss' => "lumen-jwt",
            'aud' => "lumen-jwt",
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'role' => $user->role
        ];
        return JWT::encode($payload, env('JWT_KEY', 'secret'), 'HS256');
    }

    public function register(Request $request)
    {

        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        try {
            $register = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);

            if ($register) {

                $user = User::where('email', $email)->first();
                $jwt = $this->jwt($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Register Success',
                    'data' => [
                        'token' => $jwt
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Register Failed',
                ], 400);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], $e->getCode());
        }
    }

    public function login(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');

        try {

            $user = User::where('email', $email)->first();

            if ($user) {
                if (Hash::check($password, $user->password)) {
                    # Update token user
                    $jwt = $this->jwt($user);

                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully Login',
                        'data' => [
                            'token' => $jwt // kembalikan token bersamaan dengan response
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password doesnt match'
                    ], 400);
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
            ], $e->getCode());
        }
    }
}
