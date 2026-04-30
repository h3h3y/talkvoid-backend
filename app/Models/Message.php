<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    protected $fillable = [
        'sender',
        'sender_fingerprint',
        'reply_to_fingerprint',
        'parent_id',
        'is_reply',
        'gender',
        'content',
        'is_hidden',
        'report_count'
    ];

    // Enkripsi saat menyimpan
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    // Dekripsi saat mengambil (kecuali pesan tersembunyi)
    public function getContentAttribute($value)
    {
        if ($this->is_hidden) {
            return '[Pesan ini telah dihapus karena melanggar kebijakan]';
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return '[Pesan tidak dapat dibaca]';
        }
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
