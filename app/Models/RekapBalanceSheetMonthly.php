<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapBalanceSheetMonthly extends Model
{
    protected $table = 'tr_acc_rekap_balance_sheet_monthly';
    
    protected $fillable = [
        'periode_year',
        'periode_month',
        'periode_id',
        'coa_code',
        'coa_desc',
        'coa_main_code',
        'coa_main_desc',
        'saldo_awal_debet',
        'saldo_awal_kredit',
        'saldo_awal',
        'tanggal_pertama',
        'tanggal_terakhir',
        'jumlah_transaksi',
        'total_debet',
        'total_kredit',
        'mutasi_netto',
        'saldo_akhir_debet',
        'saldo_akhir_kredit',
        'saldo_akhir',
        'usercreated',
    ];
    
    protected $casts = [
        'periode_year' => 'integer',
        'periode_month' => 'integer',
        'jumlah_transaksi' => 'integer',
        'saldo_awal_debet' => 'decimal:2',
        'saldo_awal_kredit' => 'decimal:2',
        'saldo_awal' => 'decimal:2',
        'total_debet' => 'decimal:2',
        'total_kredit' => 'decimal:2',
        'mutasi_netto' => 'decimal:2',
        'saldo_akhir_debet' => 'decimal:2',
        'saldo_akhir_kredit' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
        'tanggal_pertama' => 'date',
        'tanggal_terakhir' => 'date',
    ];
    
    // Relationships
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_code', 'coa_code');
    }
    
    public function coaMain()
    {
        return $this->belongsTo(CoaMain::class, 'coa_main_code', 'coa_main_code');
    }
    
    // Scopes
    public function scopePeriode($query, $year, $month)
    {
        $periodeId = sprintf('%04d%02d', $year, $month);
        return $query->where('periode_id', $periodeId);
    }
    
    public function scopeByCategory($query, $category)
    {
        return $query->where('coa_main_code', $category);
    }
    
    public function scopeAsset($query)
    {
        return $query->where('coa_main_code', '1'); // Assuming 1 = Asset
    }
    
    public function scopeLiability($query)
    {
        return $query->where('coa_main_code', '2'); // Assuming 2 = Liability  
    }
    
    public function scopeEquity($query)
    {
        return $query->where('coa_main_code', '3'); // Assuming 3 = Equity
    }
    
    public function scopeRevenue($query)
    {
        return $query->where('coa_main_code', '4'); // Assuming 4 = Revenue
    }
    
    public function scopeExpense($query)
    {
        return $query->where('coa_main_code', '5'); // Assuming 5 = Expense
    }
}
