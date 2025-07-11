<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
    $payments = Payment::with(['user', 'vendor', 'lead', 'b2b_vendor'])->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
        ]);

    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function b2b_vendor()
{
    return $this->belongsTo(Vendor::class, 'b2b_vendor_id');
}


    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

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

        // Store payment with type
        $payment = Payment::create([
            'lead_id'        => $request->lead_id,
            'user_id'        => $request->user_id,
            'payment_method' => $request->payment_method,
            'vendor_id'      => $request->vendor_id,
            'amount'         => number_format($request->amount, 2, '.', ''),
            'remaining'      => number_format($request->remaining, 2, '.', ''),
            'payment_type'   => $request->payment_type,
            'b2b_vendor_id'  => $request->b2b_vendor_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully',
            'data'    => $payment,
        ]);

    }

public function getPayments($id, Request $request){
    $lead_id = (int) $id;
    if(!$lead_id){
        return response()->json([
            'success' => false,
            'html' => '<tr>invalid lead id</tr>'
        ]);
    }

    $payments = DB::table('wp_mt_payments as payments')
        ->leftJoin('wp_mt_vendors as vendors', 'vendors.id', '=', 'payments.vendor_id')
        ->leftJoin('wp_mt_vendors as b2b_vendors', 'b2b_vendors.id', '=', 'payments.b2b_vendor_id')
        ->leftJoin('wp_users as wp_users', 'wp_users.id', '=', 'payments.user_id')
        ->where('payments.lead_id', $id)
        ->orderByDesc('payments.id')
        ->select(
            'payments.amount',
            'payments.created_on',
            'vendors.name as vendor_name',
            'b2b_vendors.name as b2b_vendor_name',
            'wp_users.display_name as user_name'
        )
        ->get();

    $html = '';

    if ($payments->count()) {
        foreach ($payments as $payment) {
            $html .= '<tr>';
            $html .= '<td>₹' . e($payment->amount) . '</td>';
            $html .= '<td>' . e($payment->vendor_name ?? 'N/A') . '</td>';
            $html .= '<td>' . e($payment->b2b_vendor_name ?? 'N/A') . '</td>';
            $html .= '<td>' . e($payment->created_on) . '</td>';
            $html .= '<td>' . e($payment->user_name) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = "<tr><td colspan='5'>No Payment Found</td></tr>";
    }

    return response()->json([
        'success' => true,
        'html' => $html
    ]);
}
}