<?php

namespace App\Livewire;

use Livewire\Component;

class BalanceSheetReport extends Component
{
    public function render()
    {
        return view('livewire.balance-sheet-report')
            ->layout('layouts.admin')
            ->title('Balance Sheet Report');
    }
}
