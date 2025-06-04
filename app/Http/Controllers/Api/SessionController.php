<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function check(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Authorization token not provided.',
            ], 401);
        }

        $token = substr($authHeader, 7); // Remove 'Bearer '

        // Look for token in database
        $session = DB::table('wp_mt_app_sessions')
            ->where('token', $token)
            ->first();

        if (!$session) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        return response()->json([
            'expiry_date' => $session->expiry_date,
        ], 200);
    }
}
