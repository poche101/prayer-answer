<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'group' => $this->group,
            'church' => $this->church,
            'prayer_link' => $this->prayer_link,

            // Attendance Data
            'attendance' => (int) $this->attendance,
            'formatted_attendance' => number_format($this->attendance), // e.g., "1,200"

            /** * Date Formatting
             * This fixes the "format() on string" error by using the
             * casted Carbon instance from the Model.
             */
            'date' => $this->meeting_date->format('M d, Y'), // e.g., Mar 27, 2026
            'raw_date' => $this->meeting_date->format('Y-m-d'), // For date picker inputs

            // Content Handling
            'testimony' => $this->testimony ?? 'No testimony shared',

            // Metadata for UI "time-stamps"
            'submitted_at' => $this->created_at->diffForHumans(), // e.g., "2 hours ago"

            // Helpful for conditional styling in the dashboard
            'has_testimony' => !empty($this->testimony),
        ];
    }
}
