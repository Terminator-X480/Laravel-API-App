<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AudioUploadController extends Controller
{
    public function uploadAudio(Request $request)
    {
        $request->validate([
            'audio_file' => 'required|file|mimes:m4a|max:20480', // max 20MB
        ]);

        $file = $request->file('audio_file');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads/audio', $filename, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'filename' => $filename,
            'url' => asset('storage/' . $path),
        ]);
    }
}
