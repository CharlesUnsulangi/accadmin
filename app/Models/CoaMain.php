<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CoaMain Model - LEGACY Level 1 (Reference Only)
 * 
 * Table: ms_acc_coa_main
 * PK: coa_main_code
 * 
 * STATUS: 📦 LEGACY - Hanya untuk referensi
 * ACTIVE SYSTEM: Gunakan H1-H6 di ms_acc_coa
 * 
 * @property string $coa_main_code Primary Key
 * @property string $coa_main_id
 * @property string $coa_main_desc
 * @property string $coa_main_coamain2code
 * @property int $id Auto-increment ID
 * @property bool $cek_aktif Check active flag
 * @property int $id_h Hierarchy ID
 * @property string $rec_status
 * @property string $rec_usercreated
 * @property string $rec_userupdate
 * @property \DateTime $rec_datecreated
 * @property \DateTime $rec_dateupdate
 */
class CoaMain extends Model
{
    protected $table = 'ms_acc_coa_main';
    protected $primaryKey = 'coa_main_code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $fillable = [
        'coa_main_code',
        'coa_main_id',
        'coa_main_desc',
        'coa_main_coamain2code',
        'id',
        'cek_aktif',
        'id_h',
        'rec_status',
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
    ];

    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'cek_aktif' => 'boolean',
        'id' => 'integer',
        'id_h' => 'integer',
    ];

    /**
     * Relationship: CoaMain has many CoaSub1 (Children)
     */
    public function coaSub1s()
    {
        return $this->hasMany(CoaSub1::class, 'coasub1_maincode', 'coa_main_code');
    }

    /**
     * Scope: Active records only
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1');
    }
}
