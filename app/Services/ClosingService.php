<?php

namespace App\Services;

use App\Models\TransaksiCoa;
use App\Models\MonthlyClosing;
use App\Models\YearlyClosing;
use App\Models\Coa;
use App\Models\CoaSub2;
use App\Models\CoaSub1;
use App\Models\CoaMain;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ClosingService - Service untuk 3 Layer Closing System
 * 
 * Layer 1: Monthly Closing - Rekap per bulan
 * Layer 2: Yearly Closing - Aggregate 12 bulan
 * Layer 3: Audit Calculation - Hitung dari transaksi pertama (verification)
 */
class ClosingService
{
    /**
     * LAYER 1: Calculate Monthly Closing
     * 
     * Menghitung closing bulanan dari transaksi tr_acc_transaksi_coa
     * 
     * @param int $year Tahun (2024, 2025)
     * @param int $month Bulan (1-12)
     * @param bool $saveToDB Simpan ke database atau return saja
     * @return array
     */
    public function calculateMonthly(int $year, int $month, bool $saveToDB = false): array
    {
        $periodeId = sprintf('%04d%02d', $year, $month);
        $userName = auth()->check() ? auth()->user()->name : 'system';
        
        // Get distinct COA codes yang ada transaksinya di periode ini
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $coaSummary = DB::table('tr_acc_transaksi_coa')
            ->whereBetween('transcoa_coa_date_ops', [$startDate, $endDate])
            ->whereNotNull('transcoa_coa_code')
            ->where('transcoa_coa_code', '!=', '')
            ->where('transcoa_coa_code', '!=', 'NONE')
            ->groupBy('transcoa_coa_code')
            ->selectRaw('
                transcoa_coa_code as coa_code,
                SUM(COALESCE(transcoa_debet_value_ops, 0)) as total_debet,
                SUM(COALESCE(transcoa_credit_value_ops, 0)) as total_kredit,
                COUNT(*) as jumlah_transaksi
            ')
            ->get();
        
        $results = [];
        
        foreach ($coaSummary as $summary) {
            // 1. Get Opening Balance (dari closing bulan sebelumnya)
            $openingBalance = $this->getOpeningBalance($summary->coa_code, $year, $month);
            
            // 2. Mutasi sudah dihitung di query GROUP BY di atas
            $mutasiDebet = $summary->total_debet ?? 0;
            $mutasiKredit = $summary->total_kredit ?? 0;
            $mutasiNetto = $mutasiDebet - $mutasiKredit;
            $jumlahTransaksi = $summary->jumlah_transaksi ?? 0;
            
            // 3. Calculate Closing Balance
            $closingBalance = $openingBalance['opening_balance'] + $mutasiNetto;
            
            // Determine debet/kredit position
            $closingDebet = $closingBalance >= 0 ? abs($closingBalance) : 0;
            $closingKredit = $closingBalance < 0 ? abs($closingBalance) : 0;
            
            // Try to get hierarchy from master COA, NULL if not found
            // Don't use eager loading here to avoid FK issues
            $coa = Coa::where('coa_code', $summary->coa_code)->first();
            
            // Get coa_desc from master ms_acc_coa, jika tidak ada tulis "Tidak Terdaftar"
            $coaDesc = $coa ? $coa->coa_desc : 'Tidak Terdaftar';
            
            $coaSub2 = null;
            $coaSub1 = null;
            $coaMain = null;
            
            if ($coa && $coa->coa_coasub2code) {
                $coaSub2 = CoaSub2::where('coasub2_code', $coa->coa_coasub2code)->first();
                if ($coaSub2 && $coaSub2->coasub2_coasub1code) {
                    $coaSub1 = CoaSub1::where('coasub1_code', $coaSub2->coasub2_coasub1code)->first();
                    if ($coaSub1 && $coaSub1->coa_main1_code) {
                        $coaMain = CoaMain::where('coa_main_code', $coaSub1->coa_main1_code)->first();
                    }
                }
            }
            
            $data = [
                'version_number' => 1,
                'version_status' => 'ACTIVE', // Langsung ACTIVE karena auto-generated
                'closing_year' => $year,
                'closing_month' => $month,
                'closing_periode_id' => $periodeId,
                'coa_code' => $summary->coa_code,
                'coa_desc' => $coaDesc,
                // Level 1: Main
                'coa_main_code' => $coaMain?->coa_main_code ?? null,
                'coa_main_desc' => $coaMain?->coa_main_desc ?? null,
                // Level 2: Sub1
                'coasub1_code' => $coaSub1?->coasub1_code ?? null,
                'coasub1_desc' => $coaSub1?->coasub1_desc ?? null,
                // Level 3: Sub2
                'coasub2_code' => $coaSub2?->coasub2_code ?? null,
                'coasub2_desc' => $coaSub2?->coasub2_desc ?? null,
                'opening_debet' => $openingBalance['opening_debet'],
                'opening_kredit' => $openingBalance['opening_kredit'],
                'opening_balance' => $openingBalance['opening_balance'],
                'mutasi_debet' => $mutasiDebet,
                'mutasi_kredit' => $mutasiKredit,
                'mutasi_netto' => $mutasiNetto,
                'jumlah_transaksi' => $jumlahTransaksi,
                'closing_debet' => $closingDebet,
                'closing_kredit' => $closingKredit,
                'closing_balance' => $closingBalance,
                'is_closed' => false,
                'created_by' => $userName,
            ];
            
            if ($saveToDB) {
                MonthlyClosing::create($data);
            }
            
            $results[] = $data;
        }
        
        return $results;
    }

    /**
     * LAYER 2: Calculate Yearly Closing
     * 
     * Aggregate dari 12 monthly closing ATAU langsung dari transaksi
     * 
     * @param int $year Tahun (2024, 2025)
     * @param bool $saveToDB Simpan ke database atau return saja
     * @param bool $fromTransactions Hitung langsung dari transaksi (true) atau dari monthly closing (false)
     * @return array
     */
    public function calculateYearly(int $year, bool $saveToDB = false, bool $fromTransactions = true): array
    {
        $userName = auth()->check() ? auth()->user()->name : 'system';
        
        if ($fromTransactions) {
            // Calculate langsung dari transaksi tahun ini
            return $this->calculateYearlyFromTransactions($year, $saveToDB, $userName);
        }
        
        // Calculate dari monthly closing (metode lama)
        return $this->calculateYearlyFromMonthly($year, $saveToDB, $userName);
    }

    /**
     * Calculate yearly closing langsung dari transaksi
     * Dengan carry forward: COA dari tahun sebelumnya tetap dibawa meskipun tidak ada transaksi
     */
    private function calculateYearlyFromTransactions(int $year, bool $saveToDB, string $userName): array
    {
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();
        
        // Step 1: Get COA yang ada transaksi di tahun ini
        $transactionCOAs = DB::table('tr_acc_transaksi_coa')
            ->whereBetween('transcoa_coa_date_ops', [$startDate, $endDate])
            ->whereNotNull('transcoa_coa_code')
            ->where('transcoa_coa_code', '!=', '')
            ->where('transcoa_coa_code', '!=', 'NONE')
            ->groupBy('transcoa_coa_code')
            ->selectRaw('
                transcoa_coa_code as coa_code,
                SUM(COALESCE(transcoa_debet_value_ops, 0)) as total_debet,
                SUM(COALESCE(transcoa_credit_value_ops, 0)) as total_kredit,
                COUNT(*) as jumlah_transaksi
            ')
            ->get()
            ->keyBy('coa_code'); // Index by coa_code untuk lookup
        
        // Step 2: Get COA dari tahun sebelumnya (untuk carry forward)
        $previousYearCOAs = YearlyClosing::where('closing_year', $year - 1)
            ->where('version_status', 'ACTIVE')
            ->get()
            ->keyBy('coa_code'); // Index by coa_code
        
        // Step 3: Merge - semua COA dari tahun lalu + COA baru tahun ini
        $allCOACodes = collect($previousYearCOAs->keys())
            ->merge($transactionCOAs->keys())
            ->unique()
            ->sort()
            ->values();
        
        $results = [];
        
        // Step 4: Loop semua COA (dari tahun lalu + tahun ini)
        foreach ($allCOACodes as $coaCode) {
            // Get opening balance dari tahun sebelumnya
            $previousYear = $previousYearCOAs->get($coaCode);
            $openingBalance = $previousYear ? $previousYear->closing_balance : 0;
            $openingDebet = $previousYear ? $previousYear->closing_debet : 0;
            $openingKredit = $previousYear ? $previousYear->closing_kredit : 0;
            
            // Get mutasi dari transaksi tahun ini (bisa 0 jika tidak ada transaksi)
            $transaction = $transactionCOAs->get($coaCode);
            $mutasiDebet = $transaction ? $transaction->total_debet : 0;
            $mutasiKredit = $transaction ? $transaction->total_kredit : 0;
            $mutasiNetto = $mutasiDebet - $mutasiKredit;
            $jumlahTransaksi = $transaction ? $transaction->jumlah_transaksi : 0;
            
            // Closing balance tahun ini
            $closingBalance = $openingBalance + $mutasiNetto;
            $closingDebet = $closingBalance >= 0 ? abs($closingBalance) : 0;
            $closingKredit = $closingBalance < 0 ? abs($closingBalance) : 0;
            
            // Try to get hierarchy from master COA, NULL if not found
            // Don't use eager loading here to avoid FK issues
            $coa = Coa::where('coa_code', $coaCode)->first();
            
            // Get coa_desc from master ms_acc_coa, jika tidak ada tulis "Tidak Terdaftar"
            $coaDesc = $coa ? $coa->coa_desc : 'Tidak Terdaftar';
            
            $coaSub2 = null;
            $coaSub1 = null;
            $coaMain = null;
            
            if ($coa && $coa->coa_coasub2code) {
                $coaSub2 = CoaSub2::where('coasub2_code', $coa->coa_coasub2code)->first();
                if ($coaSub2 && $coaSub2->coasub2_coasub1code) {
                    $coaSub1 = CoaSub1::where('coasub1_code', $coaSub2->coasub2_coasub1code)->first();
                    if ($coaSub1 && $coaSub1->coa_main1_code) {
                        $coaMain = CoaMain::where('coa_main_code', $coaSub1->coa_main1_code)->first();
                    }
                }
            }
            
            $data = [
                'version_number' => 1,
                'version_status' => 'ACTIVE', // Langsung ACTIVE karena auto-generated
                'closing_year' => $year,
                'coa_code' => $coaCode,
                'coa_desc' => $coaDesc,
                // Level 1: Main
                'coa_main_code' => $coaMain?->coa_main_code ?? null,
                'coa_main_desc' => $coaMain?->coa_main_desc ?? null,
                // Level 2: Sub1
                'coasub1_code' => $coaSub1?->coasub1_code ?? null,
                'coasub1_desc' => $coaSub1?->coasub1_desc ?? null,
                // Level 3: Sub2
                'coasub2_code' => $coaSub2?->coasub2_code ?? null,
                'coasub2_desc' => $coaSub2?->coasub2_desc ?? null,
                'opening_debet' => $openingDebet,
                'opening_kredit' => $openingKredit,
                'opening_balance' => $openingBalance,
                'mutasi_debet' => $mutasiDebet,
                'mutasi_kredit' => $mutasiKredit,
                'mutasi_netto' => $mutasiNetto,
                'jumlah_transaksi' => $jumlahTransaksi,
                'closing_debet' => $closingDebet,
                'closing_kredit' => $closingKredit,
                'closing_balance' => $closingBalance,
                'monthly_summary' => null, // Tidak ada detail bulanan
                'is_closed' => false,
                'created_by' => $userName,
            ];
            
            if ($saveToDB) {
                YearlyClosing::create($data);
            }
            
            $results[] = $data;
        }
        
        return $results;
    }

    /**
     * Calculate yearly closing dari monthly closing (metode lama)
     */
    private function calculateYearlyFromMonthly(int $year, bool $saveToDB, string $userName): array
    {
        
        // Get all active COA with eager loading hierarchy
        $coas = Coa::where('rec_status', 'A')
            ->with(['coaSub2.coaSub1.coaMain'])
            ->get();
        
        $results = [];
        
        foreach ($coas as $coa) {
            // Get opening balance (dari yearly closing tahun sebelumnya)
            $previousYear = YearlyClosing::where('closing_year', $year - 1)
                ->where('coa_code', $coa->coa_code)
                ->where('version_status', 'ACTIVE')
                ->first();
            
            $openingBalance = $previousYear ? $previousYear->closing_balance : 0;
            $openingDebet = $previousYear ? $previousYear->closing_debet : 0;
            $openingKredit = $previousYear ? $previousYear->closing_kredit : 0;
            
            // Aggregate dari 12 monthly closing
            $monthlyData = MonthlyClosing::where('closing_year', $year)
                ->where('coa_code', $coa->coa_code)
                ->where('version_status', 'ACTIVE')
                ->orderBy('closing_month')
                ->get();
            
            $totalMutasiDebet = $monthlyData->sum('mutasi_debet');
            $totalMutasiKredit = $monthlyData->sum('mutasi_kredit');
            $totalMutasiNetto = $totalMutasiDebet - $totalMutasiKredit;
            $totalTransaksi = $monthlyData->sum('jumlah_transaksi');
            
            // Monthly summary untuk trace back
            $monthlySummary = [];
            foreach ($monthlyData as $monthly) {
                $monthlySummary[] = [
                    'month' => $monthly->closing_month,
                    'opening' => (float) $monthly->opening_balance,
                    'mutasi_debet' => (float) $monthly->mutasi_debet,
                    'mutasi_kredit' => (float) $monthly->mutasi_kredit,
                    'mutasi_netto' => (float) $monthly->mutasi_netto,
                    'closing' => (float) $monthly->closing_balance,
                    'transaksi' => $monthly->jumlah_transaksi,
                ];
            }
            
            // Closing balance tahun ini
            $closingBalance = $openingBalance + $totalMutasiNetto;
            $closingDebet = $closingBalance >= 0 ? abs($closingBalance) : 0;
            $closingKredit = $closingBalance < 0 ? abs($closingBalance) : 0;
            
            $data = [
                'version_number' => 1,
                'version_status' => 'ACTIVE', // Langsung ACTIVE karena auto-generated
                'closing_year' => $year,
                'coa_code' => $coa->coa_code,
                'coa_desc' => $coa->coa_desc,
                // Level 1: Main (through coaSub2 → coaSub1 → coaMain)
                'coa_main_code' => $coa->coaSub2?->coaSub1?->coa_main_code ?? null,
                'coa_main_desc' => $coa->coaSub2?->coaSub1?->coaMain?->coa_main_desc ?? null,
                // Level 2: Sub1 (through coaSub2 → coaSub1)
                'coasub1_code' => $coa->coaSub2?->coa_main1_code ?? null,
                'coasub1_desc' => $coa->coaSub2?->coaSub1?->coasub1_desc ?? null,
                // Level 3: Sub2 (direct relationship)
                'coasub2_code' => $coa->coa_coasub2code ?? null,
                'coasub2_desc' => $coa->coaSub2?->coasub2_desc ?? null,
                'opening_debet' => $openingDebet,
                'opening_kredit' => $openingKredit,
                'opening_balance' => $openingBalance,
                'mutasi_debet' => $totalMutasiDebet,
                'mutasi_kredit' => $totalMutasiKredit,
                'mutasi_netto' => $totalMutasiNetto,
                'jumlah_transaksi' => $totalTransaksi,
                'closing_debet' => $closingDebet,
                'closing_kredit' => $closingKredit,
                'closing_balance' => $closingBalance,
                'monthly_summary' => $monthlySummary,
                'is_closed' => false,
                'created_by' => $userName,
            ];
            
            if ($saveToDB) {
                YearlyClosing::create($data);
            }
            
            $results[] = $data;
        }
        
        return $results;
    }

    /**
     * LAYER 3: Calculate Audit (dari transaksi pertama)
     * 
     * Hitung ulang dari awal untuk verifikasi
     * Digunakan untuk audit trail dan memastikan tidak ada discrepancy
     * 
     * @param string $coaCode COA Code (optional, kosong = semua COA)
     * @param int|null $upToYear Hitung sampai tahun berapa (optional)
     * @param int|null $upToMonth Hitung sampai bulan berapa (optional)
     * @return array
     */
    public function calculateAudit(?string $coaCode = null, ?int $upToYear = null, ?int $upToMonth = null): array
    {
        // Use transcoa_coa_code as the actual COA code
        $query = DB::table('tr_acc_transaksi_coa')
            ->select('transcoa_coa_code');
            // No status filter - include all transactions
        
        if ($coaCode) {
            $query->where('transcoa_coa_code', $coaCode);
        }
        
        if ($upToYear && $upToMonth) {
            $endDate = Carbon::create($upToYear, $upToMonth, 1)->endOfMonth();
            $query->where('transcoa_coa_date_ops', '<=', $endDate);
        }
        
        $coaCodes = $query->distinct()->pluck('transcoa_coa_code');
        
        $results = [];
        
        foreach ($coaCodes as $code) {
            $transaksi = DB::table('tr_acc_transaksi_coa')
                ->where('transcoa_coa_code', $code);
                // No status filter - include all transactions
            
            if ($upToYear && $upToMonth) {
                $endDate = Carbon::create($upToYear, $upToMonth, 1)->endOfMonth();
                $transaksi->where('transcoa_coa_date_ops', '<=', $endDate);
            }
            
            $summary = $transaksi->selectRaw('
                SUM(COALESCE(transcoa_debet_value_ops, 0)) as total_debet,
                SUM(COALESCE(transcoa_credit_value_ops, 0)) as total_kredit,
                COUNT(*) as jumlah_transaksi,
                MIN(transcoa_coa_date_ops) as tanggal_pertama,
                MAX(transcoa_coa_date_ops) as tanggal_terakhir
            ')->first();
            
            $totalDebet = $summary->total_debet ?? 0;
            $totalKredit = $summary->total_kredit ?? 0;
            $balance = $totalDebet - $totalKredit;
            
            $coa = Coa::where('coa_code', $code)->first();
            
            $results[] = [
                'coa_code' => $code,
                'coa_desc' => $coa ? $coa->coa_desc : 'Tidak Terdaftar',
                'total_debet' => $totalDebet,
                'total_kredit' => $totalKredit,
                'balance' => $balance,
                'balance_debet' => $balance >= 0 ? abs($balance) : 0,
                'balance_kredit' => $balance < 0 ? abs($balance) : 0,
                'jumlah_transaksi' => $summary->jumlah_transaksi ?? 0,
                'tanggal_pertama' => $summary->tanggal_pertama,
                'tanggal_terakhir' => $summary->tanggal_terakhir,
                'audit_date' => now(),
            ];
        }
        
        return $results;
    }

    /**
     * Get Opening Balance untuk bulan tertentu
     * 
     * @param string $coaCode
     * @param int $year
     * @param int $month
     * @return array
     */
    private function getOpeningBalance(string $coaCode, int $year, int $month): array
    {
        // Jika Januari, ambil dari yearly closing tahun sebelumnya
        if ($month == 1) {
            $previousYear = YearlyClosing::where('closing_year', $year - 1)
                ->where('coa_code', $coaCode)
                ->where('version_status', 'ACTIVE')
                ->first();
            
            if ($previousYear) {
                return [
                    'opening_debet' => $previousYear->closing_debet,
                    'opening_kredit' => $previousYear->closing_kredit,
                    'opening_balance' => $previousYear->closing_balance,
                ];
            }
        } else {
            // Ambil dari monthly closing bulan sebelumnya
            $previousMonth = MonthlyClosing::where('closing_year', $year)
                ->where('closing_month', $month - 1)
                ->where('coa_code', $coaCode)
                ->where('version_status', 'ACTIVE')
                ->first();
            
            if ($previousMonth) {
                return [
                    'opening_debet' => $previousMonth->closing_debet,
                    'opening_kredit' => $previousMonth->closing_kredit,
                    'opening_balance' => $previousMonth->closing_balance,
                ];
            }
        }
        
        // Default jika tidak ada data sebelumnya
        return [
            'opening_debet' => 0,
            'opening_kredit' => 0,
            'opening_balance' => 0,
        ];
    }

    /**
     * Compare closing dengan audit calculation
     * 
     * Untuk memastikan tidak ada discrepancy
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public function compareWithAudit(int $year, int $month): array
    {
        $monthlyClosing = MonthlyClosing::where('closing_year', $year)
            ->where('closing_month', $month)
            ->where('version_status', 'ACTIVE')
            ->get()
            ->keyBy('coa_code');
        
        $auditData = collect($this->calculateAudit(null, $year, $month))
            ->keyBy('coa_code');
        
        $discrepancies = [];
        
        foreach ($monthlyClosing as $coa => $closing) {
            $audit = $auditData->get($coa);
            
            if ($audit && abs($closing->closing_balance - $audit['balance']) > 0.01) {
                $discrepancies[] = [
                    'coa_code' => $coa,
                    'coa_desc' => $closing->coa_desc,
                    'closing_balance' => $closing->closing_balance,
                    'audit_balance' => $audit['balance'],
                    'difference' => $closing->closing_balance - $audit['balance'],
                ];
            }
        }
        
        return $discrepancies;
    }

    /**
     * Lock closing (set status = CLOSED)
     * 
     * @param int $year
     * @param int $month
     * @param string $type 'monthly' or 'yearly'
     * @return bool
     */
    public function lockClosing(int $year, int $month, string $type = 'monthly'): bool
    {
        $userName = auth()->check() ? auth()->user()->name : 'system';
        
        if ($type === 'monthly') {
            MonthlyClosing::where('closing_year', $year)
                ->where('closing_month', $month)
                ->where('version_status', 'ACTIVE')
                ->update([
                    'is_closed' => true,
                    'closed_at' => now(),
                    'closed_by' => $userName,
                ]);
        } else {
            YearlyClosing::where('closing_year', $year)
                ->where('version_status', 'ACTIVE')
                ->update([
                    'is_closed' => true,
                    'closed_at' => now(),
                    'closed_by' => $userName,
                ]);
        }
        
        return true;
    }
}
