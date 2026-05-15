<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerReport extends Model
{
    use HasFactory;

    protected $table = 'prayer_reports';

    protected $fillable = [
        'group',
        'church',
        'prayer_link',
        'attendance',
        'meeting_date',
        'testimony',
    ];

    // 👇 ADD THIS CASTS ARRAY BELOW TO CONVERT STRINGS INTO CARBON OBJECTS
    protected $casts = [
        'meeting_date' => 'date',
        'attendance' => 'integer',
    ];
}
