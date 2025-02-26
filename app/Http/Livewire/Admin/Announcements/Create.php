<?php
namespace App\Http\Livewire\Admin\Announcements;

use App\Models\Announcement;
use Livewire\Component;

class Create extends Component
{
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

    public function save()
    {
        $this->validate();

        Announcement::create([
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

        session()->flash('message', 'Announcement created successfully.');
        return redirect()->route('admin.announcements.index');
    }

    public function render()
    {
        return view('livewire.admin.announcements.create', [
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
        ]);
    }
}