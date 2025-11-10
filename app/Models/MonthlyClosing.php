<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MonthlyClosing Model
 * 
 * Table: tr_acc_monthly_closing
 * Purpose: Rekap closing bulanan dengan version control
 * 
 * @property int $id
 * @property int $version_number
 * @property string $version_status DRAFT|ACTIVE|SUPERSEDED|ARCHIVED
 * @property string|null $version_note
 * @property int $closing_year
 * @property int $closing_month
 * @property string $closing_periode_id YYYYMM
 * @property string $coa_code
 * @property string|null $coa_desc
 * @property string|null $coa_main_code
 * @property string|null $coa_main_desc
 * @property float $opening_debet
 * @property float $opening_kredit
 * @property float $opening_balance
 * @property float $mutasi_debet
 * @property float $mutasi_kredit
 * @property float $mutasi_netto
 * @property int $jumlah_transaksi
 * @property float $closing_debet
 * @property float $closing_kredit
 * @property float $closing_balance
 * @property bool $is_closed
 * @property \Carbon\Carbon|null $closed_at
 * @property string|null $closed_by
 * @property \Carbon\Carbon|null $superseded_at
 * @property string|null $superseded_by
 * @property int|null $superseded_by_version
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MonthlyClosing extends Model
{
    protected $table = 'tr_acc_monthly_closing';

    protected $fillable = [
        'version_number',
        'version_status',
        'version_note',
        'closing_year',
        'closing_month',
        'closing_periode_id',
        'coa_code',
        'coa_desc',
        'coa_main_code',
        'coa_main_desc',
        'coasub1_code',
        'coasub1_desc',
        'coasub2_code',
        'coasub2_desc',
        'opening_debet',
        'opening_kredit',
        'opening_balance',
        'mutasi_debet',
        'mutasi_kredit',
        'mutasi_netto',
        'jumlah_transaksi',
        'closing_debet',
        'closing_kredit',
        'closing_balance',
        'is_closed',
        'closed_at',
        'closed_by',
        'superseded_at',
        'superseded_by',
        'superseded_by_version',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'closing_year' => 'integer',
        'closing_month' => 'integer',
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
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
        'superseded_at' => 'datetime',
        'superseded_by_version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to COA
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_code', 'coa_code');
    }

    /**
     * Scope: Active version only
     */
    public function scopeActive($query)
    {
        return $query->where('version_status', 'ACTIVE');
    }

    /**
     * Scope: Specific periode
     */
    public function scopePeriode($query, $year, $month)
    {
        return $query->where('closing_year', $year)
                     ->where('closing_month', $month);
    }

    /**
     * Scope: Closed only
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Scope: Not closed
     */
    public function scopeNotClosed($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Get periode name
     */
    public function getPeriodeNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$this->closing_month] . ' ' . $this->closing_year;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->version_status) {
            'DRAFT' => 'badge bg-secondary',
            'ACTIVE' => 'badge bg-success',
            'SUPERSEDED' => 'badge bg-warning',
            'ARCHIVED' => 'badge bg-info',
            default => 'badge bg-light'
        };
    }

    /**
     * Get closing status badge
     */
    public function getClosingStatusBadgeAttribute(): string
    {
        if ($this->is_closed) {
            return '<span class="badge bg-success"><i class="fas fa-lock me-1"></i>Closed</span>';
        }
        return '<span class="badge bg-warning"><i class="fas fa-unlock me-1"></i>Open</span>';
    }

    /**
     * Check if can be edited
     */
    public function canEdit(): bool
    {
        return !$this->is_closed && $this->version_status === 'ACTIVE';
    }

    /**
     * Check if can be deleted
     */
    public function canDelete(): bool
    {
        return !$this->is_closed && $this->version_status === 'DRAFT';
    }
}
