<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiMain extends Model
{
    protected $table = 'tr_acc_transaksi_main';
    
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
        'transmain_code',
        'transmain_codetransaksi',
        'transmain_desc',
        'transmain_ms_transcode',
        'transmain_value',
        'transmain_date',
        'transmain_document_note',
        'transmain_operator',
        'transmain_document_date',
        'transmain_document_time',
        'transmain_ms_cashflow_code',
        'transmain_value_ops',
        'transmain_date_ops',
    ];

    protected $casts = [
        'transmain_value' => 'decimal:2',
        'transmain_value_ops' => 'decimal:2',
        'transmain_date' => 'datetime',
        'transmain_date_ops' => 'datetime',
        'transmain_document_date' => 'date',
        'transmain_document_time' => 'date',
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
    ];

    /**
     * Relationship to detail lines
     */
    public function details()
    {
        return $this->hasMany(TransaksiCoa::class, 'transcoa_transaksi_main_code', 'transmain_code')
                    ->where('rec_comcode', $this->rec_comcode)
                    ->where('rec_areacode', $this->rec_areacode);
    }
}
