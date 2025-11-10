<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TotalAudit extends Model
{
    protected $table = 'tr_acc_total_audit';
    protected $primaryKey = 'total_audit_id';
    public $timestamps = false;

    protected $fillable = [
        'audit_period', 'coa_main_id', 'coasub1_id', 'coasub2_id', 'coa_id',
        'saldo_awal', 'saldo_akhir', 'audit_result', 'version_number', 'version_status',
        'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function scopeActive(Builder $query)
    {
        return $query->where('version_status', 'ACTIVE');
    }
    public function scopeVersionHistory(Builder $query, $period)
    {
        return $query->where('audit_period', $period)->orderBy('version_number', 'desc');
    }
    public function scopeCanModify(Builder $query)
    {
        return $query->where('version_status', 'DRAFT');
    }
    public function coaMain()
    {
        return $this->belongsTo(CoaMain::class, 'coa_main_id');
    }
    public function coaSub1()
    {
        return $this->belongsTo(CoaSub1::class, 'coasub1_id');
    }
    public function coaSub2()
    {
        return $this->belongsTo(CoaSub2::class, 'coasub2_id');
    }
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
