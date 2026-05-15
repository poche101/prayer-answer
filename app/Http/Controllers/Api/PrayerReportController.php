<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrayerReport;
use App\Http\Resources\PrayerReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrayerReportController extends Controller
{
    /**
     * Display a listing of the reports with Search & Filter logic.
     */
    public function index(Request $request)
    {
        $query = PrayerReport::query();

        // Optional: Filter by Group safely if filled
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Optional: Search by Church Name safely if filled
        if ($request->filled('search')) {
            $query->where('church', 'like', '%' . $request->search . '%');
        }

        // Ordering by meeting_date ensures chronological layout on the frontend
        $reports = $query->orderBy('meeting_date', 'desc')->get();

        return PrayerReportResource::collection($reports);
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group'        => 'required|string|max:255',
            'church'       => 'required|string|max:255',
            'prayer_link'  => 'required|url',
            'attendance'   => 'required|integer|min:0',
            'meeting_date' => 'required|date',
            'testimony'    => 'nullable|string',
        ]);

        $report = PrayerReport::create($validated);

        return (new PrayerReportResource($report))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display a specific report.
     */
    public function show(PrayerReport $prayerReport)
    {
        return new PrayerReportResource($prayerReport);
    }

    /**
     * Update an existing report.
     */
    public function update(Request $request, PrayerReport $prayerReport)
    {
        $validated = $request->validate([
            'group'        => 'sometimes|string|max:255',
            'church'       => 'sometimes|string|max:255',
            'prayer_link'  => 'sometimes|url',
            'attendance'   => 'sometimes|integer|min:0',
            'meeting_date' => 'sometimes|date',
            'testimony'    => 'nullable|string',
        ]);

        $prayerReport->update($validated);

        return new PrayerReportResource($prayerReport);
    }

    /**
     * Remove a report from the database.
     */
    public function destroy(PrayerReport $prayerReport)
    {
        $prayerReport->delete();

        return response()->json([
            'message' => 'Report deleted successfully'
        ], Response::HTTP_OK);
    }
}
