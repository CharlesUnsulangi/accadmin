<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearlyClosing extends Model
{
    protected $table = 'tr_acc_yearly_closing';

    protected $fillable = [
        'version_number', 'version_status', 'version_note', 'closing_year',
        'coa_code', 'coa_desc', 'coa_main_code', 'coa_main_desc',
        'coasub1_code', 'coasub1_desc', 'coasub2_code', 'coasub2_desc',
        'opening_debet', 'opening_kredit', 'opening_balance',
        'mutasi_debet', 'mutasi_kredit', 'mutasi_netto', 'jumlah_transaksi',
        'closing_debet', 'closing_kredit', 'closing_balance',
        'monthly_summary', 'is_closed', 'closed_at', 'closed_by',
        'superseded_at', 'superseded_by', 'superseded_by_version',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'closing_year' => 'integer',
        'opening_debet' => 'decimal:2',
        'opening_kredit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'mutasi_debet' => 'decimal:2',
        'mutasi_kredit' => 'decimal:2',
        'mutasi_netto' => 'decimal:2',
        'jumlah_transaksi' => 'integer',
        'closing_debet' => 'decimal:2',
        'closing_kredit' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'monthly_summary' => 'array',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
        'superseded_at' => 'datetime',
        'superseded_by_version' => 'integer',
    ];

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_code', 'coa_code');
    }

    public function scopeActive($query)
    {
        return $query->where('version_status', 'ACTIVE');
    }

    public function scopeYear($query, $year)
    {
        return $query->where('closing_year', $year);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function canEdit(): bool
    {
        return !$this->is_closed && $this->version_status === 'ACTIVE';
    }
}
