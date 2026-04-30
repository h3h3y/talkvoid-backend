<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'message_id',
        'fingerprint',
        'reason'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
