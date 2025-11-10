<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiChequeD extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tr_acc_transaksi_cheque_d';

    /**
     * The primary key associated with the table.
     * Note: Composite keys don't work well with Eloquent, so we use null
     *
     * @var string|null
     */
    protected $primaryKey = null;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKeyName()
    {
        return ['rec_comcode', 'rec_areacode', 'transcheque_code_h', 'transcheque_no'];
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

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
        'rec_comcode',
        'rec_areacode',
        'transcheque_code_h',
        'transcheque_no',
        'transcheque_coa',
        'transcheque_value',
        'transcheque_datedoc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transcheque_datedoc' => 'date',
        'transcheque_value' => 'decimal:2',
    ];

    /**
     * Get the header transaction.
     */
    public function header()
    {
        return $this->belongsTo(TransaksiCheque::class, ['rec_comcode', 'rec_areacode', 'transcheque_code_h'], ['rec_comcode', 'rec_areacode', 'transcheque_code']);
    }

    /**
     * Get the related cheque from master.
     */
    public function cheque()
    {
        return $this->belongsTo(ChequeD::class, 'transcheque_no', 'cheque_code_d');
    }

    /**
     * Get the related COA.
     */
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'transcheque_coa', 'coa_code');
    }

    /**
     * Get formatted value.
     *
     * @return string
     */
    public function getFormattedValueAttribute()
    {
        if ($this->transcheque_value === null) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->transcheque_value, 2, ',', '.');
    }
}
