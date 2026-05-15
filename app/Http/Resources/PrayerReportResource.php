<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PrayerReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Unique ID for Alpine.js keys
            'id' => $this->id,

            // Core Information
            'group'       => $this->group,
            'church'      => $this->church,
            'prayer_link' => $this->prayer_link,

            // Attendance Data
            'attendance'           => (int) $this->attendance,
            'formatted_attendance' => number_format($this->attendance),

            // Date Fields
            // 'date'         → pre-formatted for display:  "May 15, 2026"
            // 'raw_date'     → Y-m-d for date picker inputs: "2026-05-15"
            // 'meeting_date' → same as raw_date, kept for any legacy JS reads
            'date'         => $this->meeting_date
                                ? Carbon::parse($this->meeting_date)->format('M d, Y')
                                : null,
            'raw_date'     => $this->meeting_date
                                ? Carbon::parse($this->meeting_date)->format('Y-m-d')
                                : null,
            'meeting_date' => $this->meeting_date
                                ? Carbon::parse($this->meeting_date)->format('Y-m-d')
                                : null,

            // Content
            'testimony' => $this->testimony ?? 'No testimony shared',

            // Metadata
            'submitted_at' => $this->created_at
                                ? Carbon::parse($this->created_at)->diffForHumans()
                                : null,

            // Helpers for conditional UI
            'has_testimony' => !empty($this->testimony),
        ];
    }
}
