<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class ConfirmOvertimeModal extends Component
{
    public $showModal = false; 
    public $message; 
    public $type; 
    public $confirmed = false; 

    protected $listeners = ['showOvertimeModal' => 'showModal'];

    public function showModal($message, $type)
    {
        $this->message = $message;
        $this->type = $type;
        $this->showModal = true;
    }

    public function confirm($isOvertime)
    {
        $this->confirmed = $isOvertime;
        $this->showModal = false;

        $this->emit('overtimeConfirmed', $this->confirmed, $this->type);
    }

    public function render()
    {
        return view('livewire.confirm-overtime-modal');
    }
}