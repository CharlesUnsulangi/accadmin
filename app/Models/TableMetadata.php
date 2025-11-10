<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableMetadata extends Model
{
    protected $table = 'tr_admin_it_aplikasi_table';
    
    protected $primaryKey = 'tr_aplikasi_table_id';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'tr_aplikasi_table_id',
        'ms_aplikasi_id',
        'table_name',
        'table_type',
        'table_note',
        'note_schema',
        'id',
        'record',
        'record_date_start',
        'record_date_last',
        'date_updated',
        'cek_priority'
    ];
    
    protected $casts = [
        'record' => 'integer',
        'record_date_start' => 'date',
        'record_date_last' => 'date',
        'date_updated' => 'datetime',
        'cek_priority' => 'boolean'
    ];
    
    /**
     * Get all messages for this table
     */
    public function messages()
    {
        return $this->hasMany(TableMessage::class, 'tr_aplikasi_table_id', 'tr_aplikasi_table_id')
            ->orderBy('date_created', 'desc');
    }
    
    /**
     * Get access logs for this table
     */
    public function accessLogs()
    {
        return $this->hasMany(TableAccessLog::class, 'table_name', 'table_name')
            ->orderBy('accessed_at', 'desc');
    }
    
    /**
     * Get the most recent message
     */
    public function latestMessage()
    {
        return $this->hasOne(TableMessage::class, 'tr_aplikasi_table_id', 'tr_aplikasi_table_id')
            ->latestOfMany('date_created');
    }
    
    /**
     * Get message count
     */
    public function getMessageCountAttribute()
    {
        return $this->messages()->count();
    }
    
    /**
     * Get access count
     */
    public function getAccessCountAttribute()
    {
        return $this->accessLogs()->count();
    }
    
    /**
     * Scope to search tables
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->whereRaw("CAST(table_name AS VARCHAR(MAX)) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CAST(note_schema AS VARCHAR(MAX)) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CAST(table_note AS VARCHAR(MAX)) LIKE ?", ["%{$search}%"]);
        }
        return $query;
    }
    
    /**
     * Find by table name (handles TEXT column)
     */
    public static function findByTableName($tableName)
    {
        return self::whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])->first();
    }
    
    /**
     * Find by table ID
     */
    public static function findByTableId($tableId)
    {
        return self::where('tr_aplikasi_table_id', $tableId)->first();
    }
    
    /**
     * Scope to get tables with messages
     */
    public function scopeWithMessages($query)
    {
        return $query->has('messages');
    }
    
    /**
     * Scope to get tables without messages
     */
    public function scopeWithoutMessages($query)
    {
        return $query->doesntHave('messages');
    }
}
