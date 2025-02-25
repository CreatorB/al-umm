<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Department;
use App\Models\Part;
use App\Models\Schedule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public $showErrorPopup = false;
    public $errorMessages = [];

    public $user;
    public $photo;
    public $name;
    public $email;
    public $role = '';
    public $gender;
    public $phone;
    public $password;
    public $userFields = [
        'nip' => '',
        'schedule_id' => '',
        'working_days' => '',
        'jumlah_cuti' => '',
        'jabatan_id' => '',
        'bagian_id' => '',
        'lokasi_kerja' => '',
        'tgl_mulai' => '',
        'tgl_berhenti' => '',
        'tempat_lahir' => '',
        'tanggal_lahir' => '',
        'pendidikan' => '',
        'gelar' => '',
        'jurusan' => '',
        'sekolah_universitas' => '',
        'tahun_lulus_1' => '',
        'pendidikan_2' => '',
        'jurusan_pendidikan_2' => '',
        'sekolah_universitas_2' => '',
        'tahun_lulus_2' => '',
        'alamat' => '',
        'type_pegawai' => '',
        'status_pegawai' => '',
        'ktp_id' => '',
        'keterangan' => '',
        'no_rek' => '',
        'special_adjustment_sa' => '',
        'sa_date_start_acting' => '',
        'kontrak_mulai_1' => '',
        'kontrak_selesai_1' => '',
        'kontrak_mulai_2' => '',
        'kontrak_selesai_2' => '',
        'gaji_pokok' => '',
        'ptt' => '',
        't_jabatan' => '',
        't_kehadiran' => '',
        't_anak' => '',
        'bonus_sanad' => '',
        'diniyyah' => '',
        'status' => '',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'role' => 'required',
        'gender' => 'nullable|in:L,P',
        'phone' => 'nullable|string',
        'photo' => 'nullable|image|max:1024',
        'password' => 'nullable|min:8',
        'userFields.nip' => 'required|string|max:255',
        'userFields.schedule_id' => 'required|exists:schedules,id',
        'userFields.working_days' => 'required|integer|min:0|max:31',
        'userFields.jumlah_cuti' => 'required|integer|min:0',
        'userFields.jabatan_id' => 'required|exists:departments,id',
        'userFields.bagian_id' => 'nullable|exists:parts,id',
        'userFields.lokasi_kerja' => 'nullable|string|max:255',
        'userFields.tgl_mulai' => 'nullable|date',
        'userFields.tgl_berhenti' => 'nullable|date|after_or_equal:userFields.tgl_mulai',
        'userFields.tempat_lahir' => 'nullable|string|max:255',
        'userFields.tanggal_lahir' => 'nullable|date|before:today',
        'userFields.pendidikan' => 'nullable|string|max:255',
        'userFields.gelar' => 'nullable|string|max:255',
        'userFields.jurusan' => 'nullable|string|max:255',
        'userFields.sekolah_universitas' => 'nullable|string|max:255',
        'userFields.tahun_lulus_1' => 'nullable|integer|min:1900',
        'userFields.pendidikan_2' => 'nullable|string|max:255',
        'userFields.jurusan_pendidikan_2' => 'nullable|string|max:255',
        'userFields.sekolah_universitas_2' => 'nullable|string|max:255',
        'userFields.tahun_lulus_2' => 'nullable|integer|min:1900',
        'userFields.alamat' => 'nullable|string',
        'userFields.type_pegawai' => 'nullable|string|max:255',
        'userFields.status_pegawai' => 'nullable|string|max:255',
        'userFields.ktp_id' => 'nullable|string|max:255',
        'userFields.keterangan' => 'nullable|string',
        'userFields.no_rek' => 'nullable|string|max:255',
        'userFields.special_adjustment_sa' => 'nullable|numeric|min:0',
        'userFields.sa_date_start_acting' => 'nullable|date',
        'userFields.kontrak_mulai_1' => 'nullable|date',
        'userFields.kontrak_selesai_1' => 'nullable|date|after_or_equal:userFields.kontrak_mulai_1',
        'userFields.kontrak_mulai_2' => 'nullable|date',
        'userFields.kontrak_selesai_2' => 'nullable|date|after_or_equal:userFields.kontrak_mulai_2',
        'userFields.gaji_pokok' => 'nullable|integer|min:0',
        'userFields.ptt' => 'nullable|string|max:255',
        'userFields.t_jabatan' => 'nullable|string|max:255',
        'userFields.t_kehadiran' => 'nullable|string|max:255',
        'userFields.t_anak' => 'nullable|string|max:255',
        'userFields.bonus_sanad' => 'nullable|string|max:255',
        'userFields.diniyyah' => 'nullable|string|max:255',
        'userFields.status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'userFields.nip.required' => 'NIP harus diisi.',
        'userFields.schedule_id.required' => 'Schedule harus dipilih.',
        'userFields.working_days.required' => 'Working days harus diisi.',
        'userFields.jumlah_cuti.required' => 'Jumlah cuti harus diisi.',
        'userFields.jabatan_id.required' => 'Jabatan harus dipilih.',
        'userFields.status.required' => 'Status harus dipilih.',
        'password.min' => 'Password minimal harus 8 karakter.',
    ];

    public function mount(User $user)
    {
        $this->user = $user->load('roles');

        // Set basic fields
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->gender = $this->user->gender;
        $this->phone = $this->user->phone;
        $this->role = $this->user->roles()->first()->id ?? '';

        // Set additional fields
        foreach ($this->userFields as $field => $value) {
            $this->userFields[$field] = $user->$field;
        }
    }

    public function update()
    {
        try {
            $validatedData = $this->validate();

            // Handle photo upload
            if ($this->photo) {
                if ($this->user->photo) {
                    Storage::disk('public')->delete($this->user->photo);
                }
                $path = $this->photo->store('photos', 'public');
                $this->user->photo = $path;
            }

            // Update basic fields
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'gender' => $this->gender,
                'phone' => $this->phone,
                'schedule_id' => $this->userFields['schedule_id'],
            ];

            if (!empty($this->password)) {
                $updateData['password'] = bcrypt($this->password);
            }
            $this->user->update($updateData + $this->userFields);
            // Verify role update permission
            $currentUserRole = auth()->user()->roles()->first()->name;
            $newRole = Role::find($this->role);

            if (
                $currentUserRole !== 'superadmin' &&
                !in_array($newRole->name, ['admin', 'employee'])
            ) {
                throw new \Exception('You do not have permission to assign this role.');
            }
            // Update additional fields
            $this->user->update($this->userFields);

            // Update role
            if ($this->role) {
                $this->user->syncRoles($this->role);
            }

            session()->flash('message', 'User successfully updated.');

            return redirect()->route('admin.export-users');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessages = $e->validator->errors()->all();
            $this->showErrorPopup = true;
        }
    }

    public function cancelEdit()
    {
        return redirect()->route('admin.export-users');
    }

    public function getRolesProperty()
    {
        $currentUserRole = auth()->user()->roles()->first()->name;

        if ($currentUserRole === 'superadmin') {
            return Role::where('name', '!=', 'superadmin')
                ->pluck('name', 'id');
        } else {
            return Role::whereIn('name', ['admin', 'employee'])
                ->pluck('name', 'id');
        }
    }

    public function getJabatansProperty()
    {
        return Department::pluck('name', 'id');
    }

    public function getBagiansProperty()
    {
        return Part::pluck('name', 'id');
    }

    public function getSchedulesProperty()
    {
        return Schedule::pluck('title', 'id');
    }

    protected function cleanupOldPhoto()
    {
        if ($this->user->photo) {
            Storage::disk('public')->delete($this->user->photo);
        }
    }

    public function deletePhoto()
    {
        $this->cleanupOldPhoto();
        $this->user->update(['photo' => null]);
        $this->photo = null;
        session()->flash('message', 'Photo berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.users.edit', [
            'roles' => $this->roles,
            'jabatans' => $this->jabatans,
            'bagians' => $this->bagians,
            'schedules' => $this->schedules,
        ]);
    }
}