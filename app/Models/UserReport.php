<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $table = 'user_reports';

    protected $fillable = [
        'reporter_fingerprint',
        'reported_fingerprint',
        'reason'
    ];
}
