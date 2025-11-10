<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCheque extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_acc_statuscheque';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'stacheq_code';

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
        'stacheq_code',
        'stacheq_desc',
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
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
    ];

    /**
     * Scope a query to only include active status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Scope a query to search status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('stacheq_code', 'like', '%' . $search . '%')
              ->orWhere('stacheq_desc', 'like', '%' . $search . '%');
        });
    }
}
