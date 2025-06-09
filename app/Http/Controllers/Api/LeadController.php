<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WpMtLead;
use App\Models\WpPost;
use App\Models\WpTerm;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadController extends Controller
{
    public function getProductPrice(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return response()->json(['error' => 'Product ID is required'], 400);
        }

        // Get price from WooCommerce product meta
        $priceMeta = DB::table('wp_postmeta')
            ->where('post_id', $productId)
            ->where('meta_key', '_price')
            ->value('meta_value');

        if (is_null($priceMeta)) {
            return response()->json(['error' => 'Product price not found'], 404);
        }

        // Optional: Get product title too (from wp_posts)
        $productName = DB::table('wp_posts')
            ->where('ID', $productId)
            ->value('post_title');

        return response()->json([
            'price' => $priceMeta,
            'product_name' => $productName
        ]);
    }

    public function getPaymentsByLead(Request $request)
    {
        $leadId = $request->query('lead_id');

        if (!$leadId) {
            return response()->json(['error' => 'Lead ID is required'], 400);
        }

        $payments = DB::table('wp_mt_payments')
            ->where('lead_id', $leadId)
            ->select('amount', 'created_on')
            ->get();

        return response()->json([
            'payments' => $payments
        ]);
    }

    public function getLeadData(Request $request)
    {
        
        $request->validate([
            'phone' => 'required|string',
            'country_code' => 'required|string',
        ]);
        $lead = DB::table('wp_mt_leads')
            ->where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->first();

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        // Now fetch payments for the lead
        $payments = DB::table('wp_mt_payments')
            ->where('lead_id', $lead->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id'            => $lead->id,
                'name'          => $lead->name,
                'email'         => $lead->email,
                'phone'         => $lead->phone,
                'country_code'  => $lead->country_code,
                'type'          => $lead->type,
                'type_id'       => $lead->type_id,
                'trek_date'     => $lead->trek_date,
                'source'        => $lead->source,
                'notes'         => $lead->notes ?? '',
                'no_of_people'  => $lead->no_of_people,
                'created_at'    => $lead->created_at,
                'message'       => $lead->message,
                'payments'      => $payments,
            ]
        ]);

    }
    public function getAllLeads(Request $request): JsonResponse
    {
        if (!session()->has('leads_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = WpMtLead::query();

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        if ($request->filled('trek_date')) {
            $query->where('trek_date', $request->input('trek_date'));
        }

        if ($request->filled('lead_date')) {
            $query->whereDate('created_at', $request->input('lead_date'));
        }

        $leads = $query->orderByDesc('id')->get();

        foreach ($leads as $lead) {
            switch ($lead->type) {
                case 'product':
                case 'mobile':
                    $post = WpPost::where('ID', $lead->type_id)->where('post_type', 'product')->first();
                    $lead->trek_name = $post?->post_title ?? 'Unknown Product';
                    break;

                case 'page':
                    $post = WpPost::where('ID', $lead->type_id)->where('post_type', 'page')->first();
                    $lead->trek_name = $post?->post_title ?? 'Unknown Page';
                    break;

                case 'taxonomy':
                    $term = WpTerm::find($lead->type_id);
                    $lead->trek_name = $term?->name ?? 'Unknown Taxonomy';
                    break;

                default:
                    $lead->trek_name = 'Unknown Type';
            }
        }

        return response()->json($leads);
    }

    public function get($id): JsonResponse
    {
        $lead = WpMtLead::findOrFail($id);
        return response()->json($lead);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $lead = WpMtLead::findOrFail($id);

        $data = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'country_code' => 'nullable|string',
            'no_of_people' => 'nullable|integer',
            'type_id' => 'nullable|integer',
            'source' => 'nullable|string',
            'trek_date' => 'nullable|date',
            'created_at' => 'nullable|date',
            'message' => 'nullable|string',
        ]);

        unset($data['created_at']);

        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
        ]);
    }

    public function book(Request $request, $id): JsonResponse
    {
        $leadId = (int) $id;

        $validated = $request->validate([
            'vendor_id' => 'required|integer|exists:wp_mt_vendors,id',
            'amount' => 'nullable|numeric|min:0',
            'created_on' => 'nullable|date',
        ]);

        $vendorId = $validated['vendor_id'];
        $amount = $validated['amount'] ?? 0;
        $createdOn = isset($validated['created_on'])
            ? Carbon::parse($validated['created_on'])->timezone('UTC')->format('Y-m-d H:i:s')
            : now('UTC')->format('Y-m-d H:i:s');

        $booking = DB::table('wp_mt_bookings')->where('lead_id', $leadId)->first();
        $wasUncancelled = false;

        if ($booking) {
            if ($booking->is_cancel) {
                DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update(['is_cancel' => 0]);
                $wasUncancelled = true;
            }

            if (!$booking->is_book) {
                DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update([
                    'is_book' => 1,
                    'vendor_id' => $vendorId,
                    'created_at' => $createdOn,
                ]);
            }
        } else {
            DB::table('wp_mt_bookings')->insert([
                'lead_id' => $leadId,
                'vendor_id' => $vendorId,
                'is_book' => 1,
                'created_at' => $createdOn,
            ]);
        }

        $paymentId = null;

        if ($amount > 0) {
            $paymentId = DB::table('wp_mt_payments')->insertGetId([
                'lead_id' => $leadId,
                'vendor_id' => $vendorId,
                'amount' => $amount,
                'user_id' => auth()->id() ?? 0,
                'created_on' => $createdOn,
            ]);
        }

        return response()->json([
            'success' => true,
            'was_uncancelled' => $wasUncancelled,
            'payment_id' => $paymentId,
        ]);
    }

    public function cancel($id): JsonResponse
    {
        $leadId = (int) $id;
        $booking = DB::table('wp_mt_bookings')->where('lead_id', $leadId)->first();
        $wasUnbooked = false;

        if ($booking) {
            DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update(['is_cancel' => 1]);

            if ($booking->is_book) {
                DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update(['is_book' => 0]);
                $wasUnbooked = true;
            }
        } else {
            DB::table('wp_mt_bookings')->insert([
                'lead_id' => $leadId,
                'is_cancel' => 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'was_unbooked' => $wasUnbooked,
        ]);
    }

    public function unbook($id, Request $request): JsonResponse
    {
        $leadId = (int) $id;

        $result = DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update(['is_book' => 0]);

        if ($result !== false) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => 'DB update failed']);
    }

    public function uncancel($id): JsonResponse
    {
        $leadId = (int) $id;

        $result = DB::table('wp_mt_bookings')->where('lead_id', $leadId)->update(['is_cancel' => 0]);

        if ($result !== false) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => 'DB update failed']);
    }
}
