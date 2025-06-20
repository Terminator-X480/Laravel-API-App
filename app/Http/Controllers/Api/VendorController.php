<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    // Original: Returns all vendors
    public function index()
    {
        $vendors = DB::table('wp_mt_vendors as v')
            ->leftJoin('wp_mt_locations as l', 'v.location', '=', 'l.id')
            ->select('v.*', 'l.name as location_name')
            ->get();

        return response()->json([
            'success' => true,
            'vendors' => $vendors,
        ]);
    }

    // New: Only B2B vendors
    public function b2bVendors()
    {
        $vendors = DB::table('wp_mt_vendors')
                    ->where('type', 'b2b')
                    ->get();

        return response()->json([
            'success' => true,
            'vendors' => $vendors,
        ]);
    }

    // New: All vendors except B2B
    public function nonB2bVendors()
    {
        $vendors = DB::table('wp_mt_vendors')
                    ->where('type', '!=', 'b2b')
                    ->get();

        return response()->json([
            'success' => true,
            'vendors' => $vendors,
        ]);
    }

    // Existing: Store new vendor
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'location' => 'required|integer',
            'type'     => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            $vendorId = DB::table('wp_mt_vendors')->insertGetId([
                'name'     => $request->input('name'),
                'phone'    => $request->input('phone'),
                'location' => $request->input('location'),
                'type'     => $request->input('type'),
            ]);        

            return response()->json([
                'status'     => 'success',
                'message'    => 'Vendor added successfully.',
                'vendor_id'  => $vendorId,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to insert vendor: ' . $e->getMessage(),
            ], 500);
        }
    }
}