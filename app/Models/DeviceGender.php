<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceGender extends Model
{
    protected $fillable = [
        'device_id',
        'fingerprint',
        'gender',
        'ip_address',
        'is_banned',
        'banned_until',
        'report_count',
        'total_reports'
    ];

    // Cek apakah device sedang diblokir
    public function isBanned()
    {
        if (!$this->is_banned) {
            return false;
        }

        if ($this->banned_until && now()->greaterThan($this->banned_until)) {
            $this->update([
                'is_banned' => false,
                'banned_until' => null
            ]);
            return false;
        }

        return true;
    }

    // Dapatkan sisa hari blokir
    public function getBanRemainingDays()
    {
        if ($this->banned_until && now()->lessThan($this->banned_until)) {
            return now()->diffInDays($this->banned_until);
        }
        return 0;
    }

    // Blokir akun
    public function ban($days = 30)
    {
        $this->update([
            'is_banned' => true,
            'banned_until' => now()->addDays($days)
        ]);
    }
}
