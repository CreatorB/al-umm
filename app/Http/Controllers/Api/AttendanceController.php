<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use App\Utils\NetworkUtils;
use App\Http\Middleware\CheckLocalServer;

class AttendanceController extends Controller
{
    use ApiResponse;
    private $targetLatitude = -6.395193286627945;
    private $targetLongitude = 106.96255401126793;
    private $maxDistance = 3000;

    public function __construct()
    {
        $this->middleware('check.local')->only(['checkIn', 'checkOut']);
    }

    public function checkIn(Request $request)
    {
        try {
            // if (!NetworkUtils::isLocalServerAccessible()) {
            //     return $this->errorResponse('Absensi hanya bisa dilakukan dalam jaringan kantor.', 403);
            // }

            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'device_info' => 'required',
            ]);

            $today = Carbon::now()->toDateString();
            $user = auth()->user();

            // Check distance
            $distance = $this->isWithinRange($validated['latitude'], $validated['longitude']);
            if ($distance > $this->maxDistance) {
                return $this->errorResponse('You are not within allowed range', 400);
            }

            // Get schedule
            $schedule = $user->schedule;
            if (!$schedule) {
                return $this->errorResponse('No schedule found', 404);
            }

            // Get latest shift
            $latestShift = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->orderBy('shift', 'desc')
                ->first();

            $newShift = $latestShift ? $latestShift->shift + 1 : 1;

            // Check time against schedule
            $todayDay = strtolower(Carbon::now()->format('l'));
            $startTime = $schedule->{"{$todayDay}_start"};
            $checkInTime = Carbon::now()->format('H:i:s');
            $detectedDevice = 'Unknown';
            if ($validated['device_info']) {
                $detectedDevice = $validated['device_info'];
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'attendance_date' => $today,
                'check_in' => Carbon::now(),
                'check_in_location' => "{$detectedDevice},lat: {$validated['latitude']}, long: {$validated['longitude']}",
                'status' => 'hadir',
                'shift' => $newShift,
                'late' => $startTime && $checkInTime > $startTime,
                'is_overtime' => $startTime && $checkInTime < $startTime
            ]);

            return $this->successResponse($attendance, 'Successfully checked in');

        } catch (\Exception $e) {
            Log::error('Check In Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to check in', 400);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            // if (!NetworkUtils::isLocalServerAccessible()) {
            //     return $this->errorResponse('Absensi hanya bisa dilakukan dalam jaringan kantor.', 403);
            // }

            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'device_info' => 'required',
            ]);

            $today = Carbon::now()->toDateString();
            $user = auth()->user();

            // Check distance
            $distance = $this->isWithinRange($validated['latitude'], $validated['longitude']);
            if ($distance > $this->maxDistance) {
                return $this->errorResponse('You are not within allowed range', 400);
            }

            $lastAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->whereNull('check_out')
                ->latest()
                ->first();

            if (!$lastAttendance) {
                return $this->errorResponse('No active check-in found', 404);
            }

            // Check time against schedule
            $schedule = $user->schedule;
            $todayDay = strtolower(Carbon::now()->format('l'));
            $endTime = $schedule->{"{$todayDay}_end"};
            $checkOutTime = Carbon::now()->format('H:i:s');
            $detectedDevice = 'Unknown';
            if ($validated['device_info']) {
                $detectedDevice = $validated['device_info'];
            }

            $lastAttendance->update([
                'check_out' => Carbon::now(),
                'check_out_location' => "{$detectedDevice},lat: {$validated['latitude']}, long: {$validated['longitude']}",
                'early_leave' => $endTime && $checkOutTime < $endTime,
                'is_overtime' => $endTime && $checkOutTime > $endTime
            ]);

            return $this->successResponse($lastAttendance, 'Successfully checked out');

        } catch (\Exception $e) {
            Log::error('Check Out Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to check out', 400);
        }
    }

    public function status()
    {
        try {
            $user = auth()->user();
            $today = Carbon::now()->toDateString();

            $todayAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->latest()
                ->first();

            $canCheckIn = !$todayAttendance ||
                ($todayAttendance->check_in && $todayAttendance->check_out && $todayAttendance->shift < 10);

            $canCheckOut = $todayAttendance &&
                $todayAttendance->check_in &&
                !$todayAttendance->check_out;

            return $this->successResponse([
                'can_check_in' => $canCheckIn,
                'can_check_out' => $canCheckOut,
                'today_attendance' => $todayAttendance
            ]);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to get status', 400);
        }
    }

    private function isWithinRange($latitude, $longitude)
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($this->targetLatitude);
        $lonTo = deg2rad($this->targetLongitude);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}