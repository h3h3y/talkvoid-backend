<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceGender;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    // Cek apakah device sudah terdaftar
    public function check(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string'
        ]);

        $device = DeviceGender::where('fingerprint', $request->fingerprint)->first();

        if ($device) {
            return response()->json([
                'registered' => true,
                'gender' => $device->gender
            ]);
        }

        return response()->json([
            'registered' => false,
            'message' => 'Device not registered'
        ]);
    }

    // Registrasi gender untuk device
    public function register(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string',
            'gender' => 'required|in:male,female'
        ]);

        // CEK APAKAH SUDAH TERDAFTAR — INI PENTING!
        $existing = DeviceGender::where('fingerprint', $request->fingerprint)->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat ini sudah terdaftar! Tidak dapat registrasi ulang.'
            ], 400);  // ← 400 Bad Request
        }

        // Jika belum terdaftar, lanjutkan registrasi...
        $deviceId = hash('sha256', $request->fingerprint . time());

        $device = DeviceGender::create([
            'device_id' => $deviceId,
            'fingerprint' => $request->fingerprint,
            'gender' => $request->gender,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gender berhasil disimpan',
            'gender' => $device->gender
        ], 201);
    }
}
