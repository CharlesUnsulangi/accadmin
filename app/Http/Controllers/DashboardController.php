<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
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

    /**
     * Display accounting dashboard
     */
    public function accounting()
    {
        return view('dashboard.accounting');
    }

    /**
     * Display admin IT dashboard
     */
    public function adminIt()
    {
        return view('dashboard.admin-it');
    }

    /**
     * Display system overview page (Alpine.js)
     */
    public function overview()
    {
        return view('overview');
    }

    /**
     * Get accounting dashboard statistics (API)
     */
    public function getAccountingStats()
    {
        try {
            $stats = [
                'coa_total' => Coa::count(),
                'coa_active' => Coa::where('rec_status', '1')->count(),
                'transactions' => DB::table('tr_acc_transaksi')->count(),
                'last_closing' => $this->getLastClosingPeriod(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin IT dashboard statistics (API)
     */
    public function getAdminItStats()
    {
        try {
            // Get table count
            $tables = DB::select("
                SELECT COUNT(*) as count 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_TYPE = 'BASE TABLE'
            ");

            // Get stored procedure count
            $sps = DB::table('ms_admin_it_sp')->count();

            $stats = [
                'tables' => $tables[0]->count ?? 0,
                'stored_procs' => $sps,
                'db_size' => '500 MB',
                'uptime' => '99.9%'
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system overview statistics (API)
     */
    public function getOverviewStats()
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

            // Recent Activities (mock data)
            $activities = [
                [
                    'icon' => 'fa-plus-circle',
                    'color' => 'success',
                    'title' => 'New COA Added',
                    'description' => 'Account created successfully',
                    'time' => '2 hours ago'
                ],
                [
                    'icon' => 'fa-edit',
                    'color' => 'info',
                    'title' => 'Account Updated',
                    'description' => 'COA information modified',
                    'time' => '5 hours ago'
                ],
                [
                    'icon' => 'fa-trash',
                    'color' => 'danger',
                    'title' => 'Account Deactivated',
                    'description' => 'Inactive account marked',
                    'time' => '1 day ago'
                ]
            ];

            return response()->json([
                'coa' => $coaStats,
                'hierarchy' => $hierarchyStats,
                'activities' => $activities
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading overview statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get last closing period
     */
    private function getLastClosingPeriod()
    {
        try {
            $lastClosing = DB::table('tr_acc_monthly_closing')
                ->orderByDesc('closing_month')
                ->first();

            if ($lastClosing) {
                $date = \Carbon\Carbon::createFromFormat('Ym', $lastClosing->closing_month);
                return $date->format('M Y');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
