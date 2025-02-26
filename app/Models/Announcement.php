<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Log;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'link_url',
        'link_type',
        'version',
        'is_active',
        'priority',
        'roles',
        'expired_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'expired_at' => 'datetime',
        'roles' => 'array'
    ];

    public function getRolesAttribute($value)
    {
        Log::debug('Getting roles:', ['raw' => $value, 'casted' => json_decode($value, true)]);
        return json_decode($value, true);
    }

    public function isVisibleToRole(?string $role): bool
    {
        if (empty($this->roles))
            return true; // Visible to all if no roles specified
        return in_array($role, $this->roles);
    }

    protected static function booted()
    {
        static::deleting(function ($announcement) {
            // Cleanup related data if needed
        });
    }
}