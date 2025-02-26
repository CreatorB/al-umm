<?php

use App\Http\Livewire\Roles;
use App\Http\Livewire\Users;
use App\Http\Livewire\Events;
use App\Http\Livewire\Attendances;
use App\Http\Livewire\Admin;
use App\Http\Livewire\Admin\Users as AdminUsers;
;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Admin\Announcements\Create;
use App\Http\Livewire\Admin\Announcements\Edit;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Welcome page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
});

// ping
Route::get('/ping', function () {
    return response('pong')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET');
});

// Dashboard
Route::view('dashboard', 'dashboard')->middleware(['auth'])->name('dashboard');

// Users
Route::get('/users', Users\Index::class)->middleware(['auth'])->name('users');
Route::get('/users/create', Users\Create::class)->middleware(['auth'])->name('users.create');
Route::get('/users/{user}/edit', Users\Edit::class)->middleware(['auth'])->name('users.edit');

// Profile
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// Events
Route::get('/events/create', Events\Create::class)->name('events.create');

// Roles & Permissions
Route::get('/roles', Roles\Index::class)->middleware(['auth'])->name('roles');
Route::get('/roles/{role}', Roles\Edit::class)->middleware(['auth'])->name('roles.edit');

Route::get('/attendances/tapping', Attendances\Tapping::class)->middleware(['auth', 'check.local'])->name('attendances.tapping');
Route::get('/attendances/perizinan', Attendances\Perizinan::class)->middleware(['auth'])->name('attendances.perizinan');
// Route::post('/attendances/toggle', [Attendances\Tapping::class, 'toggle'])->name('attendances.toggle');

// Route::get('/admin/export-absen', Admin\ExportAbsensi::class)->middleware(['auth', 'check.role.access:hr,admin,superadmin'])->name('admin.export-absen');
Route::match(['get', 'post'], '/admin/export-absen', Admin\ExportAbsensi::class)
    ->middleware(['auth', 'check.role.access:hr,admin,superadmin'])
    ->name('admin.export-absen');

Route::get('/admin/export-users', Admin\ExportUsers::class)->middleware(['auth', 'check.role.access:hr,admin,superadmin'])->name('admin.export-users');
// Route::get('/admin/users-edit', Admin\UsersEdit::class)->middleware(['auth', 'check.role.access:hr,admin,superadmin'])->name('admin.users-edit');

Route::middleware(['auth'])->group(function () {
    // multi role route
    Route::prefix('admin')->middleware(['check.role.access:hr,admin,superadmin'])->group(function () {
        Route::get('/users/{user}/edit', AdminUsers\Edit::class)
            ->name('admin.users.edit');
        Route::get('/announcements', \App\Http\Livewire\Admin\Announcements\Index::class)->name('admin.announcements.index');
        Route::get('/announcements/create', Create::class)->name('admin.announcements.create');
        Route::get('/announcements/{announcement}/edit', Edit::class)->name('admin.announcements.edit');
    });

    // Superadmin routes
    // Route::prefix('superadmin')->middleware(['role:superadmin'])->group(function () {
    //     Route::get('/users', \App\Http\Livewire\Superadmin\Users\Index::class)
    //         ->name('superadmin.users.index');
    //     Route::get('/users/{user}/edit', \App\Http\Livewire\Superadmin\Users\Edit::class)
    //         ->name('superadmin.users.edit');
    // });

    // Admin routes
    // Route::prefix('admin')->middleware(['role:admin'])->group(function () {
    //     Route::get('/users', \App\Http\Livewire\Admin\Users\Index::class)
    //         ->name('admin.users.index');
    //     Route::get('/users/{user}/edit', \App\Http\Livewire\Admin\Users\Edit::class)
    //         ->name('admin.users.edit');
    // });

    // Staff/other roles routes
    // Route::prefix('staff')->middleware(['role:staff'])->group(function () {
    //     Route::get('/users', \App\Http\Livewire\Staff\Users\Index::class)
    //         ->name('staff.users.index');
    //     Route::get('/users/{user}/edit', \App\Http\Livewire\Staff\Users\Edit::class)
    //         ->name('staff.users.edit');
    // });
});


require __DIR__ . '/auth.php';
