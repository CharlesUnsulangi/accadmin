<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiCheque extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tr_acc_transaksi_cheque';

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
        return ['rec_comcode', 'rec_areacode', 'transcheque_code'];
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
        'transcheque_code',
        'transcheque_transmaincode',
        'transcheque_vendor',
        'transcheque_value',
        'transcheque_date',
        'transcheque_doc',
        'transcheque_status',
        'transcheque_desc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transcheque_date' => 'date',
        'transcheque_value' => 'decimal:2',
    ];

    /**
     * Get the cheque details for this transaction.
     */
    public function details()
    {
        return $this->hasMany(TransaksiChequeD::class, ['rec_comcode', 'rec_areacode', 'transcheque_code'], ['rec_comcode', 'rec_areacode', 'transcheque_code']);
    }

    /**
     * Get the related journal transaction.
     */
    public function transaksiMain()
    {
        return $this->belongsTo(TransaksiMain::class, 'transcheque_transmaincode', 'transmain_code');
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

    /**
     * Get status badge class.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->transcheque_status) {
            'PENDING' => 'bg-warning',
            'APPROVED' => 'bg-success',
            'PAID' => 'bg-info',
            'CANCELLED' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Scope to search by code, vendor, or doc.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('transcheque_code', 'like', '%' . $search . '%')
                  ->orWhere('transcheque_vendor', 'like', '%' . $search . '%')
                  ->orWhere('transcheque_doc', 'like', '%' . $search . '%')
                  ->orWhere('transcheque_desc', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * Scope to filter by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('transcheque_status', $status);
        }
        return $query;
    }

    /**
     * Scope to filter by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if (!empty($startDate) && !empty($endDate)) {
            return $query->whereBetween('transcheque_date', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            return $query->where('transcheque_date', '>=', $startDate);
        } elseif (!empty($endDate)) {
            return $query->where('transcheque_date', '<=', $endDate);
        }
        return $query;
    }
}
