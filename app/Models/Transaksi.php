<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_transaksi';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'trans_code';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trans_code',
        'trans_desc',
        'trans_coa_debet',
        'trans_coa_kredit',
        'trans_date',
        'trans_debet',
        'trans_kredit',
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trans_date' => 'date',
        'trans_debet' => 'decimal:2',
        'trans_kredit' => 'decimal:2',
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
    ];

    /**
     * Scope a query to only include active transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Scope a query to search transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('trans_code', 'like', '%' . $search . '%')
              ->orWhere('trans_desc', 'like', '%' . $search . '%')
              ->orWhere('trans_coa_debet', 'like', '%' . $search . '%')
              ->orWhere('trans_coa_kredit', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get the COA Debet relationship
     */
    public function coaDebet()
    {
        return $this->belongsTo(Coa::class, 'trans_coa_debet', 'coa_code');
    }

    /**
     * Get the COA Kredit relationship
     */
    public function coaKredit()
    {
        return $this->belongsTo(Coa::class, 'trans_coa_kredit', 'coa_code');
    }
}
