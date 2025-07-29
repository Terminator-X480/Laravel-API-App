<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

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
                $html .= '<td>' . e(TimezoneHelper::UtcToIst($payment->created_on)) . '</td>';
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

    function getAllPayments(Request $request)
    {
        $search_filter   = $request->input('search_payment', '');
        $trek_date        = $request->input('trek_date', '');
        $payment_date     = $request->input('payment_date', '');
        $amount           = $request->input('amount', '');
        
        $page             = $request->input('page', 1);
        $leads_per_page   = $request->input('leadsPerPage', 15);

        $query = DB::table(DB::raw('(
                    SELECT * FROM wp_mt_payments 
                    WHERE id IN (
                        SELECT MAX(id) FROM wp_mt_payments GROUP BY lead_id
                    )
                ) AS payments'))
            ->leftJoin('wp_mt_leads as leads', 'leads.id', '=', 'payments.lead_id')
            ->leftJoin('wp_mt_vendors as vendors', 'vendors.id', '=', 'payments.vendor_id')
            ->leftJoin('wp_users as users', 'users.id', '=', 'payments.user_id');

        if (!empty($search_filter)) {
            $query->where(function ($q) use ($search_filter) {
                $q->where('leads.name', 'like', "%$search_filter%")
                ->orWhere('leads.phone', 'like', "%$search_filter%")
                ->orWhere('leads.email', 'like', "%$search_filter%")
                ->orWhere('leads.country_code', 'like', "%$search_filter%")
                ->orWhere('leads.no_of_people', 'like', "%$search_filter%")
                ->orWhere('leads.message', 'like', "%$search_filter%")
                ->orWhere('users.display_name', 'like', "%$search_filter%")
                ->orWhere('payments.amount', 'like', "%$search_filter%")
                ->orWhere('vendors.name', 'like', "%$search_filter%");
            });
        }

        if (!empty($payment_trek) && is_array($payment_trek)) {
            $ids = array_map(function ($filter) {
                preg_match('/\D+_(\d+)/', $filter, $matches);
                return isset($matches[1]) ? (int) $matches[1] : null;
            }, $payment_trek);
            $ids = array_filter($ids);
            if (!empty($ids)) {
                $query->whereIn('leads.type_id', $ids);
            }
        }

        if (!empty($payment_vendor)) {
            $query->whereIn('payments.vendor_id', $payment_vendor);
        }

        if (!empty($payment_date)) {
            $dates = preg_split('/ to |,/', trim($payment_date));
            $start_date = date('Y-m-d', strtotime($dates[0])) . ' 00:00:00';
            $end_date = isset($dates[1]) ? date('Y-m-d', strtotime($dates[1])) . ' 23:59:59' : date('Y-m-d', strtotime($dates[0])) . ' 23:59:59';

            $query->whereBetween('payments.created_on', [convert_ist_to_utc($start_date), convert_ist_to_utc($end_date)]);
        }

        if (!empty($trek_date)) {
            $dates = preg_split('/ to |,/', trim($trek_date));
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = isset($dates[1]) ? date('Y-m-d', strtotime($dates[1])) : $start_date;

            $query->whereBetween(DB::raw('DATE(leads.trek_date)'), [$start_date, $end_date]);
        }

        if (!empty($amount) && floatval($amount) > 0) {
            $query->where('payments.amount', '=', number_format((float)$amount, 2, '.', ''));
        }

        $total = $query->count();

        $results = $query->select(
            'leads.*',
            'payments.*',
            'vendors.name as vendor_name',
            'users.display_name as user_name'
        )
        ->orderBy('payments.created_on', 'DESC')
        ->offset(($page - 1) * $leads_per_page)
        ->limit($leads_per_page)
        ->get();
        
        $formattedResults = $results->map(function ($payment) {
            $trek_name = 'N/A';
            if ($payment->type === 'page') {
                $trek_name = DB::table('wp_posts')->where('ID', $payment->type_id)->where('post_type', 'page')->value('post_title');
            } elseif (in_array($payment->type, ['product', 'mobile'])) {
                $trek_name = DB::table('wp_posts')->where('ID', $payment->type_id)->where('post_type', 'product')->value('post_title');
            } elseif ($payment->type === 'taxonomy') {
                $trek_name = DB::table('terms')->where('term_id', $payment->type_id)->value('name');
            }
            $created_on = TimezoneHelper::UtcToIst($payment->created_at);
            return [
                'trek_name' => $trek_name,
                'name' => $payment->name,
                'phone' => $payment->country_code . $payment->phone,
                'email' => $payment->email,
                'group_size' => $payment->no_of_people,
                'trek_date' => $payment->trek_date,
                'vendor_name' => $payment->vendor_name,
                'amount' => $payment->amount,
                'created_on' => $created_on,
                'user_name' => $payment->user_name,
                'lead_id' => $payment->lead_id,
                'type' => $payment->type,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedResults,
            'pagination' => [
                'total' => $total,
                'current_page' => $page,
                'per_page' => $leads_per_page,
                'last_page' => ceil($total / $leads_per_page),
            ]
        ]);
    }
}