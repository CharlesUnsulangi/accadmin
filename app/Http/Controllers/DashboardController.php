<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        try {
            // COA Statistics
            $coaStats = [
                'total' => Coa::count(),
                'active' => Coa::active()->count(),
                'inactive' => Coa::where('rec_status', '!=', 'A')->count(),
            ];

            // Hierarchy Statistics
            $hierarchyStats = [
                'h1' => Coa::active()->whereNotNull('ms_coa_h1_id')->distinct()->count('ms_coa_h1_id'),
                'h2' => Coa::active()->whereNotNull('ms_coa_h2_id')->distinct()->count('ms_coa_h2_id'),
                'h3' => Coa::active()->whereNotNull('ms_coa_h3_id')->distinct()->count('ms_coa_h3_id'),
                'h4' => Coa::active()->whereNotNull('ms_coa_h4_id')->distinct()->count('ms_coa_h4_id'),
                'h5' => Coa::active()->whereNotNull('ms_coa_h5_id')->distinct()->count('ms_coa_h5_id'),
                'h6' => Coa::active()->whereNotNull('ms_coa_h6_id')->distinct()->count('ms_coa_h6_id'),
            ];

            // Recent Activities (mock data - replace with actual activity log)
            $recentActivities = [
                [
                    'icon' => 'fa-plus-circle',
                    'color' => 'success',
                    'title' => 'New COA Added',
                    'description' => 'Account 1010-001 created',
                    'time' => '2 hours ago'
                ],
                [
                    'icon' => 'fa-edit',
                    'color' => 'info',
                    'title' => 'Account Updated',
                    'description' => 'Modified account 2020-005',
                    'time' => '5 hours ago'
                ],
                [
                    'icon' => 'fa-check-circle',
                    'color' => 'primary',
                    'title' => 'Closing Completed',
                    'description' => 'Monthly closing for October',
                    'time' => '1 day ago'
                ],
                [
                    'icon' => 'fa-file-alt',
                    'color' => 'warning',
                    'title' => 'Report Generated',
                    'description' => 'Balance sheet exported',
                    'time' => '2 days ago'
                ],
            ];

            return view('dashboard', compact('coaStats', 'hierarchyStats', 'recentActivities'));

        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return view with default values on error
            return view('dashboard', [
                'coaStats' => ['total' => 0, 'active' => 0, 'inactive' => 0],
                'hierarchyStats' => ['h1' => 0, 'h2' => 0, 'h3' => 0, 'h4' => 0, 'h5' => 0, 'h6' => 0],
                'recentActivities' => [],
            ]);
        }
    }
}
