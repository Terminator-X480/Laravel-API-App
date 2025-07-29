<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WpMtLead;
use App\Models\WpPost;
use App\Models\WpTerm;
use App\Models\MtBooking;
use App\Models\Payment;
use App\Models\Lead;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\LogEntryCreated;
use Illuminate\Support\Facades\Http;
use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Api\WhatsappController;

class LeadController extends Controller
{

    public function index()
    {
        $posts = DB::table('wp_posts as posts')
            ->select('posts.ID', 'posts.post_title') // Select only ID and post_title
            ->where('posts.post_type', 'product')  // Filter for post type 'product'
            ->get();

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    public function getProductDetails($id)
    {
        $wordpressUrl = env('WORDPRESS_URL'); // http://localhost/Madtrek
        $apiUrl = "{$wordpressUrl}/wp-json/madtrek/v1/product/{$id}";

        try {
            \Log::info('Calling custom WP product API', ['url' => $apiUrl]);
            $response = Http::timeout(10)->get($apiUrl);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                \Log::error('Custom WP API error', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'error' => 'Product not found or API error',
                    'details' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Error calling custom WP API', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error', 'message' => $e->getMessage()], 500);
        }
    }

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

    public function allTreks(){
        $products = DB::table('wp_posts')->where('post_type', 'product')->where('post_status', 'publish')->get();
        return response()->json(['success' => true, 'treks' => $products]);
    }

    public function allTreksPages(){
        $treks = DB::table('wp_posts')
        ->where('post_type', 'product')
        ->where('post_status', 'publish')
        ->select('ID as id', 'post_title as name')
        ->get();

        // Get Pages
        $pages = DB::table('wp_posts')
            ->where('post_type', 'page')
            ->where('post_status', 'publish')
            ->select('ID as id', 'post_title as name')
            ->get();

        // Get Taxonomies (categories under 'product_cat' taxonomy)
        $taxonomies = DB::table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'product_cat')
            ->select('wp_terms.term_id as id', 'wp_terms.name')
            ->get();

