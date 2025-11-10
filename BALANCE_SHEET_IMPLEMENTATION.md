# Balance Sheet Implementation Guide

## 📋 Overview
Implementasi sistem Balance Sheet (Neraca) per bulan dari data transaksi COA dengan saldo running balance.

## 🎯 Objectives
1. Generate rekap saldo per COA per bulan
2. Klasifikasi akun berdasarkan kategori Balance Sheet (Asset, Liability, Equity)
3. Tampilan Balance Sheet format standar
4. Support filtering per bulan/tahun
5. Running balance dari periode sebelumnya

## 📊 Data Structure

### Source Tables
- `ms_acc_coa` - Master Chart of Accounts
- `ms_acc_coa_main` - Level 1 Categories (Asset, Liability, Equity, Revenue, Expense)
- `tr_acc_transaksi_coa` - Transaction details (Debit/Credit per COA)

### New Table: `tr_acc_rekap_balance_sheet_monthly`
Tabel rekap untuk menyimpan saldo per COA per bulan.

```sql
CREATE TABLE tr_acc_rekap_balance_sheet_monthly (
    id INT IDENTITY(1,1) PRIMARY KEY,
    periode_year INT NOT NULL,                      -- Tahun (2024, 2025)
    periode_month INT NOT NULL,                     -- Bulan (1-12)
    periode_id VARCHAR(6) NOT NULL,                 -- Format: YYYYMM (202401, 202412)
    coa_code VARCHAR(50) NOT NULL,                  -- FK ke ms_acc_coa
    coa_desc NVARCHAR(255),                         -- Deskripsi COA
    coa_main_code VARCHAR(10),                      -- FK ke ms_acc_coa_main (kategori utama)
    coa_main_desc NVARCHAR(100),                    -- Asset/Liability/Equity/Revenue/Expense
    
    -- Saldo awal bulan (dari bulan sebelumnya)
    saldo_awal_debet DECIMAL(18,2) DEFAULT 0,
    saldo_awal_kredit DECIMAL(18,2) DEFAULT 0,
    saldo_awal DECIMAL(18,2) DEFAULT 0,             -- Debet - Kredit
    
    -- Mutasi bulan berjalan
    tanggal_pertama DATE,                           -- Transaksi pertama bulan ini
    tanggal_terakhir DATE,                          -- Transaksi terakhir bulan ini
    jumlah_transaksi INT DEFAULT 0,                 -- Count transaksi
    total_debet DECIMAL(18,2) DEFAULT 0,            -- Total debet bulan ini
    total_kredit DECIMAL(18,2) DEFAULT 0,           -- Total kredit bulan ini
    mutasi_netto DECIMAL(18,2) DEFAULT 0,           -- Debet - Kredit
    
    -- Saldo akhir bulan
    saldo_akhir_debet DECIMAL(18,2) DEFAULT 0,
    saldo_akhir_kredit DECIMAL(18,2) DEFAULT 0,
    saldo_akhir DECIMAL(18,2) DEFAULT 0,            -- Saldo awal + Mutasi netto
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    usercreated VARCHAR(50),
    
    -- Indexes
    CONSTRAINT UQ_Rekap_BS_Monthly UNIQUE (periode_id, coa_code),
    INDEX IX_Periode (periode_year, periode_month),
    INDEX IX_COA (coa_code),
    INDEX IX_MainCategory (coa_main_code)
);
```

### Stored Procedure: `SP_generate_balance_sheet_monthly`

