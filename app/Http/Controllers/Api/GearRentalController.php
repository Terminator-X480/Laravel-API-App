<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GearRental;
use Illuminate\Support\Facades\Validator;

class GearRentalController extends Controller
{
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