<?php

namespace App\Http\Livewire\Admin\Announcements;

use App\Models\Announcement;
use Livewire\Component;

class Edit extends Component
{
    public Announcement $announcement;
    public $title;
    public $content;
    public $type = 'text';
    public $linkUrl;
    public $linkType = 'browser';
    public $version;
    public $priority = 0;
    public $isActive = true;
    public $expiredAt;
    public $selectedRoles = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'type' => 'required|in:text,app_update,link',
        'linkUrl' => 'nullable|url',
        'linkType' => 'nullable|in:browser,app,deeplink',
        'version' => 'nullable|string',
        'priority' => 'required|integer|min:0',
        'isActive' => 'boolean',
        'expiredAt' => 'nullable|date',
        'selectedRoles' => 'nullable|array',
    ];

    public function mount(Announcement $announcement)
    {
        $this->announcement = $announcement;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->type = $announcement->type;
        $this->linkUrl = $announcement->link_url;
        $this->linkType = $announcement->link_type;
        $this->version = $announcement->version;
        $this->priority = $announcement->priority;
        $this->isActive = $announcement->is_active;
        $this->expiredAt = $announcement->expired_at?->format('Y-m-d\TH:i');
        $this->selectedRoles = $announcement->roles ?? [];
    }

    public function save()
    {
        $this->validate();

        $this->announcement->update([
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'link_url' => $this->linkUrl,
            'link_type' => $this->linkType,
            'version' => $this->version,
            'priority' => $this->priority,
            'is_active' => $this->isActive,
            'expired_at' => $this->expiredAt,
            'roles' => $this->selectedRoles,
        ]);

        session()->flash('message', 'Announcement updated successfully.');
        return redirect()->route('admin.announcements.index');
    }

    public function render()
    {
        return view('livewire.admin.announcements.edit', [
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
        ]);
    }
}