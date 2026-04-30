<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceGender;
use App\Models\UserReport;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reported_fingerprint' => 'required|string|exists:device_genders,fingerprint',
            'reason' => 'required|string|in:spam,harassment,inappropriate,fake_location,other'
        ]);

        $reporterFingerprint = $request->fingerprint;
        $reportedFingerprint = $request->reported_fingerprint;

        if ($reporterFingerprint === $reportedFingerprint) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat melaporkan diri sendiri.'
            ], 400);
        }

        $existingReport = UserReport::where('reporter_fingerprint', $reporterFingerprint)
            ->where('reported_fingerprint', $reportedFingerprint)
            ->exists();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melaporkan pengguna ini sebelumnya.'
            ], 400);
        }

        UserReport::create([
            'reporter_fingerprint' => $reporterFingerprint,
            'reported_fingerprint' => $reportedFingerprint,
            'reason' => $request->reason
        ]);

        $totalReports = UserReport::where('reported_fingerprint', $reportedFingerprint)->count();

        $reportedDevice = DeviceGender::where('fingerprint', $reportedFingerprint)->first();

        if ($reportedDevice) {
            $reportedDevice->update(['report_count' => $totalReports]);

            if ($totalReports >= 10) {
                $reportedDevice->update([
                    'is_banned' => true,
                    'banned_until' => now()->addDays(30)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dilaporkan.',
            'report_count' => $totalReports
        ]);
    }

    // ========== METHOD UNTUK CEK STATUS BAN ==========
    public function checkBanStatus(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string|exists:device_genders,fingerprint'
        ]);

        $device = DeviceGender::where('fingerprint', $request->fingerprint)->first();

        if (!$device) {
            return response()->json([
                'is_banned' => false,
                'remaining_days' => 0,
                'remaining_hours' => 0,
                'report_count' => 0
            ]);
        }

        // Hitung sisa waktu dalam jam dan hari
        $remainingHours = 0;
        $remainingDays = 0;

        if ($device->banned_until && now()->lessThan($device->banned_until)) {
            $remainingHours = now()->diffInHours($device->banned_until);
            $remainingDays = round($remainingHours / 24, 1);
        }

        return response()->json([
            'is_banned' => $device->is_banned == 1,
            'remaining_days' => $remainingDays,
            'remaining_hours' => $remainingHours,
            'report_count' => $device->report_count ?? 0
        ]);
    }
}