```sql
CREATE OR ALTER PROCEDURE SP_generate_balance_sheet_monthly
    @year INT,          -- Tahun (2024)
    @month INT          -- Bulan (1-12)
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @periode_id VARCHAR(6);
    DECLARE @first_day DATE;
    DECLARE @last_day DATE;
    DECLARE @prev_periode_id VARCHAR(6);
    DECLARE @rows INT;
    
    -- Validasi input
    IF @month < 1 OR @month > 12
    BEGIN
        SELECT 'Error: Month must be between 1 and 12' AS ErrorMessage;
        RETURN;
    END
    
    -- Hitung periode
    SET @periode_id = CAST(@year AS VARCHAR(4)) + RIGHT('0' + CAST(@month AS VARCHAR(2)), 2);
    SET @first_day = DATEFROMPARTS(@year, @month, 1);
    SET @last_day = EOMONTH(@first_day);
    
    -- Periode sebelumnya (untuk saldo awal)
    IF @month = 1
    BEGIN
        SET @prev_periode_id = CAST(@year - 1 AS VARCHAR(4)) + '12';
    END
    ELSE
    BEGIN
        SET @prev_periode_id = CAST(@year AS VARCHAR(4)) + RIGHT('0' + CAST(@month - 1 AS VARCHAR(2)), 2);
    END
    
    -- Hapus data existing untuk periode ini
    DELETE FROM tr_acc_rekap_balance_sheet_monthly
    WHERE periode_id = @periode_id;
    
    -- Generate rekap
    INSERT INTO tr_acc_rekap_balance_sheet_monthly
    (
        periode_year, periode_month, periode_id,
        coa_code, coa_desc, coa_main_code, coa_main_desc,
        saldo_awal_debet, saldo_awal_kredit, saldo_awal,
        tanggal_pertama, tanggal_terakhir, jumlah_transaksi,
        total_debet, total_kredit, mutasi_netto,
        saldo_akhir_debet, saldo_akhir_kredit, saldo_akhir,
        created_at, usercreated
    )
    SELECT
        @year AS periode_year,
        @month AS periode_month,
        @periode_id AS periode_id,
        
        -- COA Info
        coa.coa_code,
        coa.coa_desc,
        main.coa_main_code,
        main.coa_main_desc,
        
        -- Saldo Awal (dari periode sebelumnya)
        ISNULL(prev.saldo_akhir_debet, 0) AS saldo_awal_debet,
        ISNULL(prev.saldo_akhir_kredit, 0) AS saldo_awal_kredit,
        ISNULL(prev.saldo_akhir, 0) AS saldo_awal,
        
        -- Mutasi bulan ini
        MIN(trans.transcoa_coa_date) AS tanggal_pertama,
        MAX(trans.transcoa_coa_date) AS tanggal_terakhir,
        COUNT(*) AS jumlah_transaksi,
        SUM(ISNULL(trans.transcoa_debet_value, 0)) AS total_debet,
        SUM(ISNULL(trans.transcoa_credit_value, 0)) AS total_kredit,
        SUM(ISNULL(trans.transcoa_debet_value, 0)) - SUM(ISNULL(trans.transcoa_credit_value, 0)) AS mutasi_netto,
        
        -- Saldo Akhir
        ISNULL(prev.saldo_akhir_debet, 0) + SUM(ISNULL(trans.transcoa_debet_value, 0)) AS saldo_akhir_debet,
        ISNULL(prev.saldo_akhir_kredit, 0) + SUM(ISNULL(trans.transcoa_credit_value, 0)) AS saldo_akhir_kredit,
        ISNULL(prev.saldo_akhir, 0) + (SUM(ISNULL(trans.transcoa_debet_value, 0)) - SUM(ISNULL(trans.transcoa_credit_value, 0))) AS saldo_akhir,
        
        -- Audit
        GETDATE() AS created_at,
        SUSER_SNAME() AS usercreated
        
    FROM tr_acc_transaksi_coa trans
    INNER JOIN ms_acc_coa coa ON trans.transcoa_coa_code = coa.coa_code
    LEFT JOIN ms_acc_main_sub2 sub2 ON coa.coa_coasub2code = sub2.coa_main2_code
    LEFT JOIN ms_acc_main_sub1 sub1 ON sub2.coa_sub1_code = sub1.coa_sub1_code
    LEFT JOIN ms_acc_coa_main main ON sub1.coa_main_code = main.coa_main_code
    LEFT JOIN tr_acc_rekap_balance_sheet_monthly prev ON prev.periode_id = @prev_periode_id AND prev.coa_code = coa.coa_code
    
    WHERE trans.transcoa_coa_date >= @first_day
      AND trans.transcoa_coa_date <= @last_day
      
    GROUP BY
        coa.coa_code,
        coa.coa_desc,
        main.coa_main_code,
        main.coa_main_desc,
        prev.saldo_akhir_debet,
        prev.saldo_akhir_kredit,
        prev.saldo_akhir
        
    ORDER BY coa.coa_code;
    
    SET @rows = @@ROWCOUNT;
    
    -- Return hasil
    SELECT 
        @rows AS RowsGenerated,
        @periode_id AS PeriodeID,
        @first_day AS FirstDay,
        @last_day AS LastDay,
        'Success' AS Status,
        'Generated ' + CAST(@rows AS VARCHAR(10)) + ' COA records for period ' + @periode_id AS Message;
END
GO
```

