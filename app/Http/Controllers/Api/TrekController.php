<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WpPost;
use App\Models\WpTerm;

class TrekController extends Controller
{
    public function getName(Request $request)
    {
        $type = $request->query('type');
        $typeId = $request->query('type_id');
        $trekName = 'Unknown Type';

        if (!$type || !$typeId) {
            return response()->json([
                'error' => 'Missing required parameters: type and type_id'
            ], 400);
        }

        switch ($type) {
            case 'product':
            case 'mobile':
                $post = WpPost::where('ID', $typeId)
                    ->where('post_type', 'product')
                    ->first();
                $trekName = $post?->post_title ?? 'Unknown Product';
                break;

            case 'page':
                $post = WpPost::where('ID', $typeId)
                    ->where('post_type', 'page')
                    ->first();
                $trekName = $post?->post_title ?? 'Unknown Page';
                break;

            case 'taxonomy':
                $term = WpTerm::find($typeId);
                $trekName = $term?->name ?? 'Unknown Taxonomy';
                break;
        }

        return response()->json([
            'trek_name' => $trekName
        ]);
    }
}
