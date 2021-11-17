<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */

    public function __construct($role = null)
    {
        //
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $token = $request->header('Authorization');
        if (!$token) {

            return response()->json([
                'success' => false,
                'message' => 'Token not provided.'
            ], 401);
        }

        try {
            $token = explode(' ', $token)[1];
            $credentials = JWT::decode($token, new Key(env('JWT_KEY', 'secret'), 'HS256'));
        } catch (ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provided token is expired.'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        $user = User::where('email', $credentials->sub)->first();
        $request->auth = $user;

        if ($role) {
            $condition = $user->hasRole($role);
            if (!$condition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not Authorized'
                ], 403);
            }
        }
        return $next($request);
    }
}
