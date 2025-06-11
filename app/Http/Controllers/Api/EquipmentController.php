<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = DB::table('wp_mt_equipments')->get();

        return response()->json([
            'success' => true,
            'equipments' => $equipments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipmentname' => 'required|string|max:255',
        ]);

        $equipmentname = trim($request->input('equipmentname'));

        $exists = DB::table('wp_mt_equipments')->where('name', $equipmentname)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This equipment already exists.',
            ], 409);
        }

        $id = DB::table('wp_mt_equipments')->insertGetId([
            'name' => $equipmentname,
        ]);


        if ($id) {
            return response()->json([
                'success' => true,
                'equipment' => [
                    'id' => $id,
                    'name' => $equipmentname,
                ]
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save equipment.',
            ], 500);
        }
    }
}