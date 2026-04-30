<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Report;
use App\Models\DeviceGender;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    const BAN_THRESHOLD = 10;

    public function store(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'fingerprint' => 'required|string',
            'reason' => 'required|string|in:spam,harassment,inappropriate,fake_location,other'
        ]);

        $messageId = $request->message_id;
        $reporterFingerprint = $request->fingerprint;

        // Cegah report ganda
        $existingReport = Report::where('message_id', $messageId)
            ->where('fingerprint', $reporterFingerprint)
            ->exists();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melaporkan pesan ini sebelumnya.'
            ], 400);
        }

        // Dapatkan pesan dan fingerprint pengirim
        $message = Message::find($messageId);
        $senderFingerprint = $message->sender_fingerprint;

        // Simpan report
        Report::create([
            'message_id' => $messageId,
            'fingerprint' => $reporterFingerprint,
            'reason' => $request->reason
        ]);

        // Hitung total report untuk pesan ini
        $totalReports = Report::where('message_id', $messageId)->count();
        $message->update(['report_count' => $totalReports]);

        // Sembunyikan pesan jika report >= 5
        if ($totalReports >= 5) {
            $message->update(['is_hidden' => true]);
        }

        // ========== UPDATE REPORT_COUNT DI DEVICE_GENDERS (UNTUK PENGIRIM) ==========
        if ($senderFingerprint) {
            // Hitung total report untuk semua pesan user ini
            $totalUserReports = Report::join('messages', 'reports.message_id', '=', 'messages.id')
                ->where('messages.sender_fingerprint', $senderFingerprint)
                ->count();

            // Update device_genders untuk pengirim pesan
            $senderDevice = DeviceGender::where('fingerprint', $senderFingerprint)->first();

            if ($senderDevice) {
                // Update report_count di device_genders
                $senderDevice->update(['report_count' => $totalUserReports]);

                // Jika report_count mencapai batas, banned!
                if ($totalUserReports >= self::BAN_THRESHOLD) {
                    $senderDevice->update([
                        'is_banned' => 1,  // tinyint(1): 1 = true
                        'banned_until' => now()->addDays(30)
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => "Pesan dilaporkan. Pengguna telah diblokir setelah {$totalUserReports} laporan.",
                        'report_count' => $totalReports,
                        'is_hidden' => $totalReports >= 5,
                        'user_banned' => true
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dilaporkan.',
            'report_count' => $totalReports,
            'is_hidden' => $totalReports >= 5,
            'user_banned' => false
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'fingerprint' => 'required|string'
        ]);

        $hasReported = Report::where('message_id', $request->message_id)
            ->where('fingerprint', $request->fingerprint)
            ->exists();

        $message = Message::find($request->message_id);

        return response()->json([
            'has_reported' => $hasReported,
            'report_count' => $message->report_count ?? 0,
            'is_hidden' => $message->is_hidden ?? false
        ]);
    }
}
