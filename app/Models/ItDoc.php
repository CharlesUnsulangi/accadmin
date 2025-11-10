<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItDoc extends Model
{
    protected $table = 'tr_admin_it_doc';
    
    protected $primaryKey = 'tr_admin_it_doc_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'catatan_text',
        'created_date',
        'created_user',
        'topik',
        'project',
        'link',
    ];

    protected $casts = [
        'created_date' => 'date',
    ];

    /**
     * Get documentation by topic
     */
    public function scopeByTopic($query, $topic)
    {
        return $query->where('topik', $topic);
    }

    /**
     * Get documentation by project
     */
    public function scopeByProject($query, $project)
    {
        return $query->where('project', $project);
    }

    /**
     * Get recent documentation
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_date', 'desc')->limit($limit);
    }

    /**
     * Search documentation
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('topik', 'like', "%{$search}%")
              ->orWhere('catatan_text', 'like', "%{$search}%")
              ->orWhere('project', 'like', "%{$search}%");
        });
    }
}
