<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'ms_admin_it_aplikasi';
    protected $primaryKey = 'ms_admin_it_aplikasi_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ms_admin_it_aplikasi_id',
        'apps_desc',
        'framework',
        'id',
        'user_created',
        'time_created',
        'cek_non_aktif',
        'aplikasi_note'
    ];

    protected $casts = [
        'cek_non_aktif' => 'boolean',
    ];

    /**
     * Get active applications
     */
    public static function getActive()
    {
        return self::where('cek_non_aktif', 0)
            ->orWhereNull('cek_non_aktif')
            ->orderBy('apps_desc')
            ->get();
    }

    /**
     * Relationship: Application has many Topics (one-to-many)
     */
    public function topics()
    {
        return $this->hasMany(ApplicationTopic::class, 'ms_admin_it_aplikasi_id', 'ms_admin_it_aplikasi_id');
    }

    /**
     * Get application by ID
     */
    public static function findById($id)
    {
        return self::where('ms_admin_it_aplikasi_id', $id)->first();
    }

    /**
     * Check if application name is unique
     */
    public static function isNameUnique($name, $excludeId = null)
    {
        $query = self::whereRaw("CAST(apps_desc AS VARCHAR(MAX)) = ?", [$name]);
        
        if ($excludeId) {
            $query->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) != ?", [$excludeId]);
        }
        
        return !$query->exists();
    }

    /**
     * Generate new application ID
     */
    public static function generateId()
    {
        return 'APP-' . strtoupper(substr(md5(uniqid() . time()), 0, 10));
    }
}
