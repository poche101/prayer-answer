<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PraiseReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'group'        => $this->group,
            'church'       => $this->church,
            'attendance'   => (int) $this->attendance, // Added attendance
            'prayer_link'  => $this->prayer_link,

            // Format the meeting date for the UI
            'date'         => Carbon::parse($this->meeting_date)->format('Y-m-d'),

            'testimony'    => $this->testimony ?? 'No testimony shared',
            'created_at'   => $this->created_at->diffForHumans(),
        ];
    }
}
