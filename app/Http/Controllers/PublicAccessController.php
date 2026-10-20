<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class PublicAccessController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string'
        ]);

        // Get configured PIN (fallback to 123456)
        $configuredPin = Setting::get('calendar_link_pin', '123456');

        if ($request->pin === $configuredPin) {
            session(['public_access_unlocked' => true]);
            return response()->json([
                'success' => true,
                'message' => 'Kode akses benar. Seluruh halaman publik telah terbuka!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode akses salah. Silakan coba lagi.'
        ], 422);
    }
}
