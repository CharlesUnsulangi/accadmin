<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableAccessLog extends Model
{
    protected $table = 'tr_admin_it_table_access_log';
    
    public $timestamps = false;
    
    protected $fillable = [
        'table_name',
        'access_type',
        'frontend_type',
        'user_agent',
        'ip_address',
        'user_id',
        'user_name',
        'additional_info',
        'accessed_at'
    ];
    
    protected $casts = [
        'accessed_at' => 'datetime',
        'additional_info' => 'array'
    ];
    
    /**
     * Log table access
     */
    public static function logAccess(
        string $tableName,
        string $accessType = 'view',
        array $additionalInfo = []
    ) {
        $request = request();
        
        // Determine frontend type from user agent
        $userAgent = $request->userAgent() ?? 'Unknown';
        $frontendType = self::determineFrontendType($userAgent, $request);
        
        return self::create([
            'table_name' => $tableName,
            'access_type' => $accessType,
            'frontend_type' => $frontendType,
            'user_agent' => $userAgent,
            'ip_address' => $request->ip(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'Guest',
            'additional_info' => !empty($additionalInfo) ? json_encode($additionalInfo) : null,
            'accessed_at' => now()
        ]);
    }
    
    /**
     * Determine frontend type from user agent and request
     */
    private static function determineFrontendType(string $userAgent, $request): string
    {
        $userAgentLower = strtolower($userAgent);
        
        // Check if it's an API request
        if ($request->is('api/*') || $request->expectsJson()) {
            return 'API';
        }
        
        // Check for mobile devices
        if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
            if (preg_match('/android/i', $userAgent)) {
                return 'Mobile - Android';
            }
            if (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
                return 'Mobile - iOS';
            }
            return 'Mobile - Other';
        }
        
        // Check for specific browsers
        if (str_contains($userAgentLower, 'chrome') && !str_contains($userAgentLower, 'edge')) {
            return 'Web - Chrome';
        }
        if (str_contains($userAgentLower, 'firefox')) {
            return 'Web - Firefox';
        }
        if (str_contains($userAgentLower, 'safari') && !str_contains($userAgentLower, 'chrome')) {
            return 'Web - Safari';
        }
        if (str_contains($userAgentLower, 'edge')) {
            return 'Web - Edge';
        }
        if (str_contains($userAgentLower, 'opera') || str_contains($userAgentLower, 'opr')) {
            return 'Web - Opera';
        }
        
        // Check for bot/crawler
        if (preg_match('/bot|crawler|spider|scraper/i', $userAgent)) {
            return 'Bot/Crawler';
        }
        
        // Check for Postman or similar tools
        if (str_contains($userAgentLower, 'postman')) {
            return 'API - Postman';
        }
        if (str_contains($userAgentLower, 'insomnia')) {
            return 'API - Insomnia';
        }
        
        return 'Web - Other';
    }
    
    /**
     * Get access statistics for a table
     */
    public static function getTableStats(string $tableName)
    {
        return self::where('table_name', $tableName)
            ->selectRaw('
                COUNT(*) as total_access,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT frontend_type) as frontend_types,
                MIN(accessed_at) as first_access,
                MAX(accessed_at) as last_access
            ')
            ->first();
    }
    
    /**
     * Get most accessed tables
     */
    public static function getMostAccessedTables(int $limit = 10)
    {
        return self::selectRaw('table_name, COUNT(*) as access_count')
            ->groupBy('table_name')
            ->orderByDesc('access_count')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get access by frontend type
     */
    public static function getAccessByFrontend()
    {
        return self::selectRaw('frontend_type, COUNT(*) as access_count')
            ->groupBy('frontend_type')
            ->orderByDesc('access_count')
            ->get();
    }
}
