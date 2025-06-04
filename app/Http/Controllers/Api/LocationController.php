<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $locations = DB::table('wp_mt_locations')->get();
        return response()->json(['success' => true, 'locations' => $locations]);
    }

    public function store(Request $request)
{
    $request->validate([
        'locationname' => 'required|string|max:255',
    ]);

    $locationname = trim($request->input('locationname'));

    // Check if location already exists
    $exists = DB::table('wp_mt_locations')->where('name', $locationname)->exists();
    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'This location already exists.',
        ], 409);
    }

    // Insert the new location
    $id = DB::table('wp_mt_locations')->insertGetId([
        'name' => $locationname,
    ]);

    if ($id) {
        return response()->json([
            'success' => true,
            'location' => [
                'id' => $id,
                'name' => $locationname,
            ]
        ]);
    }
     else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save location.',
        ], 500);
    }
}


}