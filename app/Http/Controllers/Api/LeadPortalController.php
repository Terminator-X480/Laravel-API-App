<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeadPortalController extends Controller
{
    public function showLoginForm()
    {
        return view('leads.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $response = Http::post( env('LIVE_URL') . '/Madtrek/wp-json/madtrek/v1/validate-login', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        $data = $response->json();
$data = $response->json();

        if ($response->successful() && $data['success']) {
            session([
                'leads_user_id' => $data['user_id'],
                'leads_username' => $data['username'],
                'leads_role' => $data['role'],
            ]);

            return redirect()->route('leads.dashboard');
        }

        return back()->withErrors(['login' => $data['message'] ?? 'Login failed']);
    }

    public function dashboard()
    {
        if (!session()->has('leads_user_id')) {
            return redirect()->route('leads.login');
        }

        return view('leads.dashboard', [
            'username' => session('leads_username'),
            'role' => session('leads_role')
        ]);
    }

    public function logout()
    {
        session()->forget(['leads_user_id', 'leads_username', 'leads_role']);
        return redirect()->route('leads.login');
    }

}