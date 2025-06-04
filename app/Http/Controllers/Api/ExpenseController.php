<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor'       => 'required|numeric|min:0.01',
            'amount'       => 'required|numeric|min:0.01',
            'payment_type' => 'required|string|max:255',
            'user_id'      => 'required|integer',
            'location'     => 'nullable|integer',
            'trek'         => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = [
            'trek'        => $request->input('trek', null),
            'vendor'      => $request->input('vendor'),
            'amount'      => $request->input('amount'),
            'payment_type'=> $request->input('payment_type'),
            'user_id'     => $request->input('user_id'),
            'location'    => $request->input('location'),
            'created_at'  => now(),
        ];

        try {
            DB::table('wp_mt_expense')->insert($data);

            return response()->json([
                'message' => 'Expense saved successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Expense save failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to save expense',
            ], 500);
        }
    }
}
