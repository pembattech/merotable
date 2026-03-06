<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\StaffAttendance;

class AttendanceController extends Controller
{
    // Check In
    public function checkIn(Request $request)
    {

        $staff = auth('staff')->user();
        $staffId = $staff->id;
        $restaurantId = $staff->restaurant_id;

        $today = Carbon::today();

        // Check if already checked in today
        $attendance = StaffAttendance::where('staff_id', $staffId)
            ->where('restaurant_id', $restaurantId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            return response()->json([
                'message' => 'Already checked in today.'
            ], 400);
        }

        // TODO: add a function for staff that chooses status(present, late or absent) as per the restaurant or staff shift timetable.

        $attendance = StaffAttendance::create([
            'staff_id' => $staffId,
            'restaurant_id' => $restaurantId,
            'date' => $today,
            'check_in' => now(),
            'status' => 'present'
        ]);

        // TODO: create a staff attendance resource.
        return response()->json([
            'success' => true,
            'data' => $attendance,
            'message' => 'Check-in successful.'
        ]);
    }

    // Check Out
    public function checkOut(Request $request)
    {

        $staff = auth('staff')->user();
        $staffId = $staff->id;
        $restaurantId = $staff->restaurant_id;


        $today = Carbon::today();

        $attendance = StaffAttendance::where('staff_id', $staffId)
        ->where('restaurant_id', $restaurantId)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'You have not checked in today.'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Already checked out.'
            ], 400);
        }

        $checkOutTime = now();

        // Calculate total hours
        $minutes = $attendance->check_in->diffInMinutes($checkOutTime);
        $totalHours = round($minutes / 60, 2);

        $attendance->update([
            'check_out' => $checkOutTime,
            'total_hours' => $totalHours
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out successful',
            'data' => $attendance
        ]);
    }
}