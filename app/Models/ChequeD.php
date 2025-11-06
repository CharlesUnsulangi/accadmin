<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequeD extends Model
{
    protected $table = 'ms_acc_cheque_d';
    
    public $timestamps = false;
    
    protected $primaryKey = null;
    
    public $incrementing = false;
    
    protected $fillable = [
        'cheque_code_h',
        'cheque_code_d',
        'cheque_date',
        'cheque_value_start',
        'cheque_value',
        'cheque_note',
        'cheque_purpose',
        'cheque_status',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'cheque_value_start' => 'decimal:2',
        'cheque_value' => 'decimal:2',
    ];

    /**
     * Relationship to cheque header (book)
     */
    public function header()
    {
        return $this->belongsTo(ChequeH::class, 'cheque_code_h', 'cheque_code_h');
    }

    /**
     * Get available cheques
     */
    public function scopeAvailable($query)
    {
        return $query->where('cheque_status', 'AVAILABLE');
    }

    /**
     * Get used cheques
     */
    public function scopeUsed($query)
    {
        return $query->where('cheque_status', 'USED');
    }

    /**
     * Get void cheques
     */
    public function scopeVoid($query)
    {
        return $query->where('cheque_status', 'VOID');
    }

    /**
     * Check if cheque is available
     */
    public function getIsAvailableAttribute()
    {
        return $this->cheque_status === 'AVAILABLE';
    }

    /**
     * Check if cheque is used
     */
    public function getIsUsedAttribute()
    {
        return $this->cheque_status === 'USED';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->cheque_status) {
            'AVAILABLE' => 'bg-success',
            'USED' => 'bg-secondary',
            'VOID' => 'bg-danger',
            default => 'bg-light text-dark'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->cheque_status) {
            'AVAILABLE' => 'Tersedia',
            'USED' => 'Terpakai',
            'VOID' => 'Void',
            default => $this->cheque_status
        };
    }
}
