<?php

namespace App\Livewire;

use Livewire\Component;

class VersionHistoryViewer extends Component
{
    public function render()
    {
        return view('livewire.version-history-viewer')
            ->layout('layouts.admin')
            ->title('Version History');
    }
}
