<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
public function addPayment(Request $request)
{
    $validator = Validator::make($request->all(), [
        'lead_id'        => 'required|integer|exists:wp_mt_leads,id',
        'user_id'        => 'required|integer|exists:wp_users,id',
        'payment_method' => 'required|string|in:cash,account',
        'vendor_id'      => 'required|integer|exists:wp_mt_vendors,id',
        'amount'         => 'required|numeric|min:0',
        'payment_type'   => 'required|string|in:advance,remaining,full,b2b_advance,b2b_remaining,b2b_full',
        'b2b_vendor_id'  => 'nullable|integer|exists:wp_mt_vendors,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Save payment
        $payment = Payment::create([
            'lead_id'        => $request->lead_id,
            'user_id'        => $request->user_id,
            'payment_method' => $request->payment_method,
            'vendor_id'      => $request->vendor_id,
            'amount'         => number_format($request->amount, 2, '.', ''),
            'payment_type'   => $request->payment_type,
            'b2b_vendor_id'  => $request->b2b_vendor_id,
        ]);

        // ✅ Update remaining in wp_mt_leads
        $lead = Lead::find($request->lead_id);

        if ($lead) {
            $totalAmount = floatval($lead->amount ?? 0);
            $totalPaid = Payment::where('lead_id', $lead->id)->sum('amount');
            $remaining = max(0, $totalAmount - $totalPaid);

            $lead->remaining = number_format($remaining, 2, '.', '');
            $lead->save(); // This saves to wp_mt_leads table
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment added and remaining updated successfully',
            'data'    => $payment,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}


}