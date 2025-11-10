<?php

namespace App\Livewire;

use Livewire\Component;

class VersionComparison extends Component
{
    public function render()
    {
        return view('livewire.version-comparison')
            ->layout('layouts.admin')
            ->title('Version Comparison');
    }
}
