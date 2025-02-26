<?php

namespace App\Http\Livewire\Admin\Announcements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Announcement;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';
    public $filterType = '';
    public $sortField = 'priority'; 
    public $sortDirection = 'desc'; 

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterType' => ['except' => ''],
        'sortField' => ['except' => 'priority'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterRole', 'filterType']);
    }

    public function getAnnouncementsProperty()
    {
        return Announcement::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRole, function ($query) {
                if ($this->filterRole === 'all') {
                    $query->whereNull('roles');
                } else {
                    $query->whereJsonContains('roles', $this->filterRole);
                }
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.announcements.index', [
            'announcements' => $this->announcements,
            'roles' => Role::pluck('name'),
            'types' => [
                'text' => 'Text Only',
                'app_update' => 'App Update',
                'link' => 'Link',
            ]
        ]);
    }

    public function delete($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        session()->flash('success', 'Announcement deleted successfully.');
    }
}