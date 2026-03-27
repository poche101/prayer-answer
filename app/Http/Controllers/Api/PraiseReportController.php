<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PraiseReport;
use App\Http\Resources\PraiseReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PraiseReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        // Using latest() ensures the most recent church reports appear first on your dashboard
        return PraiseReportResource::collection(PraiseReport::latest()->get());
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
            'attendance'   => 'required|integer|min:1', // Validates the new field
            'meeting_date' => 'required|date|before_or_equal:today', // Prevents future dating
            'testimony'    => 'nullable|string',
        ]);

        $report = PraiseReport::create($validated);

        // Standardized JSON response for your Alpine.js frontend
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Praise Report Submitted Successfully!',
                'data'    => new PraiseReportResource($report)
            ], 201);
        }

        return new PraiseReportResource($report);
    }

    /**
     * Display the specified report.
     */
    public function show(PraiseReport $praiseReport)
    {
        return new PraiseReportResource($praiseReport);
    }

    /**
     * Update the specified report.
     */
    public function update(Request $request, PraiseReport $praiseReport)
    {
        $validated = $request->validate([
            'group'        => 'sometimes|string|max:255',
            'church'       => 'sometimes|string|max:255',
            'prayer_link'  => 'sometimes|url',
            'attendance'   => 'sometimes|integer|min:1',
            'meeting_date' => 'sometimes|date|before_or_equal:today',
            'testimony'    => 'nullable|string',
        ]);

        $praiseReport->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Report updated successfully',
            'data'    => new PraiseReportResource($praiseReport)
        ]);
    }

    /**
     * Remove the specified report.
     */
    public function destroy(PraiseReport $praiseReport)
    {
        $praiseReport->delete();

        // Return a 204 No Content or a JSON success message
        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully'
        ], 200);
    }
}
