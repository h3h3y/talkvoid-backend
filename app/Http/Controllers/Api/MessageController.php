<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\DeviceGender;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Kirim pesan biasa
    public function send(Request $request)
    {
        $request->validate([
            'sender' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'content' => 'required|string|max:1000',
            'fingerprint' => 'required|string'
        ]);

        // Cek apakah device diblokir
        $device = DeviceGender::where('fingerprint', $request->fingerprint)->first();

        if ($device && $device->isBanned()) {
            $remainingDays = $device->getBanRemainingDays();
            return response()->json([
                'success' => false,
                'message' => "Akun Anda telah diblokir selama {$remainingDays} hari karena melanggar kebijakan."
            ], 403);
        }

        $message = Message::create([
            'sender' => $request->sender,
            'sender_fingerprint' => $request->fingerprint,
            'is_reply' => false,
            'gender' => $request->gender,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim!',
            'data' => $message
        ], 201);
    }

    // Kirim balasan (PRIVATE - hanya untuk pengirim pesan asli)
    public function sendReply(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:messages,id',
            'reply_to_fingerprint' => 'required|string',
            'content' => 'required|string|max:1000',
            'fingerprint' => 'required|string',
            'sender' => 'required|string',
            'gender' => 'required|in:male,female'
        ]);

        // Cek apakah device diblokir
        $device = DeviceGender::where('fingerprint', $request->fingerprint)->first();

        if ($device && $device->isBanned()) {
            $remainingDays = $device->getBanRemainingDays();
            return response()->json([
                'success' => false,
                'message' => "Akun Anda telah diblokir selama {$remainingDays} hari karena melanggar kebijakan."
            ], 403);
        }

        $message = Message::create([
            'sender' => $request->sender,
            'sender_fingerprint' => $request->fingerprint,
            'reply_to_fingerprint' => $request->reply_to_fingerprint,
            'parent_id' => $request->parent_id,
            'is_reply' => true,
            'gender' => $request->gender,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dikirim!',
            'data' => $message
        ]);
    }

    // Ambil pesan random
    public function random(Request $request)
    {
        $request->validate([
            'my_gender' => 'required|in:male,female',
            'fingerprint' => 'required|string'
        ]);

        $myGender = $request->my_gender;
        $oppositeGender = $myGender === 'male' ? 'female' : 'male';
        $fingerprint = $request->fingerprint;

        // Dapatkan fingerprint user yang diblokir
        $bannedFingerprints = DeviceGender::where('is_banned', true)
            ->where(function ($query) {
                $query->whereNull('banned_until')
                    ->orWhere('banned_until', '>', now());
            })
            ->pluck('fingerprint')
            ->toArray();

        // Query pesan yang bisa dilihat:
        // 1. Pesan biasa (is_reply = false) ATAU
        // 2. Balasan yang ditujukan ke user ini (reply_to_fingerprint = fingerprint user)
        $query = Message::where('gender', $oppositeGender)
            ->where('is_hidden', false)
            ->where(function ($q) use ($fingerprint) {
                $q->where('is_reply', false)
                    ->orWhere('reply_to_fingerprint', $fingerprint);
            });

        if (!empty($bannedFingerprints)) {
            $query->whereNotIn('sender_fingerprint', $bannedFingerprints);
        }

        $message = $query->inRandomOrder()->first();

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => "Belum ada pesan dari lawan jenis yang tersedia."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    // Ambil semua pesan (untuk testing)
    public function all()
    {
        return response()->json([
            'success' => true,
            'data' => Message::latest()->get()
        ]);
    }
}
