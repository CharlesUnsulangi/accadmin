<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_bank';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Bank_Code';

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
        'Bank_Code',
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
     * Scope a query to only include active banks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Scope a query to search banks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('Bank_Code', 'like', '%' . $search . '%');
    }
}
