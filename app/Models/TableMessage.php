<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TableMessage extends Model
{
    protected $table = 'tr_admin_it_aplikasi_table_msg';
    
    protected $primaryKey = 'tr_admin_it_aplikasi_table_msg_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'tr_admin_it_aplikasi_table_msg_id',
        'tr_aplikasi_table_id',
        'msg_desc',
        'user_created',
        'date_created'
    ];
    
    protected $casts = [
        'date_created' => 'date'
    ];
    
    /**
     * Get the next available ID
     */
    public static function getNextId()
    {
        $maxId = self::max('tr_admin_it_aplikasi_table_msg_id');
        return $maxId ? $maxId + 1 : 1;
    }
    
    /**
     * Add a message for a table and update table_note
     */
    public static function addMessage($tableId, $message, $user = null)
    {
        // Create the message
        $newMessage = self::create([
            'tr_admin_it_aplikasi_table_msg_id' => self::getNextId(),
            'tr_aplikasi_table_id' => $tableId,
            'msg_desc' => $message,
            'user_created' => $user ?? auth()->user()?->name ?? 'System',
            'date_created' => now()
        ]);
        
        // Update table_note in parent table with the latest message
        self::updateTableNote($tableId, $message);
        
        return $newMessage;
    }
    
    /**
     * Update table_note with latest message
     */
    public static function updateTableNote($tableId, $message = null)
    {
        // If message not provided, get the latest message
        if (!$message) {
            $latestMessage = self::where('tr_aplikasi_table_id', $tableId)
                ->orderBy('date_created', 'desc')
                ->first();
            
            $message = $latestMessage ? $latestMessage->msg_desc : null;
        }
        
        // Update table_note in tr_admin_it_aplikasi_table
        DB::table('tr_admin_it_aplikasi_table')
            ->where('tr_aplikasi_table_id', $tableId)
            ->update([
                'table_note' => $message,
                'date_updated' => now()
            ]);
    }
    
    /**
     * Delete message and update table_note
     */
    public function delete()
    {
        $tableId = $this->tr_aplikasi_table_id;
        
        // Delete the message
        $deleted = parent::delete();
        
        // Update table_note with the new latest message (or null if no messages left)
        self::updateTableNote($tableId);
        
        return $deleted;
    }
    
    /**
     * Get all messages for a table
     */
    public static function getTableMessages($tableId)
    {
        return self::where('tr_aplikasi_table_id', $tableId)
            ->orderBy('date_created', 'desc')
            ->get();
    }
    
    /**
     * Relationship to table metadata
     */
    public function tableMetadata()
    {
        return $this->belongsTo(TableMetadata::class, 'tr_aplikasi_table_id', 'tr_aplikasi_table_id');
    }
    
    /**
     * Get messages with table information
     */
    public static function getMessagesWithTableInfo($tableId)
    {
        return self::with('tableMetadata')
            ->where('tr_aplikasi_table_id', $tableId)
            ->orderBy('date_created', 'desc')
            ->get();
    }
}

