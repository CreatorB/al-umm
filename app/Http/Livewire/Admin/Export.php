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

    public function export()
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
        $sheet->setCellValue('E1', 'HARI KERJA / KEHADIRAN');
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
        $sheet->setCellValue('P1', 'CATATAN');

        // Isi data
        $row = 2;
        foreach ($users as $user) {
            // Inisialisasi total keterlambatan dan pulang cepat
            $totalKeterlambatan = 0;
            $totalPulangCepat = 0;

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
            }

            $keterlambatanFormatted = $this->convertMinutesToTime($totalKeterlambatan);
            $pulangCepatFormatted = $this->convertMinutesToTime($totalPulangCepat);

            // isi data
            $sheet->setCellValue('A' . $row, $user->nip);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->department);
            $sheet->setCellValue('D' . $row, $user->bagian);
            $sheet->setCellValue('E' . $row, $user->attendances->count() . '/' . $user->working_days);
            $sheet->setCellValue('F' . $row, $keterlambatanFormatted);
            $sheet->setCellValue('G' . $row, $pulangCepatFormatted);
            $sheet->setCellValue('H' . $row, $user->attendances->where('status', 'alpha')->count());
            $sheet->setCellValue('I' . $row, $user->attendances->where('status', 'izin')->count());
            $sheet->setCellValue('J' . $row, $user->attendances->where('status', 'sakit')->count());
            $sheet->setCellValue('K' . $row, $user->attendances->where('status', 'tugas_luar')->count());
            $sheet->setCellValue('L' . $row, $user->attendances->where('status', 'alpha')->count());
            $sheet->setCellValue('M' . $row, $user->cuti_diambil);
            $sheet->setCellValue('N' . $row, $user->sisa_cuti_sebelumnya);
            $sheet->setCellValue('O' . $row, $user->jumlah_cuti);
            $sheet->setCellValue('P' . $row, $user->catatan);
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