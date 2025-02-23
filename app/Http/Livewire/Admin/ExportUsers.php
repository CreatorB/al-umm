<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ExportUsers extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $file;
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $filters = [
        'status' => '',
        'gender' => '',
        'jabatan_id' => '',
        'bagian_id' => ''
    ];

    // Listener untuk reset pagination saat pencarian
    protected $queryString = ['search', 'sortField', 'sortDirection'];

    protected $listeners = ['delete' => 'deleteUser'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset('filters');
        $this->search = '';
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header setup
        $headers = [
            'A1' => 'Nama',
            'B1' => 'Email',
            'C1' => 'Password',
            'D1' => 'NIP',
            'E1' => 'Jenis Kelamin',
            'F1' => 'Nomor Telepon',
            'G1' => 'Jabatan',
            'H1' => 'Bagian',
            'I1' => 'Schedule ID',
            'J1' => 'Lokasi Kerja',
            'K1' => 'Tanggal Mulai',
            'L1' => 'Tanggal Berhenti',
            'M1' => 'Tempat Lahir',
            'N1' => 'Tanggal Lahir',
            'O1' => 'Pendidikan',
            'P1' => 'Gelar',
            'Q1' => 'Jurusan',
            'R1' => 'Sekolah/Universitas',
            'S1' => 'Tahun Lulus',
            'T1' => 'Alamat',
            'U1' => 'Status Pegawai',
            'V1' => 'No. Rekening',
            'W1' => 'Gaji Pokok',
            'X1' => 'Status'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Auto-size columns
        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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

            array_shift($rows); // Remove header row

            foreach ($rows as $row) {
                if (!empty($row[0])) { // Only process rows with a name
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
                        'status_pegawai' => $row[20] ?? null,
                        'no_rek' => $row[21] ?? null,
                        'gaji_pokok' => $row[22] ?? null,
                        'status' => $row[23] ?? 'inactive',
                    ]);
                }
            }

            $this->reset('file');
            session()->flash('success', 'Data berhasil diupload!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            session()->flash('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus user!');
        }
    }

    public function getUsersProperty()
    {
        return User::query()
            // ->whereNotIn('roles_id', [1])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
                // Atau jika Anda menggunakan ID: $query->where('id', 1);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['status'], function ($query) {
                $query->where('status', $this->filters['status']);
            })
            ->when($this->filters['gender'], function ($query) {
                $query->where('gender', $this->filters['gender']);
            })
            ->when($this->filters['jabatan_id'], function ($query) {
                $query->where('jabatan_id', $this->filters['jabatan_id']);
            })
            ->when($this->filters['bagian_id'], function ($query) {
                $query->where('bagian_id', $this->filters['bagian_id']);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.export-users', [
            'users' => $this->users
        ]);
    }
}