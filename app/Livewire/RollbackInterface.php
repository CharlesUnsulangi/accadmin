<?php

namespace App\Livewire;

use Livewire\Component;

class RollbackInterface extends Component
{
    public function render()
    {
        return view('livewire.rollback-interface')
            ->layout('layouts.admin')
            ->title('Rollback Interface');
    }
}