        return response()->json([
            'treks' => $treks,
            'pages' => $pages,
            'taxonomies' => $taxonomies
        ]);
    }

    public function getAllLeads(Request $request)
    {
        if (!session()->has('leads_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = WpMtLead::query();

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        // Filter by trek_date
        // if ($request->filled('trek_date')) {
        //     $query->where('trek_date', $request->input('trek_date'));
        // }
        if ($request->filled('trek_date')) {
            $dateInput = $request->input('trek_date');
        
            if (strpos($dateInput, ' to ') !== false) {
                // It's a range
                [$startDate, $endDate] = explode(' to ', $dateInput);
                $query->whereBetween('trek_date', [$startDate, $endDate]);
            } else {
                // It's a single date
                $query->where('trek_date', $dateInput);
            }
        }
        
        // Filter by lead_date
        if ($request->filled('lead_date')) {
            $dateInput = $request->input('lead_date');
            // $query->whereDate('created_at', $request->input('lead_date'));
            if (strpos($dateInput, ' to ') !== false) {
                // It's a range
                [$startDate, $endDate] = explode(' to ', $dateInput);
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                // It's a single date
                $query->where('created_at', $dateInput);
            }
        }

        // Booked only
        if ($request->booked_only == 1) {
            $query->whereIn('id', function ($subQuery) {
                $subQuery->select('lead_id')->from('wp_mt_bookings')->where('is_book', 1);
            });
        }

        //multifilter 
        $multiFilter = $request->input('multi_filter');
        // Handle if multi_filter is a comma-separated string instead of an array
        if (is_string($multiFilter)) {
            $multiFilter = array_map('trim', explode(',', $multiFilter));
        }
        if (!empty($multiFilter) && is_array($multiFilter)) {
            $filteredIds = [];

            foreach ($multiFilter as $filter) {
                if (preg_match('/^\D+_(\d+)$/', $filter, $matches)) {
                    $filteredIds[] = intval($matches[1]);
                }
            }
            if (!empty($filteredIds)) {
                $existingTypeIds = DB::table('wp_mt_leads')
                    ->whereIn('type_id', $filteredIds)
                    ->distinct()
                    ->pluck('type_id')
                    ->toArray();

                if (!empty($existingTypeIds)) {
                    $query->whereIn('type_id', $existingTypeIds);
                }
            }
        }

        //source
        $leadSources = $request->input('leadSources');
        if (is_string($leadSources)) {
            $leadSources = array_map('trim', explode(',', $leadSources));
        }
        if (!empty($leadSources) && is_array($leadSources)) {
            $query->whereIn('source', $leadSources);
        }

        // Pagination parameters
        $leadsPerPage = (int) ($request->input('per_page') ?? 15);
        $page = (int) ($request->input('page') ?? 1);

        // Paginate results
        $paginatedLeads = $query->orderByDesc('id')->paginate($leadsPerPage, ['*'], 'page', $page);

        // Fetch and enhance each lead
        $leads = $paginatedLeads->getCollection()->map(function ($lead) {
            // Trek Name
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

            // Background color by source
            $sourceColors = [
                'Popup' => '#ae2ab7',
                'Enquiry' => '#b71c1c',
                'Whatsapp' => '#019505',
                'Meta' => '#3b5998',
                'Call' => '#d3b238',
                'Other' => '#534dad',
                'Google' => '#1e90e1',
                'Abhinav' => '#008080',
                'Kailash' => '#db6f2f',
                'Khushwant' => '#88c708',
                'Vendor' => '#c7406c',
            ];
            $lead->backgroud_color = $sourceColors[$lead->source] ?? 'transparent';

            // Status
            $lead->current_status = 'New';
            $latestMeta = DB::table('wp_mt_leadsmeta')
                ->where('lead_id', $lead->id)
                ->where('meta_key', 'status_changed')
                ->orderByDesc('meta_id')
                ->value('meta_value');

            if ($latestMeta) {
                $metaData = json_decode($latestMeta, true);
                if (!empty($metaData) && is_array($metaData)) {
                    $lastStatusEntry = end($metaData);
                    if (!empty($lastStatusEntry['status_id'])) {
                        $status = DB::table('wp_mt_leads_status')
                            ->where('id', intval($lastStatusEntry['status_id']))
                            ->value('status');
                        if ($status) {
                            $lead->current_status = $status;
                        }
                    }
                }
            }

            // Status options
            $lead->statuses = DB::table('wp_mt_leads_status')->select('id', 'status')->get();

            // Booking data
            $booking = MtBooking::where('lead_id', $lead->id)->first();
            $lead->is_book = $booking->is_book ?? 0;
            $lead->is_cancel = $booking->is_cancel ?? 0;

            // Call count
            $lead->call_count = DB::table('wp_whatsapp_numbers_calls')
                ->where('phone', 'LIKE', '%' . $lead->phone . '%')
                ->count();

            //whatsapp messages
            $results = WhatsappController::printAllWhatsappMessages($lead);
            $msgs = [];
            foreach( $results as $msg ){
    
                $formatted = $msg->time ;
                $msgs[] = [
                            'direction' => $msg->direction,
                            'body' => $msg->body,
                            'type' => $msg->type,
                            'time' => $formatted,
                            'device_name' => $msg->dname

                        ];
            }

            $lead->all_messages = $msgs;

            return $lead;
        });

        return response()->json([
            'data' => $leads,
            'pagination' => [
                'total' => $paginatedLeads->total(),
                'per_page' => $paginatedLeads->perPage(),
                'current_page' => $paginatedLeads->currentPage(),
                'last_page' => $paginatedLeads->lastPage(),
                'from' => $paginatedLeads->firstItem(),
                'to' => $paginatedLeads->lastItem(),
            ],
        ]);
    }

    public function get($id)
    {
        $lead = WpMtLead::findOrFail($id);
        $lead->created_on = TimezoneHelper::UtcToIst($lead->created_at);
        return response()->json($lead);
    }

    public function update(Request $request, $id)
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
        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    }

    public function addLead(Request $request){
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
        if(empty($data['created_at'])){
            unset($data['created_at']);
        }else{
            $data['created_at'] = TimezoneHelper::IstToUtc($data['created_at']);
        }
        $data['type'] = 'product';
        $response = WpMtLead::insert($data);
        if($response){
            return response()->json(['success' => true, 'message' => 'Lead Added successfully']);
        }
        else{
            return response()->json(['false' => true, 'message' => 'Something went wrong ']);
        }
    }

    public function handleLeadPaymentAndBooking(array $data): array
    {
        $leadId = $data['lead_id'] ?? null;
        if (! $leadId) {
            return ['success' => false, 'error' => 'Missing lead_id'];
        }

        $amount = isset($data['amount']) ? (float) $data['amount'] : 0.0;
        $vendorId = $data['vendor_id'] ?? null;
        $userId = session('leads_user_id');
        // $createdOn = isset($data['created_on']) ? Carbon::parse($data['created_on'])->setTimezone('UTC')->format('Y-m-d H:i:s') : now()->toDateTimeString();
        $createdOn = TimezoneHelper::IstToUtc($createdOn)?? now()->toDateTimeString();
        
        $vendorName = null;
        if ($vendorId) {
            $vendorName = Vendor::where('id', $vendorId)->value('name');
        }

        $existingBooking = MtBooking::where('lead_id', $leadId)->first();

        $wasUncanceled = false;
        $paymentId = null;

        // ------- Handle Booking -------
        if ($existingBooking) {
            if ($existingBooking->is_cancel == 1) {
                $existingBooking->update(['is_cancel' => 0]);
                event(new LogEntryCreated($leadId, 6 ));
                $wasUncanceled = true;
            }

            if ($existingBooking->is_book != 1) {
                $updateData = collect($data)->only($existingBooking->getFillable())->toArray();
                $updateData['is_book'] = 1;
                $updateData['bot'] = $data['bot'] ?? 0;
                $existingBooking->update($updateData);

                $actionData = [];
                if ($amount > 0) {
                    $actionData['paid_amount'] = $amount;
                }
                if ($vendorName) {
                    $actionData['receiver'] = $vendorName;
                }
                event(new LogEntryCreated($leadId, 1 , $actionData ));
            }
        } else {
            $insertData = collect($data)->only((new MtBooking)->getFillable())->toArray();
            $insertData['lead_id'] = $leadId;
            $insertData['is_book'] = 1;
            // $insertData['created_at'] = $createdOn;
            $insertData['bot'] = $data['bot'] ?? 0; // or null if your DB allows it


            MtBooking::create($insertData);

            $actionData = [];
            if ($amount > 0) {
                $actionData['paid_amount'] = $amount;
            }
            if ($vendorName) {
                $actionData['receiver'] = $vendorName;
            }
            event(new LogEntryCreated($leadId, 1 , $actionData ));
        }

        // ------- Handle Payment -------
        if ($amount > 0) {
            $paymentData = collect($data)->only((new Payment)->getFillable())->toArray();
            $paymentData['user_id'] = $userId;
            $paymentData['vendor_id'] = $vendorId;
            $paymentData['created_on'] = $createdOn;
            $paymentData['bot'] = $data['bot'] ?? 0;;

            $payment = Payment::create($paymentData);
            $paymentId = $payment->id;
            $actionData = [];
            if ($amount > 0) {
                    $actionData['amount'] = $amount;
                }
            if ($vendorName) {
                $actionData['receiver'] = $vendorName;
            }

            event(new LogEntryCreated($leadId, 7 , $actionData ));
        }

        return [
            'success' => true,
            'was_uncanceled' => $wasUncanceled,
            'payment_id' => $paymentId,
        ];
    }

    public function book(Request $request, $id): JsonResponse
    {
        $data = $request->all();
        $data['lead_id'] = $id;

        $response = $this->handleLeadPaymentAndBooking($data);

        return response()->json($response);
    }

    public function unbook(Request $request, $id)
    {
        $leadId = intval($id);
        $unbookReason = $request->input('reason', '');
        if (!$leadId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Lead ID.'
            ], 400);
        }

        // Assuming you have a Booking model mapped to wp_mt_bookings table
        $booking = MtBooking::where('lead_id', $leadId)->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        $booking->is_book = 0;
        $booking->save();

        $actionData = [];
        if ( $unbookReason) {
            $actionData = ['_unbook_reason' => $unbookReason];
        }
        event(new LogEntryCreated($leadId, 2 , $actionData ));

        return response()->json([
            'success' => true,
            'message' => 'Successfully marked as unbooked'
        ]);
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $booking = MtBooking::where('lead_id', $id)->first();
        $wasUnbooked = false;

        if ($booking) {
            // Update is_cancel to 1
            $booking->is_cancel = 1;

            // If the lead was booked, mark it as unbooked
            if ($booking->is_book == 1) {
                $booking->is_book = 0;
                $wasUnbooked = true;
                event(new LogEntryCreated($id, 2 ));
            }

            $booking->save();
        } else {
            // No existing booking record: insert one
            MtBooking::create([
                'lead_id'   => $id,
                'is_cancel' => 1,
            ]);
        }
        event(new LogEntryCreated($id, 5 ));

        return response()->json([
            'success' => true,
            'was_unbooked' => $wasUnbooked,
        ]);
    }

    public function uncancel(Request $request, $id){
        $leadId = (int) $id;

        if (!$leadId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Lead ID.'
            ], 400);
        }

        $cancelLead = MtBooking::where('lead_id',$leadId)->first();
        if(!$cancelLead){
            return response()->json([
                'success' => false,
                'message' => 'Lead Not Found',
            ],404);
        }
        $cancelLead->is_cancel = 0;
        $cancelLead->save();
        event(new LogEntryCreated($id, 6 ));
        return response()->json([
            'success' => true,
            'message' => 'Successfully marked as uncancelled'
        ]);
    }

    public function savePayments(Request $request){
        $data = $request->all();
        $response = $this->handleLeadPaymentAndBooking($data);
        return response()->json($response);
    }

    public function updateStatus(Request $request,$id)
    {
        $lead_id = (int) $id;
        $status_id = intval($request->input('status_id'));
        $status_changed = $request->input('status_changed');

        if (!$lead_id || !$status_id) {
            return response()->json(['success' => false, 'message' => 'Invalid data provided'], 400);
        }

        $table_leadsmeta = 'wp_mt_leadsmeta';

        // Fetch existing meta_value (JSON) for the lead
        $existingMeta = DB::table($table_leadsmeta)
            ->where('lead_id', $lead_id)
            ->where('meta_key', 'status_changed')
            ->value('meta_value');

        // Decode or initialize the array
        $meta_value_data = $existingMeta ? json_decode($existingMeta, true) : [];

        // Append new status change
        $meta_value_data[] = [
            'status_id' => $status_id,
            'status_changed' => $status_changed,
            'changed_at' => now()->toDateTimeString(), // optional timestamp
        ];

        // Save back to DB
        if ($existingMeta) {
            $update = DB::table($table_leadsmeta)
                ->where('lead_id', $lead_id)
                ->where('meta_key', 'status_changed')
                ->update([
                    'meta_value' => json_encode($meta_value_data),
                ]);

            if ($update === false) {
                return response()->json(['success' => false, 'message' => 'Failed to update meta.'], 500);
            }
        } else {
            $insert = DB::table($table_leadsmeta)->insert([
                'lead_id' => $lead_id,
                'meta_key' => 'status_changed',
                'meta_value' => json_encode($meta_value_data),
                'created_at' => now(),
            ]);

            if (!$insert) {
                return response()->json(['success' => false, 'message' => 'Failed to insert meta.'], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
