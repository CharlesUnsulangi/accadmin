<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Ms_Area';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Are_Code';

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
        'Are_Code',
        'Are_Name',
        'Are_Address',
        'Are_ContactNo',
        'Are_Fax',
        'Are_PIC',
        'Are_PIChp',
        'Are_Email',
        'Are_db',
        'Are_dbtype',
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
     * Scope a query to only include active areas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }

    /**
     * Scope a query to search areas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('Are_Code', 'like', '%' . $search . '%')
              ->orWhere('Are_Name', 'like', '%' . $search . '%')
              ->orWhere('Are_Address', 'like', '%' . $search . '%')
              ->orWhere('Are_PIC', 'like', '%' . $search . '%');
        });
    }
}
