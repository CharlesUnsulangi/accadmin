<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationTopic extends Model
{
    protected $table = 'ms_admin_it_aplikasi_topic';
    protected $primaryKey = 'ms_admin_it_topic';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'ms_admin_it_topic',
        'topic_desc',
        'value_priority',
        'ms_admin_it_aplikasi_id'
    ];

    protected $casts = [
        'ms_admin_it_topic' => 'integer',
        'value_priority' => 'integer',
    ];

    /**
     * Relationship: Topic belongs to one Application
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'ms_admin_it_aplikasi_id', 'ms_admin_it_aplikasi_id');
    }

    /**
     * Get topics for specific application
     */
    public static function getByApplication($aplikasiId)
    {
        return self::whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$aplikasiId])
            ->orderBy('value_priority', 'asc')
            ->orderBy('topic_desc', 'asc')
            ->get();
    }

    /**
     * Get next topic ID
     */
    public static function getNextTopicId()
    {
        $max = self::max('ms_admin_it_topic');
        return ($max ?? 0) + 1;
    }
}
