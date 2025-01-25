<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Index extends Component
{
    public function mount()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return Redirect::to('/admin/export-users');
        }
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::with('roles')->simplePaginate()
        ]);
    }
}
