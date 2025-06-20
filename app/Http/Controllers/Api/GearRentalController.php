<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GearRental;
use Illuminate\Support\Facades\Validator;

class GearRentalController extends Controller
{

    public function index()
    {
        $gears = DB::table('wp_mt_gear_rentals as rentals')
            ->leftJoin('wp_users as users', 'users.id', '=', 'rentals.user_id')
            ->leftJoin('wp_mt_vendors as vendors', 'vendors.id', '=', 'rentals.vendor_id')
            ->leftJoin('wp_mt_locations as locations', 'locations.id', '=', 'rentals.location')
            ->leftJoin('wp_mt_equipments as equipments', 'equipments.id', '=', 'rentals.equipment')
            ->select(
                'rentals.*',
                'users.display_name as user_name',
                'vendors.name as vendor_name',
                'locations.name as location_name',
                'equipments.name as equipment_name'
            )
            ->get();

        return response()->json([
            'success' => true,
            'gears' => $gears
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Received rental request:', $request->all());

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric',
            'vendor_id' => 'nullable|integer',
            'location' => 'nullable|integer',
            'equipment' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'payment_type' => 'nullable|in:cash,account',
            'payment' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $rental = GearRental::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Rental saved successfully.',
            'data' => $rental
        ], 201);
    }
}