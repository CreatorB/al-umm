<?php

use App\Models\Announcement;

interface AnnouncementRepositoryInterface
{
    public function getAllActive();
    public function getForRole(?string $role);
    public function create(array $data);
    public function update(Announcement $announcement, array $data);
}

// app/Repositories/AnnouncementRepository.php
class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function getAllActive()
    {
        return Announcement::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getForRole(?string $role)
    {
        return Announcement::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
            })
            ->where(function($query) use ($role) {
                $query->whereNull('roles')
                      ->orWhereJsonContains('roles', $role);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return Announcement::create($data);
    }

    public function update(Announcement $announcement, array $data)
    {
        return $announcement->update($data);
    }
}