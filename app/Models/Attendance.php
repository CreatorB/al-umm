<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in',
        'check_out',
        'check_in_location',
        'check_out_location',
        'status',
        'shift',
        'over_time_in',
        'over_time_out',
        'permit_id',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date:Y-m-d', 
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'over_time_in' => 'datetime',
        'over_time_out' => 'datetime',
        'is_overtime' => 'boolean',
        'shift' => 'integer'
    ];

    // Status constants
    const STATUS_HADIR = 'hadir';
    const STATUS_SAKIT = 'sakit';
    const STATUS_IZIN = 'izin';
    const STATUS_TUGAS_LUAR = 'tugas_luar';
    const STATUS_CUTI = 'cuti';
    const STATUS_ALPHA = 'alpha';


    /**
     * Define a relationship to the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relationship to the permit.
     */
    public function permit()
    {
        return $this->belongsTo(Permit::class);
    }
}
