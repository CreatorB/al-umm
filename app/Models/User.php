<?php

namespace App\Models;

use App\Traits\WithUuid;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, WithUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'gender',
        'phone',
        'api_token',
        'nip',
        'working_days',
        'working_time_start',
        'working_time_end',
        'jumlah_cuti',
        'jabatan_id',
        'bagian_id',
        'lokasi_kerja',
        'tgl_mulai',
        'tgl_berhenti',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan',
        'gelar',
        'jurusan',
        'sekolah_universitas',
        'tahun_lulus_1',
        'pendidikan_2',
        'jurusan_pendidikan_2',
        'sekolah_universitas_2',
        'tahun_lulus_2',
        'alamat',
        'alamat_email',
        'type_pegawai',
        'status_pegawai',
        'ktp_id',
        'keterangan',
        'no_rek',
        'special_adjustment_sa',
        'sa_date_start_acting',
        'kontrak_mulai_1',
        'kontrak_selesai_1',
        'kontrak_mulai_2',
        'kontrak_selesai_2',
        'gaji_pokok',
        'ptt',
        't_jabatan',
        't_kehadiran',
        't_anak',
        'bonus_sanad',
        'diniyyah',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'schedule_id',
        'jabatan_id',
        'bagian_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'photo_url'
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'jabatan_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'bagian_id');
    }


}
