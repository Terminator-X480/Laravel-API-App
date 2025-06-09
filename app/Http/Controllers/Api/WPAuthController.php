<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class WPAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $response = Http::post(env('LIVE_URL') . '/madtrek/wp-json/madtrek/v1/validate-login', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        $data = $response->json();
$data = $response->json();

        if ($response->successful() && $data['success']) {
            // Store in session
            session([
                'wp_user_id' => $data['user_id'],
                'wp_username' => $data['username'],
                'wp_role' => $data['role'],
            ]);

        return redirect()->route('leads.dashboard');
        
        }

        return back()->withErrors(['login' => $data['message'] ?? 'Login failed']);
    }
}
