<?php
namespace App\Http\Livewire\Attendances;
use Livewire\Component;
use Log;
use Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class Tapping extends Component
{
    public $latitude;
    public $longitude;
    public $canCheckIn = false;
    public $canCheckOut = false;
    private $targetLatitude = -6.395193286627945;
    private $targetLongitude = 106.96255401126793;
    private $maxDistance = 2000;

    public function mount()
    {
        $this->checkAttendanceStatus();
    }

    private function checkAttendanceStatus()
    {
        $today = Carbon::now()->toDateString();
        
        // Get latest attendance for today
        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();
    
        if (!$todayAttendance) {
            // No attendance record for today
            $this->canCheckIn = true;
            $this->canCheckOut = false;
        } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {
            // Has checked in today but not checked out
            $this->canCheckIn = false;
            $this->canCheckOut = true;
        } else {
            // Already completed attendance for today
            $this->canCheckIn = false;
            $this->canCheckOut = false;
            session()->flash('error', 'You have already completed your attendance for today.');
        }
    }
    
    public function checkIn()
    {
        // Log::info('Check In - Start');
        
        // Check if already checked in today
        $today = Carbon::now()->toDateString();
        $existingAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();
    
        if ($existingAttendance) {
            session()->flash('error', 'You have already checked in for today.');
            return;
        }
    
        // Check distance
        $distance = $this->isWithinRange();
        if ($distance > $this->maxDistance) {
            session()->flash('error', 'You are not within the allowed range to check in. Your current location: ' . 
                "{$this->latitude}, {$this->longitude}. Distance: " . round($distance, 2) . ' meters');
            return;
        }
    
        try {
            Attendance::create([
                'user_id' => Auth::id(),
                'attendance_date' => $today,
                'check_in' => Carbon::now(),
                'check_in_location' => "{$this->latitude}, {$this->longitude}",
                'status' => 'hadir',
            ]);
    
            $this->canCheckIn = false;
            $this->canCheckOut = true;
            session()->flash('success', 'Successfully checked in!');
        } catch (\Exception $e) {
            Log::error('Check In Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to check in. Please try again.');
        }
    
        // Log::info('Check In - End');
    }

    public function checkOut()
    {
        // Log::info('Check Out - Start');

        // Check distance
        $distance = $this->isWithinRange();
        if ($distance > $this->maxDistance) {
            session()->flash('error', 'You are not within the allowed range to check out. Your current location: ' . 
                "{$this->latitude}, {$this->longitude}. Distance: " . round($distance, 2) . ' meters');
            return;
        }

        try {
            $lastAttendance = Attendance::where('user_id', Auth::id())
                ->whereNull('check_out')
                ->latest()
                ->first();

            if ($lastAttendance) {
                $lastAttendance->update([
                    'check_out' => Carbon::now(),
                    'check_out_location' => "{$this->latitude}, {$this->longitude}",
                ]);

                $this->canCheckIn = true;
                $this->canCheckOut = false;
                session()->flash('success', 'Successfully checked out!');
            }
        } catch (\Exception $e) {
            Log::error('Check Out Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to check out. Please try again.');
        }

        // Log::info('Check Out - End');
    }

    private function isWithinRange()
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($this->targetLatitude);
        $lonTo = deg2rad($this->targetLongitude);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function render()
    {
        return view('livewire.attendances.tapping');
    }
}