<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_vendor';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ven_code';

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
        'ven_code',
        'ven_name',
        'ven_pic',
        'ven_addrase',
        'ven_phone',
        'ven_email',
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
     * Scope a query to only include active vendors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Scope a query to search vendors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('ven_code', 'like', '%' . $search . '%')
              ->orWhere('ven_name', 'like', '%' . $search . '%')
              ->orWhere('ven_pic', 'like', '%' . $search . '%')
              ->orWhere('ven_addrase', 'like', '%' . $search . '%')
              ->orWhere('ven_email', 'like', '%' . $search . '%');
        });
    }
}
