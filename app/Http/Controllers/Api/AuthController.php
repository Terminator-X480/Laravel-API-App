<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        \Log::info('Sending login request to WordPress', ['username' => $username]);

        // Replace with your actual WP URL
        $wpUrl = env('LIVE_URL') . '/madtrek/wp-json/madtrek/v1/validate-login';

        try {
            $response = \Http::post($wpUrl, [
                'username' => $username,
                'password' => $password,
            ]);

            $data = $response->json();
            \Log::info('Login Response:', ['response' => $data]);

            if (!$data['success']) {
                return response()->json(['message' => $data['message'] ?? 'Invalid credentials'], 401);
            }

            // Create Laravel token/session
            $token = Str::random(32);
            $expiryDate = now()->addMonths(6)->format('Y-m-d H:i:s');

            DB::table('wp_mt_app_sessions')->updateOrInsert(
                ['user_id' => $data['user_id']],
                [
                    'username' => $data['username'],
                    'token' => $token,
                    'expiry_date' => $expiryDate,
                ]
            );

            return response()->json([
                'user_id' => $data['user_id'],
                'username' => $data['username'],
                'token' => $token,
                'expiry_date' => $expiryDate,
                'role' => $data['role'] ?? '',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Login Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Login failed. Try again later.'], 500);
        }
    }

    private function isSerialized($data)
    {
        return $data === 'b:0;' || @unserialize($data) !== false;
    }
}
