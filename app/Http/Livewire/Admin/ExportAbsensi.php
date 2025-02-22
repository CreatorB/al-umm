<?php
namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportAbsensi extends Component
{
    public $startDate;
    public $endDate;

    protected $rules = [
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    public function mount()
    {
        // Set default date range to current month
        // $this->startDate = now()->startOfMonth()->format('Y-m-d');
        // $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    private function convertMinutesToTime($minutes)
    {
        $days = floor($minutes / (60 * 24));
        $hours = floor(($minutes % (60 * 24)) / 60);
        $mins = $minutes % 60;

        return sprintf('%d hari, %d jam, %d menit', $days, $hours, $mins);
    }

    // public function calculateLateAndEarlyLeave($attendances, $schedule)
    // {
    //     $totalLateMinutes = 0;
    //     $totalEarlyLeaveMinutes = 0;

    //     foreach ($attendances as $attendance) {

    //         if (!$schedule || empty($attendance->check_in) || empty($attendance->check_out)) {
    //             continue;
    //         }

    //         $checkInTime = Carbon::parse($attendance->check_in);
    //         $checkOutTime = Carbon::parse($attendance->check_out);

    //         $workStartTime = Carbon::parse($attendance->attendance_date . ' ' . $schedule->start_time);
    //         $workEndTime = Carbon::parse($attendance->attendance_date . ' ' . $schedule->end_time);

    //         if ($checkInTime->gt($workStartTime)) {
    //             $totalLateMinutes += $checkInTime->diffInMinutes($workStartTime);
    //         }

    //         if ($checkOutTime->lt($workEndTime)) {
    //             $totalEarlyLeaveMinutes += $workEndTime->diffInMinutes($checkOutTime);
    //         }
    //     }

    //     return [
    //         'totalLateMinutes' => $totalLateMinutes,
    //         'totalEarlyLeaveMinutes' => $totalEarlyLeaveMinutes,
    //     ];
    // }
    public function calculateLateAndEarlyLeave($attendances, $schedule)
    {
        $totalLateMinutes = 0;
        $totalEarlyLeaveMinutes = 0;

        foreach ($attendances as $attendance) {
            if (!$schedule || empty($attendance->check_in) || empty($attendance->check_out)) {
                continue;
            }

            $attendanceDate = Carbon::parse($attendance->attendance_date)->format('Y-m-d');

            // Parse jadwal dengan tanggal yang benar
            $workStartTime = Carbon::parse($attendanceDate . ' ' . $schedule->start_time);
            $workEndTime = Carbon::parse($attendanceDate . ' ' . $schedule->end_time);

            // Parse check in dan check out
            $checkInTime = Carbon::parse($attendance->check_in);
            $checkOutTime = Carbon::parse($attendance->check_out);

            // Hitung keterlambatan
            if ($checkInTime->gt($workStartTime)) {
                $totalLateMinutes += $checkInTime->diffInMinutes($workStartTime);
            }

            // Hitung pulang cepat
            if ($checkOutTime->lt($workEndTime)) {
                $totalEarlyLeaveMinutes += $workEndTime->diffInMinutes($checkOutTime);
            }
        }

        return [
            'totalLateMinutes' => $totalLateMinutes,
            'totalEarlyLeaveMinutes' => $totalEarlyLeaveMinutes,
        ];
    }

    // public function exportAbsen()
    // {
    //     $this->validate();

    //     Carbon::setLocale('id');

    //     $attendances = Attendance::with('user')
    //         ->whereBetween('attendance_date', [$this->startDate, $this->endDate])
    //         ->get();

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('Data Absensi');

    //     $startDate = Carbon::parse($this->startDate);
    //     $endDate = Carbon::parse($this->endDate);

    //     $sheet->setCellValue('A1', 'ID PEGAWAI');
    //     $sheet->setCellValue('B1', 'NAMA PEGAWAI');
    //     $sheet->setCellValue('C1', 'BAGIAN');

    //     $col = 'D';
    //     $currentDate = $startDate->copy();

    //     while ($currentDate->lte($endDate)) {
    //         $sheet->setCellValue($col . '1', $currentDate->day);

    //         $hari = $currentDate->isoFormat('dddd');
    //         $sheet->setCellValue($col . '2', strtoupper(substr($hari, 0, 3)));

    //         $col++;
    //         $currentDate->addDay();
    //     }

    //     $row = 3;
    //     $users = User::with([
    //         'attendances' => function ($query) use ($startDate, $endDate) {
    //             $query->whereBetween('attendance_date', [$startDate, $endDate]);
    //         }
    //     ])->get();

    //     foreach ($users as $user) {
    //         $sheet->setCellValue('A' . $row, $user->nip);
    //         $sheet->setCellValue('B' . $row, $user->name);
    //         $sheet->setCellValue('C' . $row, $user->bagian);

    //         $col = 'D';
    //         $currentDate = $startDate->copy();
    //         while ($currentDate->lte($endDate)) {
    //             // Ambil semua attendance untuk tanggal tertentu
    //             $attendances = $user->attendances->where('attendance_date', $currentDate->toDateString());

    //             // Jika ada attendance
    //             if ($attendances->isNotEmpty()) {
    //                 $attendanceData = [];
    //                 foreach ($attendances as $attendance) {
    //                     $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '0';
    //                     $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '0';

    //                     // Tambahkan data check-in dan check-out ke array
    //                     if ($checkIn != '0' && $checkOut != '0') {
    //                         $attendanceData[] = $checkIn . ' - ' . $checkOut;
    //                     }

    //                     // Jika ada overtime, tambahkan informasi lembur
    //                     if ($attendance->over_time_in) {
    //                         $overtimeIn = $attendance->over_time_in ? Carbon::parse($attendance->over_time_in)->format('H:i') : '0';
    //                         $overtimeOut = $attendance->over_time_out ? Carbon::parse($attendance->over_time_out)->format('H:i') : '0';
    //                         $attendanceData[] = '(Lembur: ' . $overtimeIn . ' - ' . $overtimeOut . ')';
    //                     }
    //                 }

    //                 // Gabungkan data dengan pemisah koma atau baris baru
    //                 $sheet->setCellValue($col . $row, implode("\n", $attendanceData)); // Gunakan "\n" untuk baris baru
    //                 $sheet->getStyle($col . $row)->getAlignment()->setWrapText(true); // Aktifkan wrap text
    //             }

    //             $col++;
    //             $currentDate->addDay();
    //         }
    //         $row++;
    //     }

    //     $writer = new Xlsx($spreadsheet);
    //     $fileName = 'al-umm_data_absensi_' . now()->format('Y-m-d') . '.xlsx';
    //     $writer->save($fileName);

    //     return response()->download($fileName)->deleteFileAfterSend(true);
    // }

    public function exportAbsen()
    {
        $this->validate();
        Carbon::setLocale('id');

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Debug
        Log::info("Direct Check Attendance", [
            'date_range' => [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')],
            'sample_data' => Attendance::whereBetween('attendance_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])->first()
        ]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Absensi');

        // Headers
        $sheet->setCellValue('A1', 'ID PEGAWAI');
        $sheet->setCellValue('B1', 'NAMA PEGAWAI');
        $sheet->setCellValue('C1', 'BAGIAN');

        // Date columns
        $col = 'D';
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $sheet->setCellValue($col . '1', $currentDate->day);
            $hari = $currentDate->isoFormat('dddd');
            $sheet->setCellValue($col . '2', strtoupper(substr($hari, 0, 3)));
            $col++;
            $currentDate->addDay();
        }

        // Get all users
        $users = User::all();

        $row = 3;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->bagian);

            $col = 'D';
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                // Query attendance langsung untuk setiap tanggal
                $attendances = Attendance::where('user_id', $user->id)
                    ->where('attendance_date', $currentDate->format('Y-m-d'))
                    ->orderBy('check_in')
                    ->get();

                if ($attendances->isNotEmpty()) {
                    $attendanceData = [];
                    foreach ($attendances as $attendance) {
                        $timeStr = '';

                        // Check in time
                        if ($attendance->check_in) {
                            $timeStr .= Carbon::parse($attendance->check_in)->format('H:i');
                        }

                        // Check out time
                        if ($attendance->check_out) {
                            $timeStr .= ' - ' . Carbon::parse($attendance->check_out)->format('H:i');
                        }

                        // Shift number
                        if ($attendance->shift) {
                            $timeStr .= " (S{$attendance->shift})";
                        }

                        if ($timeStr) {
                            $attendanceData[] = $timeStr;
                        }

                        // Overtime
                        if ($attendance->is_overtime && $attendance->over_time_in && $attendance->over_time_out) {
                            $overtimeIn = Carbon::parse($attendance->over_time_in)->format('H:i');
                            $overtimeOut = Carbon::parse($attendance->over_time_out)->format('H:i');
                            $attendanceData[] = "(Lembur: $overtimeIn - $overtimeOut)";
                        }
                    }

                    if (!empty($attendanceData)) {
                        $sheet->setCellValue($col . $row, implode("\n", $attendanceData));
                        $sheet->getStyle($col . $row)
                            ->getAlignment()
                            ->setWrapText(true)
                            ->setVertical('center')
                            ->setHorizontal('center');
                    } else {
                        $sheet->setCellValue($col . $row, '-');
                    }
                } else {
                    $sheet->setCellValue($col . $row, '-');
                }

                $col++;
                $currentDate->addDay();
            }
            $row++;
        }

        $highestColumn = $sheet->getHighestColumn();
        $columnIterator = $sheet->getColumnIterator('A', $highestColumn);
        foreach ($columnIterator as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'al-umm_data_absensi_' . now()->format('Y-m-d') . '.xlsx';
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
            'schedule',
            'department',
            'part'
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

            // Pastikan tanggal dalam format string yang konsisten
            $groupedAttendances = [];
            foreach ($user->attendances as $attendance) {
                // Konversi ke format Y-m-d untuk memastikan konsistensi
                $dateKey = Carbon::parse($attendance->attendance_date)->format('Y-m-d');
                if (!isset($groupedAttendances[$dateKey])) {
                    $groupedAttendances[$dateKey] = [];
                }
                $groupedAttendances[$dateKey][] = $attendance;
            }

            $hadir = 0;
            $izin = 0;
            $sakit = 0;
            $tugasLuar = 0;
            $alpha = 0;
            $lemburCount = 0;

            foreach ($groupedAttendances as $dateKey => $attendances) {
                $statusCounted = false;
                $day = Carbon::parse($dateKey)->isoFormat('dddd');

                $earliestCheckIn = null;
                $latestCheckOut = null;

                foreach ($attendances as $attendance) {
                    if (!empty($attendance->check_in)) {
                        $checkIn = Carbon::parse($attendance->check_in);
                        if (!$earliestCheckIn || $checkIn->lt($earliestCheckIn)) {
                            $earliestCheckIn = $checkIn;
                        }
                    }

                    if (!empty($attendance->check_out)) {
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
            $sisaSaatIni = $user->jumlah_cuti;
            $sisaSebelumnya = $sisaSaatIni + $cutiDiambil;

            // Fill the sheet
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->department->name ?? '-');
            $sheet->setCellValue('D' . $row, $user->part->name ?? '-');
            $sheet->setCellValue('E' . $row, $hadir + $cutiDiambil . '/' . $user->working_days);
            $sheet->setCellValue('F' . $row, $keterlambatanFormatted);
            $sheet->setCellValue('G' . $row, $pulangCepatFormatted);
            $sheet->setCellValue('H' . $row, $absen);
            $sheet->setCellValue('I' . $row, $izin);
            $sheet->setCellValue('J' . $row, $sakit);
            $sheet->setCellValue('K' . $row, $tugasLuar);
            $sheet->setCellValue('L' . $row, $alpha);
            $sheet->setCellValue('M' . $row, $cutiDiambil);
            $sheet->setCellValue('N' . $row, $sisaSebelumnya);
            $sheet->setCellValue('O' . $row, $sisaSaatIni);
            $sheet->setCellValue('P' . $row, $lemburCount);
            $sheet->setCellValue('Q' . $row, $waktuLemburFormatted);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'al-umm_rekap_absen_' . now()->format('Y-m-d') . '.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    // public function exportRekap()
    // {
    //     $this->validate();

    //     $users = User::with([
    //         'attendances' => function ($query) {
    //             $query->whereBetween('attendance_date', [$this->startDate, $this->endDate]);
    //         },
    //         'schedule',
    //         'department',
    //         'part'
    //     ])->get();

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('Rekap Absen');

    //     $sheet->setCellValue('A1', 'ID PEGAWAI');
    //     $sheet->setCellValue('B1', 'NAMA PEGAWAI');
    //     $sheet->setCellValue('C1', 'DEPARTMENT');
    //     $sheet->setCellValue('D1', 'BAGIAN');
    //     $sheet->setCellValue('E1', 'KEHADIRAN / HARI KERJA');
    //     $sheet->setCellValue('F1', 'KETERLAMBATAN');
    //     $sheet->setCellValue('G1', 'PULANG CEPAT');
    //     $sheet->setCellValue('H1', 'ABSEN');
    //     $sheet->setCellValue('I1', 'IZIN');
    //     $sheet->setCellValue('J1', 'SAKIT');
    //     $sheet->setCellValue('K1', 'TUGAS LUAR');
    //     $sheet->setCellValue('L1', 'ALPA');
    //     $sheet->setCellValue('M1', 'CUTI DIAMBIL');
    //     $sheet->setCellValue('N1', 'SISA SEBELUMNYA');
    //     $sheet->setCellValue('O1', 'SISA SAAT INI');
    //     $sheet->setCellValue('P1', 'LEMBUR');
    //     $sheet->setCellValue('Q1', 'WAKTU LEMBUR');

    //     $row = 2;
    //     foreach ($users as $user) {
    //         $totalLateMinutes = 0;
    //         $totalEarlyLeaveMinutes = 0;
    //         $totalWaktuLembur = 0;

    //         $groupedAttendances = [];
    //         foreach ($user->attendances as $attendance) {
    //             $date = $attendance->attendance_date;
    //             if (!isset($groupedAttendances[$date])) {
    //                 $groupedAttendances[$date] = [];
    //             }
    //             $groupedAttendances[$date][] = $attendance;
    //         }

    //         $hadir = 0;
    //         $izin = 0;
    //         $sakit = 0;
    //         $tugasLuar = 0;
    //         $alpha = 0;
    //         $lemburCount = 0;

    //         foreach ($groupedAttendances as $date => $attendances) {
    //             $statusCounted = false;

    //             $day = Carbon::parse($date)->isoFormat('dddd');

    //             $earliestCheckIn = null;
    //             $latestCheckOut = null;
    //             foreach ($attendances as $attendance) {

    //                 if (!empty($attendance->check_in) && Carbon::parse($attendance->check_in)->isSameDay($date)) {
    //                     $checkIn = Carbon::parse($attendance->check_in);
    //                     if (!$earliestCheckIn || $checkIn->lt($earliestCheckIn)) {
    //                         $earliestCheckIn = $checkIn;
    //                     }
    //                 }
    //                 if (!empty($attendance->check_out) && Carbon::parse($attendance->check_out)->isSameDay($date)) {
    //                     $checkOut = Carbon::parse($attendance->check_out);
    //                     if (!$latestCheckOut || $checkOut->gt($latestCheckOut)) {
    //                         $latestCheckOut = $checkOut;
    //                     }
    //                 }
    //             }

    //             foreach ($attendances as $attendance) {
    //                 if (!$statusCounted) {
    //                     switch ($attendance->status) {
    //                         case 'hadir':
    //                             $hadir++;
    //                             break;
    //                         case 'izin':
    //                             $izin++;
    //                             break;
    //                         case 'sakit':
    //                             $sakit++;
    //                             break;
    //                         case 'tugas_luar':
    //                             $tugasLuar++;
    //                             break;
    //                         case 'alpha':
    //                             $alpha++;
    //                             break;
    //                     }
    //                     $statusCounted = true;
    //                 }

    //                 if ($user->schedule) {
    //                     $schedule = (object) [
    //                         'start_time' => $user->schedule->{strtolower($day) . '_start'} ?? null,
    //                         'end_time' => $user->schedule->{strtolower($day) . '_end'} ?? null,
    //                     ];

    //                     $lateAndEarlyLeave = $this->calculateLateAndEarlyLeave([$attendance], $schedule);
    //                     $totalLateMinutes += $lateAndEarlyLeave['totalLateMinutes'];
    //                     $totalEarlyLeaveMinutes += $lateAndEarlyLeave['totalEarlyLeaveMinutes'];
    //                 }

    //                 if ($attendance->is_overtime && $attendance->over_time_in && $attendance->over_time_out) {
    //                     $overtimeIn = Carbon::parse($attendance->over_time_in);
    //                     $overtimeOut = Carbon::parse($attendance->over_time_out);
    //                     $totalWaktuLembur += $overtimeOut->diffInMinutes($overtimeIn);
    //                     $lemburCount++;
    //                 }
    //             }
    //         }

    //         $absen = $izin + $sakit + $tugasLuar + $alpha;

    //         $keterlambatanFormatted = $this->convertMinutesToTime($totalLateMinutes);
    //         $pulangCepatFormatted = $this->convertMinutesToTime($totalEarlyLeaveMinutes);
    //         $waktuLemburFormatted = $this->convertMinutesToTime($totalWaktuLembur);

    //         $cutiDiambil = $user->attendances->where('status', 'cuti')->count();
    //         $sisaSaatIni = $user->jumlah_cuti;
    //         $sisaSebelumnya = $sisaSaatIni + $cutiDiambil;

    //         $sheet->setCellValue('A' . $row, $user->nip);
    //         $sheet->setCellValue('B' . $row, $user->name);
    //         $sheet->setCellValue('C' . $row, $user->department->name ?? '-');
    //         $sheet->setCellValue('D' . $row, $user->bagian->name ?? '-');
    //         $sheet->setCellValue('E' . $row, $hadir + $user->attendances->where('status', 'cuti')->count() . '/' . $user->working_days);
    //         $sheet->setCellValue('F' . $row, $keterlambatanFormatted);
    //         $sheet->setCellValue('G' . $row, $pulangCepatFormatted);
    //         $sheet->setCellValue('H' . $row, $absen);
    //         $sheet->setCellValue('I' . $row, $izin);
    //         $sheet->setCellValue('J' . $row, $sakit);
    //         $sheet->setCellValue('K' . $row, $tugasLuar);
    //         $sheet->setCellValue('L' . $row, $alpha);
    //         $sheet->setCellValue('M' . $row, $cutiDiambil);
    //         $sheet->setCellValue('N' . $row, $sisaSebelumnya);
    //         $sheet->setCellValue('O' . $row, $sisaSaatIni);
    //         $sheet->setCellValue('P' . $row, $lemburCount);
    //         $sheet->setCellValue('Q' . $row, $waktuLemburFormatted);
    //         $row++;
    //     }

    //     $writer = new Xlsx($spreadsheet);
    //     $fileName = 'al-umm_rekap_absen_' . now()->format('Y-m-d') . '.xlsx';
    //     $writer->save($fileName);

    //     return response()->download($fileName)->deleteFileAfterSend(true);
    // }

    public function render()
    {
        return view('livewire.admin.export-absen');
    }
}