## 🔧 Implementation Steps

### Step 1: Create Database Objects

```sql
-- 1. Create table
-- (Copy SQL from above)

-- 2. Create stored procedure
-- (Copy SQL from above)

-- 3. Test SP
EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = 12;

-- 4. Verify data
SELECT TOP 10 * FROM tr_acc_rekap_balance_sheet_monthly
ORDER BY periode_id DESC, coa_code;
```

### Step 2: Create Laravel Model

File: `app/Models/RekapBalanceSheetMonthly.php`

```php
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
}
```

### Step 3: Create Livewire Component

```bash
php artisan make:livewire BalanceSheetReport
```

### Step 4: Add to AdminSp Table

Tambahkan SP ke tabel `ms_admin_sp` agar bisa dijalankan dari UI:

```sql
INSERT INTO ms_admin_sp (
    ms_admin_sp_id,
    sp_name,
    sp_desc,
    sp_category,
    input_date_start,
    input_date_end,
    created_date,
    usercreated
) VALUES (
    'SP_generate_balance_sheet_monthly',
    'SP_generate_balance_sheet_monthly',
    'Generate Balance Sheet rekap per bulan dengan saldo running',
    'Finance Report',
    1, -- Perlu input year via date_start
    0, -- Tidak perlu date_end
    GETDATE(),
    SUSER_SNAME()
);
```

## 📱 UI Features

### Balance Sheet Report Page Features:
1. **Filter Controls**
   - Year selector (dropdown 2020-2030)
   - Month selector (dropdown Jan-Dec)
   - Generate button (run SP)
   - Export to Excel button

2. **Display Sections**
   - **ASSET** (Aktiva)
     - Current Asset (Aktiva Lancar)
     - Fixed Asset (Aktiva Tetap)
     - Subtotal Asset
   
   - **LIABILITY** (Kewajiban)
     - Current Liability (Kewajiban Lancar)
     - Long-term Liability (Kewajiban Jangka Panjang)
     - Subtotal Liability
   
   - **EQUITY** (Modal/Ekuitas)
     - Capital (Modal)
     - Retained Earnings (Laba Ditahan)
     - Current Profit/Loss (Laba/Rugi Berjalan)
     - Subtotal Equity
   
   - **BALANCE CHECK**
     - Total Asset
     - Total Liability + Equity
     - Difference (should be 0)

3. **Column Display**
   - COA Code
   - COA Description
   - Saldo Awal
   - Mutasi Debet
   - Mutasi Kredit
   - Saldo Akhir

## � Cara Menghitung Saldo Awal

### Konsep Dasar
**Saldo Awal Bulan Ini = Saldo Akhir Bulan Lalu**

### Tiga Skenario:

#### 1. **Periode Pertama Kali (Initial Setup)** 🎯

Gunakan SP `SP_setup_initial_balance` untuk setup opening balance:

```sql
-- Setup saldo awal Januari 2024 dari semua transaksi s/d 31 Des 2023
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;

-- Hasil: Saldo Akhir 202401 = Opening Balance untuk Februari 2024
```

**Alternatif: Import Manual**
```sql
-- Import dari Excel/sistem lama
-- File: database_setup_initial_balance.sql
-- Gunakan tabel temporary untuk bulk insert
```

#### 2. **Bulan Berikutnya (Running Balance)** �🔄

```sql
-- Februari 2024: Ambil saldo akhir Januari sebagai saldo awal
Saldo Awal Feb = Saldo Akhir Jan (dari tabel rekap)
Mutasi Feb = Transaksi Februari (dari tr_acc_transaksi_coa)
Saldo Akhir Feb = Saldo Awal Feb + Mutasi Feb

-- Implemented di SP_generate_balance_sheet_monthly:
LEFT JOIN tr_acc_rekap_balance_sheet_monthly prev 
    ON prev.periode_id = @prev_periode_id 
    AND prev.coa_code = coa.coa_code
```

#### 3. **Recalculate dari Awal** ♻️

Jika data rekap rusak atau perlu rebuild:

```sql
-- Recalculate all periods dari awal
-- Step 1: Setup initial (Jan 2024)
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;

-- Step 2: Generate bulan-bulan berikutnya
DECLARE @m INT = 2;
WHILE @m <= 12
BEGIN
    EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = @m;
    SET @m = @m + 1;
END
```

