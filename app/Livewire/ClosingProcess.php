<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ClosingService;
use App\Models\MonthlyClosing;
use App\Models\YearlyClosing;
use Carbon\Carbon;

class ClosingProcess extends Component
{
    public $selectedYear;
    public $selectedMonth;
    public $closingType = 'monthly'; // monthly, yearly, audit
    public $previewData = [];
    public $showPreview = false;
    public $isProcessing = false;
    
    protected $closingService;
    
    public function mount()
    {
        $this->selectedYear = date('Y');
        $this->selectedMonth = date('m');
    }
    
    public function boot(ClosingService $closingService)
    {
        $this->closingService = $closingService;
    }

    /**
     * Preview data sebelum generate
     */
    public function preview()
    {
        $this->validate([
            'selectedYear' => 'required|integer|min:2020|max:2099',
            'selectedMonth' => 'required_if:closingType,monthly|integer|min:1|max:12',
        ]);
        
        $this->isProcessing = true;
        
        try {
            if ($this->closingType === 'monthly') {
                $this->previewData = app(ClosingService::class)->calculateMonthly(
                    (int) $this->selectedYear, 
                    (int) $this->selectedMonth, 
                    false
                );
            } elseif ($this->closingType === 'yearly') {
                $this->previewData = app(ClosingService::class)->calculateYearly(
                    (int) $this->selectedYear, 
                    false
                );
            } else {
                $this->previewData = app(ClosingService::class)->calculateAudit(
                    null,
                    (int) $this->selectedYear,
                    (int) $this->selectedMonth
                );
            }
            
            $this->showPreview = true;
            session()->flash('message', 'Preview berhasil di-generate. Silakan review data sebelum menyimpan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        
        $this->isProcessing = false;
    }

    /**
     * Generate dan simpan closing
     */
    public function generate()
    {
        $this->validate([
            'selectedYear' => 'required|integer|min:2020|max:2099',
            'selectedMonth' => 'required_if:closingType,monthly|integer|min:1|max:12',
        ]);
        
        $this->isProcessing = true;
        
        try {
            if ($this->closingType === 'monthly') {
                app(ClosingService::class)->calculateMonthly(
                    (int) $this->selectedYear, 
                    (int) $this->selectedMonth, 
                    true // Save to DB
                );
                
                $periode = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->format('F Y');
                session()->flash('success', "Closing bulanan {$periode} berhasil di-generate!");
                
            } elseif ($this->closingType === 'yearly') {
                app(ClosingService::class)->calculateYearly(
                    (int) $this->selectedYear, 
                    true // Save to DB
                );
                
                session()->flash('success', "Closing tahunan {$this->selectedYear} berhasil di-generate!");
            }
            
            $this->showPreview = false;
            $this->previewData = [];
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        
        $this->isProcessing = false;
    }

    /**
     * Lock closing
     */
    public function lockClosing()
    {
        try {
            app(ClosingService::class)->lockClosing(
                (int) $this->selectedYear,
                (int) $this->selectedMonth,
                $this->closingType
            );
            
            session()->flash('success', 'Closing berhasil di-lock. Data tidak bisa diubah lagi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get existing closings
     */
    public function getExistingClosings()
    {
        if ($this->closingType === 'monthly') {
            return MonthlyClosing::where('closing_year', $this->selectedYear)
                ->where('closing_month', $this->selectedMonth)
                ->orderBy('version_number', 'desc')
                ->get();
        } else {
            return YearlyClosing::where('closing_year', $this->selectedYear)
                ->orderBy('version_number', 'desc')
                ->get();
        }
    }

    public function render()
    {
        $existingClosings = $this->getExistingClosings();
        
        return view('livewire.closing-process', [
            'existingClosings' => $existingClosings,
        ])
            ->layout('layouts.admin')
            ->title('Closing Process - 3 Layer System');
    }
}
