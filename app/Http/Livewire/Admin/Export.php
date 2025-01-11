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
        $secs = 0;

        return sprintf('%d hari, %d jam, %d menit, %d detik', $days, $hours, $mins, $secs);
    }

    public function exportAbsen()
    {
        $this->validate();

        Carbon::setLocale('id');

        // Ambil data absensi berdasarkan rentang tanggal
        $attendances = Attendance::with('user')
            ->whereBetween('attendance_date', [$this->startDate, $this->endDate])
            ->get();

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Absensi');

        // Header Tanggal (26 hingga 25 bulan berikutnya)
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Set header kolom
        $sheet->setCellValue('A1', 'ID PEGAWAI');
        $sheet->setCellValue('B1', 'NAMA PEGAWAI');
        $sheet->setCellValue('C1', 'BAGIAN');

        $col = 'D'; // Mulai dari kolom D
        $currentDate = $startDate->copy();
        // while ($currentDate->lte($endDate)) {
        //     $sheet->setCellValue($col . '1', $currentDate->day); // Tanggal (26, 27, ..., 25)
        //     $sheet->setCellValue($col . '2', $currentDate->isoFormat('ddd')); // Hari (SEN, SEL, RAB, ...)
        //     $col++;
        //     $currentDate->addDay();
        // }
        while ($currentDate->lte($endDate)) {
            $sheet->setCellValue($col . '1', $currentDate->day); // Tanggal (26, 27, ..., 25)

            // Format hari dalam bahasa Indonesia
            $hari = $currentDate->isoFormat('dddd'); // Mengambil nama hari (Senin, Selasa, ...)
            $sheet->setCellValue($col . '2', strtoupper(substr($hari, 0, 3))); // Ambil 3 huruf pertama (SEN, SEL, ...)

            $col++;
            $currentDate->addDay();
        }

        // Isi data
        $row = 3; // Mulai dari baris ke-3
        $users = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('attendance_date', [$startDate, $endDate]);
            }
        ])->get();

        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->bagian);

            $col = 'D'; // Mulai dari kolom D
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

        // Simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_absensi_' . now()->format('Y-m-d') . '.xlsx';
        $writer->save($fileName);

        // Download file
        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    public function exportRekap()
    {
        $this->validate();

        // Ambil data berdasarkan rentang tanggal
        $users = User::with([
            'attendances' => function ($query) {
                $query->whereBetween('attendance_date', [$this->startDate, $this->endDate]);
            }
        ])->get();

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absen');

        // Header
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

        // Isi data
        $row = 2;
        foreach ($users as $user) {
            // Inisialisasi total keterlambatan dan pulang cepat
            $totalKeterlambatan = 0;
            $totalPulangCepat = 0;
            $totalWaktuLembur = 0;

            // Hitung keterlambatan dan pulang cepat
            foreach ($user->attendances as $attendance) {
                if ($attendance->check_in && $user->working_time_start) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $workingStart = Carbon::parse($attendance->attendance_date . ' ' . $user->working_time_start);

                    // Hitung keterlambatan dalam menit
                    if ($checkIn->gt($workingStart)) {
                        $totalKeterlambatan += $checkIn->diffInMinutes($workingStart);
                    }
                }

                if ($attendance->check_out && $user->working_time_end) {
                    $checkOut = Carbon::parse($attendance->check_out);
                    $workingEnd = Carbon::parse($attendance->attendance_date . ' ' . $user->working_time_end);

                    // Hitung pulang cepat dalam menit
                    if ($checkOut->lt($workingEnd)) {
                        $totalPulangCepat += $workingEnd->diffInMinutes($checkOut);
                    }
                }

                if ($attendance->over_time_in && $attendance->over_time_out) {
                    $overtimeIn = Carbon::parse($attendance->over_time_in);
                    $overtimeOut = Carbon::parse($attendance->over_time_out);
                    $totalWaktuLembur += $overtimeOut->diffInMinutes($overtimeIn);
                }
            }

            $keterlambatanFormatted = $this->convertMinutesToTime($totalKeterlambatan);
            $pulangCepatFormatted = $this->convertMinutesToTime($totalPulangCepat);
            $waktuLemburFormatted = $this->convertMinutesToTime($totalWaktuLembur);

            // Hitung cuti diambil
            $cutiDiambil = $user->attendances->where('status', 'cuti')->count();

            // Hitung sisa saat ini
            $sisaSaatIni = $user->jumlah_cuti - $cutiDiambil;

            $lemburCount = $user->attendances->whereNotNull('over_time_in')->count();
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->department);
            $sheet->setCellValue('D' . $row, $user->bagian);
            $kehadiran = $user->attendances->where('status', 'hadir')->count() + $user->attendances->where('status', 'cuti')->count();
            $sheet->setCellValue('E' . $row, $kehadiran . '/' . $user->working_days);
            $sheet->setCellValue('F' . $row, $keterlambatanFormatted);
            $sheet->setCellValue('G' . $row, $pulangCepatFormatted);
            $absen = $user->attendances->where('status', 'izin')->count() + $user->attendances->where('status', 'sakit')->count() + $user->attendances->where('status', 'tugas_luar')->count() + $user->attendances->where('status', 'alpha')->count();
            $sheet->setCellValue('H' . $row, $absen);
            $sheet->setCellValue('I' . $row, $user->attendances->where('status', 'izin')->count());
            $sheet->setCellValue('J' . $row, $user->attendances->where('status', 'sakit')->count());
            $sheet->setCellValue('K' . $row, $user->attendances->where('status', 'tugas_luar')->count());
            $sheet->setCellValue('L' . $row, $user->attendances->where('status', 'alpha')->count());
            $sheet->setCellValue('M' . $row, $cutiDiambil);
            $sheet->setCellValue('N' . $row, $user->jumlah_cuti);
            $sheet->setCellValue('O' . $row, $sisaSaatIni);
            $sheet->setCellValue('P' . $row, $lemburCount);
            $sheet->setCellValue('Q' . $row, $waktuLemburFormatted);
            $row++;
        }

        // Simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'rekap_absen_' . now()->format('Y-m-d') . '.xlsx';
        $writer->save($fileName);

        // Download file
        return response()->download($fileName)->deleteFileAfterSend(true);
    }



    public function render()
    {
        return view('livewire.admin.export-absen');
    }
}