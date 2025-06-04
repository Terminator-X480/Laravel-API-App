<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BasicAuthWooCommerce
{
    public function handle(Request $request, Closure $next)
    {
        $consumerSecret = $request->getPassword();

        if ($consumerSecret) {
            $row = DB::table('wp_woocommerce_api_keys')
                ->where('consumer_secret', $consumerSecret)
                ->first();

            if ($row) {
                // Merge user info only if a valid secret was found
                $request->merge(['woocommerce_user' => (array) $row]);
            }
        }

        // Always allow the request to continue
        return $next($request);
    }
}
