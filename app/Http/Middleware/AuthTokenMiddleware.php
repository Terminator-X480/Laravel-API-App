<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();  // Get token from Authorization header
    
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
        }
    
        $session = DB::table('wp_mt_app_sessions')->where('token', $token)->first();
    
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token'], 401);
        }
    
        // Optional: check expiry date here
        if (strtotime($session->expiry_date) < time()) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        }
    
        // Add user info to request if needed
        $request->merge(['user_id' => $session->user_id]);
    
        return $next($request);
    }
    
}
