<?php

namespace App\Livewire;

use App\Models\Coa;
use Livewire\Component;

/**
 * Dashboard Component
 * Shows statistics and overview of the accounting system
 */
class Dashboard extends Component
{
    public $refreshInterval = 30000; // Refresh every 30 seconds

    /**
     * Get COA statistics
     */
    public function getCoaStats()
    {
        try {
            return [
                'total' => Coa::count(),
                'active' => Coa::active()->count(),
                'inactive' => Coa::where('rec_status', '!=', 'A')->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Dashboard getCoaStats error: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
            ];
        }
    }

    /**
     * Get COA hierarchy statistics (H1-H6 flexible hierarchy)
     */
    public function getHierarchyStats()
    {
        try {
            return [
                'h1' => Coa::active()->whereNotNull('ms_coa_h1_id')->distinct()->count('ms_coa_h1_id'),
                'h2' => Coa::active()->whereNotNull('ms_coa_h2_id')->distinct()->count('ms_coa_h2_id'),
                'h3' => Coa::active()->whereNotNull('ms_coa_h3_id')->distinct()->count('ms_coa_h3_id'),
                'h4' => Coa::active()->whereNotNull('ms_coa_h4_id')->distinct()->count('ms_coa_h4_id'),
                'h5' => Coa::active()->whereNotNull('ms_coa_h5_id')->distinct()->count('ms_coa_h5_id'),
                'h6' => Coa::active()->whereNotNull('ms_coa_h6_id')->distinct()->count('ms_coa_h6_id'),
                'total' => Coa::active()->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Dashboard getHierarchyStats error: ' . $e->getMessage());
            return [
                'h1' => 0,
                'h2' => 0,
                'h3' => 0,
                'h4' => 0,
                'h5' => 0,
                'h6' => 0,
                'total' => 0,
            ];
        }
    }

    /**
     * Get account type distribution
     */
    public function getAccountTypeDistribution()
    {
        try {
            // Simple distribution by first character of coa_code
            $distribution = [
                'Asset' => Coa::active()->where('coa_code', 'like', '1%')->count(),
                'Liability' => Coa::active()->where('coa_code', 'like', '2%')->count(),
                'Equity' => Coa::active()->where('coa_code', 'like', '3%')->count(),
                'Revenue' => Coa::active()->where('coa_code', 'like', '4%')->count(),
                'Expense' => Coa::active()->where('coa_code', 'like', '5%')->count(),
                'Other' => Coa::active()->where('coa_code', 'not like', '1%')
                    ->where('coa_code', 'not like', '2%')
                    ->where('coa_code', 'not like', '3%')
                    ->where('coa_code', 'not like', '4%')
                    ->where('coa_code', 'not like', '5%')
                    ->count(),
            ];

            return $distribution;
        } catch (\Exception $e) {
            \Log::error('Dashboard getAccountTypeDistribution error: ' . $e->getMessage());
            return [
                'Asset' => 0,
                'Liability' => 0,
                'Equity' => 0,
                'Revenue' => 0,
                'Expense' => 0,
                'Other' => 0,
            ];
        }
    }

    /**
     * Get recently created COAs
     */
    public function getRecentCoas()
    {
        try {
            return Coa::active()
                ->orderBy('rec_datecreated', 'desc')
                ->take(5)
                ->get(['coa_code', 'coa_desc', 'rec_datecreated']);
        } catch (\Exception $e) {
            \Log::error('Dashboard getRecentCoas error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get recently updated COAs
     */
    public function getRecentUpdates()
    {
        try {
            return Coa::active()
                ->orderBy('rec_dateupdate', 'desc')
                ->take(5)
                ->get(['coa_code', 'coa_desc', 'rec_dateupdate']);
        } catch (\Exception $e) {
            \Log::error('Dashboard getRecentUpdates error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.dashboard', [
            'coaStats' => $this->getCoaStats(),
            'hierarchyStats' => $this->getHierarchyStats(),
            'accountTypes' => $this->getAccountTypeDistribution(),
            'recentCoas' => $this->getRecentCoas(),
            'recentUpdates' => $this->getRecentUpdates(),
        ]);
    }
}