### Rumus Perhitungan:

```
SALDO AWAL BULAN INI:
= Saldo Akhir Bulan Lalu

MUTASI BULAN INI:
= SUM(Debet bulan ini) - SUM(Kredit bulan ini)

SALDO AKHIR BULAN INI:
= Saldo Awal + Mutasi
= Saldo Awal + (Debet - Kredit)

UNTUK PERIODE PERTAMA:
Saldo Awal = 0 (atau dari sistem lama)
Saldo Akhir = SUM(Debet s/d periode ini) - SUM(Kredit s/d periode ini)
```

### File SQL Helper:

1. **database_saldo_awal_calculation.sql**
   - Helper function untuk hitung saldo awal per COA
   - Useful untuk debugging

2. **database_setup_initial_balance.sql**
   - SP untuk setup opening balance periode pertama
   - Template untuk import manual
   - Test queries

## 🔄 Monthly Process Flow

### Flow Normal (Setelah Initial Setup):

```
1. End of Month
   ↓
2. Run SP_generate_balance_sheet_monthly
   - Read: Saldo Akhir bulan lalu (prev.saldo_akhir)
   - Process: Hitung mutasi bulan ini
   - Write: Save ke tr_acc_rekap_balance_sheet_monthly
   ↓
3. Data tersimpan dengan struktur:
   - Saldo Awal (from prev month)
   - Mutasi (current month)
   - Saldo Akhir (awal + mutasi)
   ↓
4. View Balance Sheet Report
   ↓
5. Export to Excel (optional)
```

### Flow First Time Setup:

```
1. Determine Cutoff Date (misal: 31 Des 2023)
   ↓
2. Option A: Calculate from All Transactions
   EXEC SP_setup_initial_balance @target_year=2024, @target_month=1
   ↓
3. Option B: Import from Previous System
   Use #temp_saldo_awal table
   ↓
4. Verify Opening Balance
   Check total Asset = Liability + Equity
   ↓
5. Generate Next Months
   Loop: EXEC SP_generate_balance_sheet_monthly
   ↓
6. Done!
```

## 🎨 Balance Sheet Format Example

```
PT. XYZ COMPANY
BALANCE SHEET
Per 31 Desember 2024

ASSET
  Current Asset
    1-1001  Cash                     100,000,000
    1-1002  Bank BCA                 500,000,000
    1-1101  Account Receivable       200,000,000
                                     -----------
  Subtotal Current Asset             800,000,000
  
  Fixed Asset
    1-2001  Building               1,000,000,000
    1-2002  Equipment                300,000,000
                                     -----------
  Subtotal Fixed Asset             1,300,000,000
                                     -----------
TOTAL ASSET                        2,100,000,000

LIABILITY
  Current Liability
    2-1001  Account Payable          150,000,000
    2-1002  Tax Payable               50,000,000
                                     -----------
  Subtotal Current Liability         200,000,000
                                     -----------
TOTAL LIABILITY                      200,000,000

EQUITY
  3-1001  Capital                  1,500,000,000
  3-2001  Retained Earnings          300,000,000
  3-3001  Current Year Profit        100,000,000
                                     -----------
TOTAL EQUITY                       1,900,000,000
                                     -----------
TOTAL LIABILITY + EQUITY           2,100,000,000

Balance Check: OK (Difference = 0)
```

## 🚀 Next Steps

1. ✅ Create table `tr_acc_rekap_balance_sheet_monthly`
2. ✅ Create SP `SP_generate_balance_sheet_monthly`
3. ⏳ Create Model `RekapBalanceSheetMonthly`
4. ⏳ Create Livewire Component `BalanceSheetReport`
5. ⏳ Create Blade View
6. ⏳ Add Route
7. ⏳ Add Menu Item
8. ⏳ Test with actual data

## 📝 Notes

- **Running Balance**: Setiap bulan mengambil saldo akhir dari bulan sebelumnya sebagai saldo awal
- **First Period**: Jika belum ada data bulan sebelumnya, saldo awal = 0
- **Performance**: Index pada periode_id dan coa_code untuk query cepat
- **Accuracy**: Balance Sheet harus balance (Asset = Liability + Equity)
- **Audit Trail**: Semua generate di-track dengan created_at dan usercreated
