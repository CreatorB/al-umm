<?php
namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Export extends Component
{
    public $startDate;
    public $endDate;

    protected $rules = [
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    private function convertMinutesToTime($minutes)
    {
        $days = floor($minutes / (60 * 24));
        $hours = floor(($minutes % (60 * 24)) / 60);
        $mins = $minutes % 60;

        return sprintf('%d hari, %d jam, %d menit', $days, $hours, $mins);
    }

    public function calculateLateAndEarlyLeave($attendances, $schedule)
    {
        $totalLateMinutes = 0;
        $totalEarlyLeaveMinutes = 0;

        foreach ($attendances as $attendance) {

            if (!$schedule || empty($attendance->check_in) || empty($attendance->check_out)) {
                continue;
            }

            $checkInTime = Carbon::parse($attendance->check_in);
            $checkOutTime = Carbon::parse($attendance->check_out);

            $workStartTime = Carbon::parse($attendance->attendance_date . ' ' . $schedule->start_time);
            $workEndTime = Carbon::parse($attendance->attendance_date . ' ' . $schedule->end_time);

            if ($checkInTime->gt($workStartTime)) { 
                $totalLateMinutes += $checkInTime->diffInMinutes($workStartTime);
            }

            if ($checkOutTime->lt($workEndTime)) { 
                $totalEarlyLeaveMinutes += $workEndTime->diffInMinutes($checkOutTime);
            }
        }

        return [
            'totalLateMinutes' => $totalLateMinutes,
            'totalEarlyLeaveMinutes' => $totalEarlyLeaveMinutes,
        ];
    }

    public function exportAbsen()
    {
        $this->validate();

        Carbon::setLocale('id');

        $attendances = Attendance::with('user')
            ->whereBetween('attendance_date', [$this->startDate, $this->endDate])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Absensi');

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $sheet->setCellValue('A1', 'ID PEGAWAI');
        $sheet->setCellValue('B1', 'NAMA PEGAWAI');
        $sheet->setCellValue('C1', 'BAGIAN');

        $col = 'D'; 
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $sheet->setCellValue($col . '1', $currentDate->day); 

            $hari = $currentDate->isoFormat('dddd'); 
            $sheet->setCellValue($col . '2', strtoupper(substr($hari, 0, 3))); 

            $col++;
            $currentDate->addDay();
        }

        $row = 3; 
        $users = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('attendance_date', [$startDate, $endDate]);
            }
        ])->get();

        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->bagian);

            $col = 'D'; 
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $attendance = $user->attendances->firstWhere('attendance_date', $currentDate->toDateString());
                if ($attendance) {
                    $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '0';
                    $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '0';
                    if ($attendance->over_time_in) {
                        $overtimeIn = $attendance->over_time_in ? Carbon::parse($attendance->over_time_in)->format('H:i') : '0';
                        $overtimeOut = $attendance->over_time_out ? Carbon::parse($attendance->over_time_out)->format('H:i') : '0';
                        $sheet->setCellValue($col . $row, $checkIn . ' - ' . $checkOut . ' (Lembur : ' . $overtimeIn . ' - ' . $overtimeOut . ')');
                    } else {
                        $sheet->setCellValue($col . $row, $checkIn . ' - ' . $checkOut);
                    }
                }
                $col++;
                $currentDate->addDay();
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_absensi_' . now()->format('Y-m-d') . '.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    public function exportRekap()
    {
        $this->validate();

        $users = User::with([
            'attendances' => function ($query) {
                $query->whereBetween('attendance_date', [$this->startDate, $this->endDate]);
            },
            'schedule' 
        ])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absen');

        $sheet->setCellValue('A1', 'ID PEGAWAI');
        $sheet->setCellValue('B1', 'NAMA PEGAWAI');
        $sheet->setCellValue('C1', 'DEPARTMENT');
        $sheet->setCellValue('D1', 'BAGIAN');
        $sheet->setCellValue('E1', 'KEHADIRAN / HARI KERJA');
        $sheet->setCellValue('F1', 'KETERLAMBATAN');
        $sheet->setCellValue('G1', 'PULANG CEPAT');
        $sheet->setCellValue('H1', 'ABSEN');
        $sheet->setCellValue('I1', 'IZIN');
        $sheet->setCellValue('J1', 'SAKIT');
        $sheet->setCellValue('K1', 'TUGAS LUAR');
        $sheet->setCellValue('L1', 'ALPA');
        $sheet->setCellValue('M1', 'CUTI DIAMBIL');
        $sheet->setCellValue('N1', 'SISA SEBELUMNYA');
        $sheet->setCellValue('O1', 'SISA SAAT INI');
        $sheet->setCellValue('P1', 'LEMBUR');
        $sheet->setCellValue('Q1', 'WAKTU LEMBUR');

        $row = 2;
        foreach ($users as $user) {
            $totalLateMinutes = 0;
            $totalEarlyLeaveMinutes = 0;
            $totalWaktuLembur = 0;

            $groupedAttendances = [];
            foreach ($user->attendances as $attendance) {
                $date = $attendance->attendance_date;
                if (!isset($groupedAttendances[$date])) {
                    $groupedAttendances[$date] = [];
                }
                $groupedAttendances[$date][] = $attendance;
            }

            $hadir = 0;
            $izin = 0;
            $sakit = 0;
            $tugasLuar = 0;
            $alpha = 0;
            $lemburCount = 0;

            foreach ($groupedAttendances as $date => $attendances) {
                $statusCounted = false; 

                $day = Carbon::parse($date)->isoFormat('dddd');

                $earliestCheckIn = null;
                $latestCheckOut = null;
                foreach ($attendances as $attendance) {

                    if (!empty($attendance->check_in) && Carbon::parse($attendance->check_in)->isSameDay($date)) {
                        $checkIn = Carbon::parse($attendance->check_in);
                        if (!$earliestCheckIn || $checkIn->lt($earliestCheckIn)) {
                            $earliestCheckIn = $checkIn;
                        }
                    }
                    if (!empty($attendance->check_out) && Carbon::parse($attendance->check_out)->isSameDay($date)) {
                        $checkOut = Carbon::parse($attendance->check_out);
                        if (!$latestCheckOut || $checkOut->gt($latestCheckOut)) {
                            $latestCheckOut = $checkOut;
                        }
                    }
                }

                foreach ($attendances as $attendance) {
                    if (!$statusCounted) {
                        switch ($attendance->status) {
                            case 'hadir':
                                $hadir++;
                                break;
                            case 'izin':
                                $izin++;
                                break;
                            case 'sakit':
                                $sakit++;
                                break;
                            case 'tugas_luar':
                                $tugasLuar++;
                                break;
                            case 'alpha':
                                $alpha++;
                                break;
                        }
                        $statusCounted = true; 
                    }

                    if ($user->schedule) {
                        $schedule = (object) [
                            'start_time' => $user->schedule->{strtolower($day) . '_start'} ?? null,
                            'end_time' => $user->schedule->{strtolower($day) . '_end'} ?? null,
                        ];

                        $lateAndEarlyLeave = $this->calculateLateAndEarlyLeave([$attendance], $schedule);
                        $totalLateMinutes += $lateAndEarlyLeave['totalLateMinutes'];
                        $totalEarlyLeaveMinutes += $lateAndEarlyLeave['totalEarlyLeaveMinutes'];
                    }

                    if ($attendance->is_overtime && $attendance->over_time_in && $attendance->over_time_out) {
                        $overtimeIn = Carbon::parse($attendance->over_time_in);
                        $overtimeOut = Carbon::parse($attendance->over_time_out);
                        $totalWaktuLembur += $overtimeOut->diffInMinutes($overtimeIn);
                        $lemburCount++;
                    }
                }
            }

            $absen = $izin + $sakit + $tugasLuar + $alpha;

            $keterlambatanFormatted = $this->convertMinutesToTime($totalLateMinutes);
            $pulangCepatFormatted = $this->convertMinutesToTime($totalEarlyLeaveMinutes);
            $waktuLemburFormatted = $this->convertMinutesToTime($totalWaktuLembur);

            $cutiDiambil = $user->attendances->where('status', 'cuti')->count();
            $sisaSaatIni = $user->jumlah_cuti - $cutiDiambil;

            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->department);
            $sheet->setCellValue('D' . $row, $user->bagian);
            $sheet->setCellValue('E' . $row, $hadir + $user->attendances->where('status', 'cuti')->count() . '/' . $user->working_days);
            $sheet->setCellValue('F' . $row, $keterlambatanFormatted);
            $sheet->setCellValue('G' . $row, $pulangCepatFormatted);
            $sheet->setCellValue('H' . $row, $absen);
            $sheet->setCellValue('I' . $row, $izin);
            $sheet->setCellValue('J' . $row, $sakit);
            $sheet->setCellValue('K' . $row, $tugasLuar);
            $sheet->setCellValue('L' . $row, $alpha);
            $sheet->setCellValue('M' . $row, $cutiDiambil);
            $sheet->setCellValue('N' . $row, $user->jumlah_cuti);
            $sheet->setCellValue('O' . $row, $sisaSaatIni);
            $sheet->setCellValue('P' . $row, $lemburCount);
            $sheet->setCellValue('Q' . $row, $waktuLemburFormatted);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'rekap_absen_' . now()->format('Y-m-d') . '.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.export-absen');
    }
}