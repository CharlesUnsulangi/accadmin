<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequeH extends Model
{
    protected $table = 'ms_acc_cheque_h';
    
    public $timestamps = false;
    
    protected $primaryKey = 'cheque_code_h';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    protected $fillable = [
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
        'cheque_code_h',
        'cheque_desc',
        'cheque_coacode',
        'cheque_resino',
        'cheque_startno',
        'cheque_endno',
        'cheque_bank',
        'cheque_rek',
        'cheque_cabang',
        'cheque_txt',
        'cheque_type',
    ];

    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
    ];

    /**
     * Relationship to cheque details (individual cheques)
     */
    public function details()
    {
        return $this->hasMany(ChequeD::class, 'cheque_code_h', 'cheque_code_h');
    }

    /**
     * Relationship to COA
     */
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'cheque_coacode', 'coa_code');
    }

    /**
     * Get active cheques only
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Get total cheques in this book
     */
    public function getTotalChequesAttribute()
    {
        return $this->details()->count();
    }

    /**
     * Get used cheques count
     */
    public function getUsedChequesAttribute()
    {
        return $this->details()->where('cheque_status', 'USED')->count();
    }

    /**
     * Get available cheques count
     */
    public function getAvailableChequesAttribute()
    {
        return $this->details()->where('cheque_status', 'AVAILABLE')->count();
    }

    /**
     * Get total value of all cheques
     */
    public function getTotalValueAttribute()
    {
        return $this->details()->sum('cheque_value');
    }
}
