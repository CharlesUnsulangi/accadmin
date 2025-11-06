<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CoaSub1 Model - LEGACY Level 2 (Reference Only)
 * 
 * Table: ms_acc_coasub1
 * PK: coasub1_code
 * FK: coasub1_maincode → ms_acc_coa_main.coa_main_code
 * 
 * STATUS: 📦 LEGACY - Hanya untuk referensi
 * ACTIVE SYSTEM: Gunakan H1-H6 di ms_acc_coa
 * 
 * @property string $coasub1_code Primary Key
 * @property string $coasub1_id
 * @property string $coasub1_desc
 * @property string $coasub1_maincode FK to CoaMain
 * @property string $rec_status
 * @property string $rec_usercreated
 * @property string $rec_userupdate
 * @property \DateTime $rec_datecreated
 * @property \DateTime $rec_dateupdate
 */
class CoaSub1 extends Model
{
    protected $table = 'ms_acc_coasub1';
    protected $primaryKey = 'coasub1_code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $fillable = [
        'coasub1_code',
        'coasub1_id',
        'coasub1_desc',
        'coasub1_maincode',
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
     * Relationship: CoaSub1 belongs to CoaMain (Parent)
     */
    public function coaMain()
    {
        return $this->belongsTo(CoaMain::class, 'coasub1_maincode', 'coa_main_code');
    }

    /**
     * Relationship: CoaSub1 has many CoaSub2 (Children)
     */
    public function coaSub2s()
    {
        return $this->hasMany(CoaSub2::class, 'coasub2_coasub1code', 'coasub1_code');
    }

    /**
     * Scope: Active records only
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }
}
