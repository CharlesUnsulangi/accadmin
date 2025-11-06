<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiCoa extends Model
{
    protected $table = 'tr_acc_transaksi_coa';
    
    public $timestamps = false;
    
    protected $primaryKey = null;
    public $incrementing = false;
    
    protected $fillable = [
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
        'rec_comcode',
        'rec_areacode',
        'transcoa_code',
        'transcoa_transaksi_main_code',
        'transcoa_coa_desc',
        'transcoa_head_code',
        'transcoa_debet_value',
        'transcoa_credit_value',
        'transcoa_coa_date',
        'transcoa_coa_code',
        'transcoa_coa_type',
        'transcoa_statusposting',
        'transcoa_dateposting',
        'transcoa_statusapp',
        'transcoa_debet_value_ops',
        'transcoa_credit_value_ops',
        'transcoa_coa_date_ops',
        'BL_Code_h',
        'BL_Date',
        'BL_Operator',
        'id',
    ];

    protected $casts = [
        'transcoa_debet_value' => 'decimal:2',
        'transcoa_credit_value' => 'decimal:2',
        'transcoa_debet_value_ops' => 'decimal:2',
        'transcoa_credit_value_ops' => 'decimal:2',
        'transcoa_coa_date' => 'date',
        'transcoa_coa_date_ops' => 'date',
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'transcoa_dateposting' => 'datetime',
        'BL_Date' => 'datetime',
    ];

    /**
     * Relationship to COA
     */
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'transcoa_coa_code', 'coa_code');
    }
}
