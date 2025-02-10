<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public function user()
    {
        return $this->hasMany(User::class, 'schedule_id');
    }
}