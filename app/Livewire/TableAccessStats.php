<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TableAccessLog;
use Illuminate\Support\Facades\DB;

class TableAccessStats extends Component
{
    public $timeRange = '24h'; // 24h, 7d, 30d, all
    public $limit = 10;
    
    public function getMostAccessedTables()
    {
        $query = TableAccessLog::selectRaw('
                table_name,
                COUNT(*) as access_count,
                COUNT(DISTINCT user_id) as unique_users,
                MAX(accessed_at) as last_accessed
            ')
            ->groupBy('table_name')
            ->orderByDesc('access_count');
            
        // Apply time range filter
        if ($this->timeRange !== 'all') {
            $query->where('accessed_at', '>=', $this->getTimeRangeDate());
        }
        
        return $query->limit($this->limit)->get();
    }
    
    public function getAccessByFrontend()
    {
        $query = TableAccessLog::selectRaw('
                frontend_type,
                COUNT(*) as access_count,
                COUNT(DISTINCT table_name) as tables_accessed
            ')
            ->groupBy('frontend_type')
            ->orderByDesc('access_count');
            
        if ($this->timeRange !== 'all') {
            $query->where('accessed_at', '>=', $this->getTimeRangeDate());
        }
        
        return $query->get();
    }
    
    public function getRecentAccess()
    {
        return TableAccessLog::orderByDesc('accessed_at')
            ->limit(15)
            ->get();
    }
    
    public function getOverallStats()
    {
        $query = TableAccessLog::query();
        
        if ($this->timeRange !== 'all') {
            $query->where('accessed_at', '>=', $this->getTimeRangeDate());
        }
        
        return [
            'total_access' => $query->count(),
            'unique_tables' => $query->distinct('table_name')->count('table_name'),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'unique_frontends' => $query->distinct('frontend_type')->count('frontend_type'),
        ];
    }
    
    private function getTimeRangeDate()
    {
        return match($this->timeRange) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subCentury(),
        };
    }
    
    public function render()
    {
        return view('livewire.table-access-stats', [
            'mostAccessed' => $this->getMostAccessedTables(),
            'accessByFrontend' => $this->getAccessByFrontend(),
            'recentAccess' => $this->getRecentAccess(),
            'stats' => $this->getOverallStats(),
        ])->layout('layouts.admin');
    }
}

