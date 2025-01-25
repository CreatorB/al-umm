<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ExportUsers extends Component
{
    use WithFileUploads;

    public $file;
    public $users;

    public function mount()
    {

        $this->users = User::all();
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Password');
        $sheet->setCellValue('D1', 'NIP');
        $sheet->setCellValue('E1', 'Jenis Kelamin');
        $sheet->setCellValue('F1', 'Nomor Telepon');
        $sheet->setCellValue('G1', 'Jabatan');
        $sheet->setCellValue('H1', 'Bagian');
        $sheet->setCellValue('I1', 'Schedule ID');
        $sheet->setCellValue('J1', 'Lokasi Kerja');
        $sheet->setCellValue('K1', 'Tanggal Mulai');
        $sheet->setCellValue('L1', 'Tanggal Berhenti');
        $sheet->setCellValue('M1', 'Tempat Lahir');
        $sheet->setCellValue('N1', 'Tanggal Lahir');
        $sheet->setCellValue('O1', 'Pendidikan');
        $sheet->setCellValue('P1', 'Gelar');
        $sheet->setCellValue('Q1', 'Jurusan');
        $sheet->setCellValue('R1', 'Sekolah/Universitas');
        $sheet->setCellValue('S1', 'Tahun Lulus');
        $sheet->setCellValue('T1', 'Alamat');
        $sheet->setCellValue('U1', 'Alamat Email');
        $sheet->setCellValue('V1', 'Status Pegawai');
        $sheet->setCellValue('W1', 'No. Rekening');
        $sheet->setCellValue('X1', 'Gaji Pokok');
        $sheet->setCellValue('Y1', 'Status');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'al-umm_users_template.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    public function uploadData()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            array_shift($rows);

            foreach ($rows as $row) {
                User::create([
                    'name' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'password' => bcrypt($row[2] ?? 'password'),
                    'nip' => $row[3] ?? null,
                    'gender' => $row[4] ?? null,
                    'phone' => $row[5] ?? null,
                    'jabatan_id' => $row[6] ?? null,
                    'bagian_id' => $row[7] ?? null,
                    'schedule_id' => $row[8] ?? null,
                    'lokasi_kerja' => $row[9] ?? null,
                    'tgl_mulai' => $row[10] ?? null,
                    'tgl_berhenti' => $row[11] ?? null,
                    'tempat_lahir' => $row[12] ?? null,
                    'tanggal_lahir' => $row[13] ?? null,
                    'pendidikan' => $row[14] ?? null,
                    'gelar' => $row[15] ?? null,
                    'jurusan' => $row[16] ?? null,
                    'sekolah_universitas' => $row[17] ?? null,
                    'tahun_lulus_1' => $row[18] ?? null,
                    'alamat' => $row[19] ?? null,
                    'alamat_email' => $row[20] ?? null,
                    'status_pegawai' => $row[21] ?? null,
                    'no_rek' => $row[22] ?? null,
                    'gaji_pokok' => $row[23] ?? null,
                    'status' => $row[24] ?? 'inactive',
                ]);
            }

            $this->users = User::all();

            Session::flash('success', 'Data berhasil diupload!');
        } catch (\Exception $e) {

            Session::flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmDelete($userId)
    {

        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            $this->deleteUser($userId);
        }
    }
    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            Session::flash('success', 'User berhasil dihapus!');
            $this->users = User::all();
        } else {
            Session::flash('error', 'User tidak ditemukan!');
        }
    }

    public function render()
    {
        return view('livewire.admin.export-users');
    }
}