<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CoaSub2 Model - LEGACY Level 3 (Reference Only)
 * 
 * Table: ms_acc_coasub2
 * PK: coasub2_code
 * FK: coasub2_coasub1code → ms_acc_coasub1.coasub1_code
 * 
 * STATUS: 📦 LEGACY - Hanya untuk referensi
 * ACTIVE SYSTEM: Gunakan H1-H6 di ms_acc_coa
 * 
 * @property string $coasub2_code Primary Key
 * @property string $coasub2_id
 * @property string $coasub2_desc
 * @property string $coasub2_coasub1code FK to CoaSub1
 * @property string $rec_status
 * @property string $rec_usercreated
 * @property string $rec_userupdate
 * @property \DateTime $rec_datecreated
 * @property \DateTime $rec_dateupdate
 */
class CoaSub2 extends Model
{
    protected $table = 'ms_acc_coasub2';
    protected $primaryKey = 'coasub2_code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $fillable = [
        'coasub2_code',
        'coasub2_id',
        'coasub2_desc',
        'coasub2_coasub1code',
        'rec_status',
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
    ];

    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
    ];

    /**
     * Relationship: CoaSub2 belongs to CoaSub1 (Parent)
     */
    public function coaSub1()
    {
        return $this->belongsTo(CoaSub1::class, 'coasub2_coasub1code', 'coasub1_code');
    }

    /**
     * Relationship: CoaSub2 has many COAs (Children)
     */
    public function coas()
    {
        return $this->hasMany(Coa::class, 'coa_coasub2code', 'coasub2_code');
    }

    /**
     * Scope: Active records only
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }
}
