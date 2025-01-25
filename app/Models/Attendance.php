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
