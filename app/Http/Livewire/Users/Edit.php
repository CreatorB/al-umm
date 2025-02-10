<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use App\Rules\CheckValidPhoneNumber;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $name;
    public $email;
    public $role = '';
    public $gender = 'male';
    public $phone;
    public $user;

    public $confirmingUserDeletion = false;

    public function mount(User $user)
    {
        // Log::info('Mounting user', ['user' => $user]);
        $this->user = $user->load('roles');

        // Log::info('User loaded with roles', [
        //     'user_id' => $this->user->id,
        //     'roles' => $this->user->roles->pluck('name')->toArray(),
        // ]);

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->gender = $this->user->gender ?? 'male';
        $this->phone = $this->user->phone ?? null;
        $this->status = $this->user->status;

        $this->role = $this->user->roles()->pluck('id')[0] ?? '';
    }

    // public function updatedStatus($value)
    // {
    //     $this->user->status = $value ? 'active' : 'inactive';
    //     $this->user->save();
    //     $this->bannerMesage('Status user berhasil diubah.');
    // }

    public function update()
    {
        $this->validate([
            'name' => ['required'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'role' => ['required'],
            'gender' => ['sometimes'],
            'phone' => ['sometimes', new CheckValidPhoneNumber],
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email
        ]);

        // All current roles will be removed from the user and replaced by the array given
        $this->user->syncRoles($this->role);

        $this->user->refresh();

        $this->bannerMessage('User updated.');
    }

    public function getRolesProperty()
    {
        return Role::pluck('name', 'id');
    }

    public function render()
    {
        // return view('livewire.users.edit');
        // Log::info('Rendering component with user', ['user' => $this->user]);
        return view('livewire.users.edit', [
            'user' => $this->user,
            'confirmingUserDeletion' => $this->confirmingUserDeletion,
        ]);
    }

    // public function confirmDelete()
    // {
    //     Log::info('Confirm delete method called', [
    //         'user_id' => $this->user->id,
    //         'current_user_id' => Auth::id(),
    //         'has_delete_permission' => Auth::user()->can('delete users')
    //     ]);

    //     $this->confirmingUserDeletion = true;
    //     Log::info('Confirm deletion flag set', [
    //         'confirming_deletion' => $this->confirmingUserDeletion
    //     ]);
    // }

    public function deleteUser(User $user)
    {
        Log::info('Delete user method called', [
            'user_id' => $user->id,
            'current_user_id' => Auth::id(),
            'current_user_role' => Auth::user()->roles()->pluck('name')->first(),
            'has_delete_permission' => Auth::user()->can('delete users')
        ]);

        $role = Auth::user()->roles()->pluck('name')->first();
        if (!in_array($role, ['superadmin', 'admin'])) {
            Log::error('User does not have permission to delete', ['current_user_role' => $role]);
            $this->bannerMessage('Anda tidak memiliki izin menghapus user.', 'danger');
            return redirect()->back();
        }

        if ($user->id == Auth::id()) {
            Log::error('User tried to delete their own account');
            $this->bannerMessage('Anda tidak dapat menghapus akun sendiri', 'danger');
            return;
        }

        if ($user->hasRole('superadmin')) {
            Log::error('User tried to delete a superadmin');
            $this->bannerMessage('Tidak dapat menghapus user super admin', 'danger');
            return;
        }

        Log::info('Deleting user', ['user_id' => $user->id]);
        $user->delete();

        Log::info('User deleted successfully', ['user_id' => $user->id]);
        return redirect()->route('users');
    }

    public function cancelDelete()
    {
        $this->confirmingUserDeletion = false;
    }
}
