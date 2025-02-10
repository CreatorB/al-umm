<?php

namespace App\Http\Livewire\Users;
use App\Models\User;
use Livewire\Component;

class UserStatusSwitch extends Component
{
    public $status;
    public $userId;

    public function mount($status, $userId)
    {
        $this->status = $status === 'active';
        $this->userId = $userId;
    }

    public function updatedStatus($value)
    {
        $user = User::findOrFail($this->userId);
        $user->status = $value ? 'active' : 'inactive';
        $user->save();

        $this->emit('notify', 'success', 'User status updated successfully.');
        // $this->bannerMesage('Status user berhasil diubah.');
    }

    public function render()
    {
        return view('livewire.users.user-status-switch');
    }

}