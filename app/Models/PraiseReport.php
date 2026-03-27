<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PraiseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'church',
        'prayer_link',
        'attendance',
        'meeting_date',
        'testimony'
    ];

    // This ensures ->format() works in your Resource
    protected $casts = [
        'meeting_date' => 'date',
    ];
}
