<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrayerReport extends Model
{
    use HasFactory;

    protected $table = 'praise_reports'; // Keeps the same table name

    protected $fillable = [
        'group',
        'church',
        'prayer_link',
        'meeting_date',
        'testimony'
    ];

    protected $casts = [
        'meeting_date' => 'date', // Fixes the ->format() on string error
    ];
}
