<?php
namespace App\Http\Livewire\Attendances;

use App\Utils\NetworkUtils;
use Livewire\Component;
use Log;
use Auth;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class Tapping extends Component
{
    public $latitude;
    public $longitude;
    public $canCheckIn = false;
    public $canCheckOut = false;
    private $targetLatitude = -6.395193286627945;
    private $targetLongitude = 106.96255401126793;
    private $maxDistance = 3000;

    public $isInNetwork = false;
    public $pingTime = null;
    public $deviceInfo = null;
    public $todayAttendance = null;

    protected $listeners = [
        'overtimeConfirmed' => 'handleOvertimeConfirmation',
        'locationRetrieved' => 'locationRetrieved',
        'locationFailed' => 'locationFailed',
        'checkPing' => 'validateNetwork',
        'updateDeviceInfo' => 'handleDeviceInfoUpdate'
    ];

    public function locationRetrieved($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        Log::info("Browser location retrieved: {$this->latitude}, {$this->longitude}");
    }

    public function locationFailed()
    {
        session()->flash('error', 'Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan dan koneksi internet tersedia.');
        Log::error('Failed to retrieve location from browser.');
    }

    public function mount()
    {
        $this->loadTodayAttendance();
        $this->checkAttendanceStatus();
        // $this->getUserLocation();
        // $this->validateNetwork();
    }

    private function loadTodayAttendance()
    {
        $this->todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->latest()
            ->first();
    }

    private function getUserLocation()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        Log::info("Attempting to retrieve location for IP: {$ip}");

        // Jika IP adalah IP internal, langsung minta lokasi dari browser
        if ($this->isInternalIP($ip)) {
            Log::info("IP is internal. Requesting browser location.");
            $this->emit('requestBrowserLocation');
            return;
        }

        // Coba ambil lokasi dari IP
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $location = @file_get_contents("http://ip-api.com/json/{$ip}", false, $context);

        if ($location === FALSE) {
            Log::error("Failed to retrieve location from ip-api.com for IP: {$ip}");
            $this->emit('requestBrowserLocation');
            return;
        }

        $location = json_decode($location, true);

        if ($location && $location['status'] === 'success') {
            $this->latitude = $location['lat'];
            $this->longitude = $location['lon'];
            Log::info("Location retrieved successfully: {$this->latitude}, {$this->longitude}");
        } else {
            Log::error("Failed to decode location or status is not success for IP: {$ip}");
            $this->emit('requestBrowserLocation');
        }
    }

    private function isInternalIP($ip)
    {
        // Daftar range IP internal
        $internalRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ];

        foreach ($internalRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange($ip, $range)
    {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; // Apply mask to subnet
        return ($ip & $mask) === $subnet;
    }


    // private function checkAttendanceStatus()
    // {
    //     $today = Carbon::now()->toDateString();

    //     $todayAttendance = Attendance::where('user_id', Auth::id())
    //         ->whereDate('attendance_date', $today)
    //         ->first();

    //     if (!$todayAttendance) {

    //         $this->canCheckIn = true;
    //         $this->canCheckOut = false;
    //     } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {

    //         $this->canCheckIn = false;
    //         $this->canCheckOut = true;
    //     } else {

    //         if ($todayAttendance->shift < 10) {
    //             $this->canCheckIn = true;
    //             $this->canCheckOut = false;
    //         } else {
    //             $this->canCheckIn = false;
    //             $this->canCheckOut = false;
    //             session()->flash('error', 'You have already completed all shifts for today.');
    //         }
    //     }
    // }
    private function checkAttendanceStatus()
    {
        if (!$this->todayAttendance) {
            $this->canCheckIn = true;
            $this->canCheckOut = false;
            return;
        }

        if ($this->todayAttendance->check_in && !$this->todayAttendance->check_out) {
            $this->canCheckIn = false;
            $this->canCheckOut = true;
            return;
        }

        if ($this->todayAttendance->shift < 10) {
            $this->canCheckIn = true;
            $this->canCheckOut = false;
        } else {
            $this->canCheckIn = false;
            $this->canCheckOut = false;
            session()->flash('error', 'You have already completed all shifts for today.');
        }
    }

    private function getTodaySchedule()
    {
        // $user = User::find(Auth::id());
        // return $user->schedule;
        $user = User::with(['department', 'schedule', 'part'])->find(Auth::id());
        return $user->schedule;
    }

    // public function checkIn()
    // {
    //     if (!$this->validateNetwork()) {
    //         session()->flash('error', 'Anda harus terhubung ke jaringan Wi-Fi / LAN Mahad Syathiby untuk melakukan absensi.');
    //         return;
    //     }

    //     $today = Carbon::now()->toDateString();

    //     // if (!$this->latitude || !$this->longitude) {
    //     //     session()->flash('error', 'Lokasi tidak valid. Pastikan izin lokasi diaktifkan.');
    //     //     return;
    //     // }

    //     $schedule = $this->getTodaySchedule();
    //     if (!$schedule) {
    //         session()->flash('error', 'No schedule found for today.');
    //         return;
    //     }

    //     // $distance = $this->isWithinRange();
    //     // if ($distance > $this->maxDistance) {
    //     //     session()->flash('error', 'You are not within the allowed range to check in. Your current location: ' .
    //     //         "{$this->latitude}, {$this->longitude}. Distance: " . round($distance, 2) . ' meters');
    //     //     return;
    //     // }

    //     try {

    //         $latestShift = Attendance::where('user_id', Auth::id())
    //             ->whereDate('attendance_date', $today)
    //             ->orderBy('shift', 'desc')
    //             ->first();

    //         $newShift = $latestShift ? $latestShift->shift + 1 : 1;

    //         $todayDay = Carbon::now()->isoFormat('dddd');
    //         $startTime = $schedule->{"{$todayDay}_start"};
    //         $endTime = $schedule->{"{$todayDay}_end"};

    //         $checkInTime = Carbon::now()->format('H:i:s');

    //         $deviceDetails = $this->deviceInfo ? json_encode($this->deviceInfo) : 'No device info';
    //         $connectionInfo = $this->pingTime ? "Connected ({$this->pingTime}ms)" : 'Connected';

    //         $attendanceData = [
    //             'user_id' => Auth::id(),
    //             'attendance_date' => $today,
    //             'check_in' => Carbon::now(),
    //             // 'check_in_location' => "{$this->latitude}, {$this->longitude}",
    //             'check_in_location' => $deviceDetails,
    //             'status' => 'hadir',
    //             'shift' => $newShift,
    //         ];

    //         if ($startTime && $checkInTime < $startTime) {
    //             $this->emit('showOvertimeModal', 'Apakah Anda bermaksud lembur?', 'check-in');
    //         } elseif ($startTime && $checkInTime > $startTime) {
    //             $attendanceData['late'] = true;
    //         }

    //         Attendance::create($attendanceData);

    //         $this->canCheckIn = false;
    //         $this->canCheckOut = true;
    //         session()->flash('success', 'Successfully checked in for shift ' . $newShift . '!');
    //     } catch (\Exception $e) {
    //         Log::error('Check In Error: ' . $e->getMessage());
    //         session()->flash('error', 'Failed to check in. Please try again.');
    //     }
    // }

    // public function checkOut()
    // {
    //     if (!$this->validateNetwork()) {
    //         session()->flash('error', 'Anda harus terhubung ke jaringan Wi-Fi / LAN Mahad Syathiby untuk melakukan absensi.');
    //         return;
    //     }

    //     $today = Carbon::now()->toDateString();

    //     // $distance = $this->isWithinRange();
    //     // if ($distance > $this->maxDistance) {
    //     //     session()->flash('error', 'You are not within the allowed range to check out. Your current location: ' .
    //     //         "{$this->latitude}, {$this->longitude}. Distance: " . round($distance, 2) . ' meters');
    //     //     return;
    //     // }

    //     try {
    //         $lastAttendance = Attendance::where('user_id', Auth::id())
    //             ->whereDate('attendance_date', $today)
    //             ->whereNull('check_out')
    //             ->latest()
    //             ->first();

    //         if ($lastAttendance) {

    //             $schedule = $this->getTodaySchedule();
    //             $todayDay = Carbon::now()->isoFormat('dddd');
    //             $startTime = $schedule->{"{$todayDay}_start"};
    //             $endTime = $schedule->{"{$todayDay}_end"};

    //             $checkOutTime = Carbon::now()->format('H:i:s');

    //             $updateData = [
    //                 'check_out' => Carbon::now(),
    //                 // 'check_out_location' => "{$this->latitude}, {$this->longitude}",
    //                 'check_out_location' => $this->pingTime ? "Connected ({$this->pingTime}ms)" : 'Connected',
    //             ];

    //             if ($endTime && $checkOutTime > $endTime) {
    //                 $this->emit('showOvertimeModal', 'Apakah Anda bermaksud lembur?', 'check-out');
    //             } elseif ($endTime && $checkOutTime < $endTime) {
    //                 $updateData['early_leave'] = true;
    //             }

    //             $lastAttendance->update($updateData);

    //             $this->canCheckIn = true;
    //             $this->canCheckOut = false;
    //             session()->flash('success', 'Successfully checked out for shift ' . $lastAttendance->shift . '!');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Check Out Error: ' . $e->getMessage());
    //         session()->flash('error', 'Failed to check out. Please try again.');
    //     }
    // }
    public function checkIn()
    {
        if (!$this->validateNetwork()) {
            // session()->flash('error', 'Anda harus terhubung ke jaringan Wi-Fi / LAN Mahad Syathiby untuk melakukan absensi.');
            return;
        }

        $schedule = $this->getTodaySchedule();
        if (!$schedule) {
            session()->flash('error', 'No schedule found for today.');
            return;
        }

        try {
            $latestShift = $this->todayAttendance ? $this->todayAttendance->shift : 0;
            $newShift = $latestShift + 1;

            $todayDay = Carbon::now()->isoFormat('dddd');
            $startTime = $schedule->{"{$todayDay}_start"};
            $checkInTime = Carbon::now()->format('H:i:s');

            $deviceDetails = $this->deviceInfo ? json_encode($this->deviceInfo) : 'No device info';

            $attendanceData = [
                'user_id' => Auth::id(),
                'attendance_date' => Carbon::today(),
                'check_in' => Carbon::now(),
                'check_in_location' => $deviceDetails,
                'status' => 'hadir',
                'shift' => $newShift,
            ];

            if ($startTime && $checkInTime < $startTime) {
                $this->emit('showOvertimeModal', 'Apakah Anda bermaksud lembur?', 'check-in');
            } elseif ($startTime && $checkInTime > $startTime) {
                $attendanceData['late'] = true;
            }

            Attendance::create($attendanceData);

            $this->loadTodayAttendance();
            $this->checkAttendanceStatus();

            session()->flash('success', 'Successfully checked in for shift ' . $newShift . '!');
        } catch (\Exception $e) {
            Log::error('Check In Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to check in. Please try again.');
        }
    }

    public function checkOut()
    {
        if (!$this->validateNetwork()) {
            // session()->flash('error', 'Anda harus terhubung ke jaringan Wi-Fi / LAN Mahad Syathiby untuk melakukan absensi.');
            return;
        }

        $schedule = $this->getTodaySchedule();
        if (!$schedule) {
            session()->flash('error', 'No schedule found for today.');
            return;
        }

        if (!$this->todayAttendance) {
            session()->flash('error', 'No active attendance found for today.');
            return;
        }

        try {
            $todayDay = Carbon::now()->isoFormat('dddd');
            $endTime = $schedule->{"{$todayDay}_end"};
            $checkOutTime = Carbon::now()->format('H:i:s');

            $deviceDetails = $this->deviceInfo ? json_encode($this->deviceInfo) : 'No device info';

            $updateData = [
                'check_out' => Carbon::now(),
                'check_out_location' => $deviceDetails,
            ];

            if ($endTime) {
                if ($checkOutTime > $endTime) {
                    $this->emit('showOvertimeModal', 'Apakah Anda bermaksud lembur?', 'check-out');
                } elseif ($checkOutTime < $endTime) {
                    $updateData['early_leave'] = true;
                }
            }

            $this->todayAttendance->update($updateData);
            $this->loadTodayAttendance();
            $this->checkAttendanceStatus();

            session()->flash('success', 'Successfully checked out!');
        } catch (\Exception $e) {
            Log::error('Check Out Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to check out. Please try again.');
        }
    }
    // public function checkOut()
    // {
    //     if (!$this->validateNetwork()) {
    //         session()->flash('error', 'Anda harus terhubung ke jaringan Wi-Fi / LAN Mahad Syathiby untuk melakukan absensi.');
    //         return;
    //     }

    //     if (!$this->todayAttendance) {
    //         session()->flash('error', 'No active attendance found.');
    //         return;
    //     }

    //     try {
    //         $schedule = $this->getTodaySchedule();
    //         $todayDay = Carbon::now()->isoFormat('dddd');
    //         $endTime = $schedule->{"{$todayDay}_end"};
    //         $checkOutTime = Carbon::now()->format('H:i:s');

    //         $updateData = [
    //             'check_out' => Carbon::now(),
    //             'check_out_location' => $this->pingTime ? "Connected ({$this->pingTime}ms)" : 'Connected',
    //         ];

    //         if ($endTime && $checkOutTime > $endTime) {
    //             $this->emit('showOvertimeModal', 'Apakah Anda bermaksud lembur?', 'check-out');
    //         } elseif ($endTime && $checkOutTime < $endTime) {
    //             $updateData['early_leave'] = true;
    //         }

    //         $this->todayAttendance->update($updateData);

    //         $this->loadTodayAttendance();
    //         $this->checkAttendanceStatus();

    //         session()->flash('success', 'Successfully checked out!');
    //     } catch (\Exception $e) {
    //         Log::error('Check Out Error: ' . $e->getMessage());
    //         session()->flash('error', 'Failed to check out. Please try again.');
    //     }
    // }

    // public function handleDeviceInfoUpdate($info)
    // {
    //     if (empty($info)) {
    //         Log::warning('Empty device info received');
    //         return;
    //     }

    //     $this->deviceInfo = $info;
    //     Log::info('Device info updated', ['info' => $info]);
    // }
    public function handleDeviceInfoUpdate($info)
    {
        Log::info('Device info update received', [
            'info' => $info,
            'isArray' => is_array($info)
        ]);

        if (empty($info)) {
            Log::warning('Empty device info received');
            return;
        }

        $deviceInfo = is_array($info) ? $info : json_decode($info, true);

        $this->deviceInfo = $deviceInfo;
        $this->isInNetwork = true;

        Log::info('Device info updated', [
            'deviceInfo' => $this->deviceInfo,
            'isInNetwork' => $this->isInNetwork
        ]);
    }
    public function validateNetwork()
    {

        Log::info('Network Validation Started', [
            'isInNetwork' => $this->isInNetwork,
            'deviceInfo' => $this->deviceInfo,
            'pingTime' => $this->pingTime
        ]);

        if (!$this->isInNetwork) {
            $deviceDetails = $this->deviceInfo ? json_encode($this->deviceInfo) : 'No device info available';
            Log::warning('Network validation failed', [
                'device_info' => $deviceDetails,
                'isInNetwork' => $this->isInNetwork
            ]);
            session()->flash('error', 'Afwan, absen hanya bisa menggunakan jaringan Wi-Fi / LAN Mahad Syathiby.');
            return false;
        }

        try {
            $isServerAccessible = NetworkUtils::isLocalServerAccessible();
            if (!$isServerAccessible) {
                Log::warning('Local server not accessible');
                session()->flash('error', 'Afwan, absen hanya bisa menggunakan jaringan Wi-Fi / LAN Mahad Syathiby.');
                return false;
            }

            Log::info('Network validation successful');
            return true;
        } catch (\Exception $e) {
            Log::error('Network validation error: ' . $e->getMessage());
            session()->flash('error', 'Gagal melakukan validasi jaringan. Silakan coba lagi.');
            return false;
        }
    }

    // public function handleOvertimeConfirmation($isOvertime, $type)
    // {
    //     if ($isOvertime) {
    //         if ($type === 'check-in') {

    //             Attendance::where('user_id', Auth::id())
    //                 ->whereDate('attendance_date', Carbon::today())
    //                 ->latest()
    //                 ->update(['is_overtime' => true, 'over_time_in' => Carbon::now()]);
    //         } elseif ($type === 'check-out') {

    //             Attendance::where('user_id', Auth::id())
    //                 ->whereDate('attendance_date', Carbon::today())
    //                 ->latest()
    //                 ->update(['is_overtime' => true, 'over_time_out' => Carbon::now()]);
    //         }
    //     }
    // }
    public function handleOvertimeConfirmation($isOvertime, $type)
    {
        if (!$isOvertime || !$this->todayAttendance)
            return;

        if ($type === 'check-in') {
            $this->todayAttendance->update([
                'is_overtime' => true,
                'over_time_in' => Carbon::now()
            ]);
        } elseif ($type === 'check-out') {
            $this->todayAttendance->update([
                'is_overtime' => true,
                'over_time_out' => Carbon::now()
            ]);
        }

        $this->loadTodayAttendance();
    }

    private function isWithinRange()
    {
        if (!$this->latitude || !$this->longitude) {
            session()->flash('error', 'Lokasi tidak valid. Pastikan izin lokasi diaktifkan.');
            return false;
        }
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