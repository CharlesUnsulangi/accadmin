<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\Bank;
use App\Models\Area;
use App\Models\Vendor;
use App\Models\StatusCheque;
use App\Models\CoaMain;
use App\Models\CoaSub1;
use App\Models\CoaSub2;

/**
 * Master Data Dashboard
 * 
 * Menampilkan overview semua master data dalam sistem
 * dengan statistik dan quick access links
 */
class MasterDataDashboard extends Component
{
    public $statistics = [];
    public $lastUpdated = [];
    
    public function mount()
    {
        $this->loadStatistics();
        $this->loadLastUpdated();
    }

    /**
     * Load statistics untuk semua master tables
     */
    private function loadStatistics()
    {
        $this->statistics = [
            'coa' => [
                'total' => Coa::where('rec_status', '1')->count(),
                'inactive' => Coa::where('rec_status', '0')->count(),
                'icon' => 'fa-chart-line',
                'color' => 'primary',
                'route' => 'coa.modern',
                'label' => 'Chart of Accounts',
                'description' => 'Detail COA accounts for transactions'
            ],
            'coa_main' => [
                'total' => CoaMain::where('rec_status', '1')->count(),
                'inactive' => CoaMain::where('rec_status', '0')->count(),
                'icon' => 'fa-layer-group',
                'color' => 'info',
                'route' => 'coa.main',
                'label' => 'COA Main Categories',
                'description' => 'Level 1: Assets, Liabilities, Equity, etc.'
            ],
            'coa_sub1' => [
                'total' => CoaSub1::where('rec_status', '1')->count(),
                'inactive' => CoaSub1::where('rec_status', '0')->count(),
                'icon' => 'fa-sitemap',
                'color' => 'secondary',
                'route' => 'coa.legacy',
                'label' => 'COA Sub Category 1',
                'description' => 'Level 2: Sub categories'
            ],
            'coa_sub2' => [
                'total' => CoaSub2::where('rec_status', '1')->count(),
                'inactive' => CoaSub2::where('rec_status', '0')->count(),
                'icon' => 'fa-project-diagram',
                'color' => 'secondary',
                'route' => 'coa.legacy',
                'label' => 'COA Sub Category 2',
                'description' => 'Level 3: Detail sub categories'
            ],
            'banks' => [
                'total' => Bank::where('rec_status', '1')->count(),
                'inactive' => Bank::where('rec_status', '0')->count(),
                'icon' => 'fa-university',
                'color' => 'success',
                'route' => 'master.bank',
                'label' => 'Banks',
                'description' => 'Bank master data'
            ],
            'areas' => [
                'total' => Area::where('rec_status', '1')->count(),
                'inactive' => Area::where('rec_status', '0')->count(),
                'icon' => 'fa-map-marker-alt',
                'color' => 'warning',
                'route' => 'master.area',
                'label' => 'Areas/Branches',
                'description' => 'Branch and area master'
            ],
            'vendors' => [
                'total' => Vendor::where('rec_status', '1')->count(),
                'inactive' => Vendor::where('rec_status', '0')->count(),
                'icon' => 'fa-users',
                'color' => 'danger',
                'route' => 'master.vendor',
                'label' => 'Vendors/Suppliers',
                'description' => 'Vendor and supplier master'
            ],
            'status_cheque' => [
                'total' => StatusCheque::count(),
                'inactive' => 0,
                'icon' => 'fa-check-circle',
                'color' => 'dark',
                'route' => 'master.statuscheque',
                'label' => 'Cheque Status',
                'description' => 'Reference data for cheque status'
            ],
        ];
    }

    /**
     * Load last updated information
     */
    private function loadLastUpdated()
    {
        $tables = [
            'ms_acc_coa' => 'COA',
            'ms_acc_coa_main' => 'COA Main',
            'ms_acc_coasub1' => 'COA Sub1',
            'ms_acc_coasub2' => 'COA Sub2',
            'ms_acc_bank' => 'Banks',
            'ms_acc_area' => 'Areas',
            'ms_acc_vendor' => 'Vendors',
            'ms_acc_statuscheque' => 'Status Cheque',
        ];

        foreach ($tables as $table => $label) {
            try {
                $lastUpdate = DB::table($table)
                    ->orderBy('rec_dateupdate', 'desc')
                    ->first(['rec_dateupdate', 'rec_userupdate']);
                
                if ($lastUpdate && $lastUpdate->rec_dateupdate) {
                    $this->lastUpdated[$table] = [
                        'label' => $label,
                        'date' => $lastUpdate->rec_dateupdate,
                        'user' => $lastUpdate->rec_userupdate ?? 'System'
                    ];
                }
            } catch (\Exception $e) {
                // Skip if table doesn't have the columns
                continue;
            }
        }
    }

    /**
     * Refresh statistics
     */
    public function refresh()
    {
        $this->loadStatistics();
        $this->loadLastUpdated();
        session()->flash('message', 'Statistics refreshed successfully!');
    }

    /**
     * Navigate to specific master page
     */
    public function navigateTo($route)
    {
        return redirect()->route($route);
    }

    public function render()
    {
        return view('livewire.master-data-dashboard')
            ->layout('layouts.bootstrap');
    }
}
