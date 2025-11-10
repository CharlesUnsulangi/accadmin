-- =====================================================
-- Setup Initial Balance (Saldo Awal Periode Pertama)
-- =====================================================
-- Gunakan ini untuk setup saldo awal periode pertama
-- Misalnya: Saldo per 31 Desember 2023 sebagai opening balance 2024
-- =====================================================

-- Option 1: Hitung dari SEMUA transaksi sejak awal waktu
-- Cocok jika data transaksi lengkap dari tahun-tahun sebelumnya

CREATE OR ALTER PROCEDURE SP_setup_initial_balance
    @target_year INT,           -- Tahun target (misal 2024)
    @target_month INT = 1       -- Bulan target (default Januari)
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @periode_id VARCHAR(6);
    DECLARE @cutoff_date DATE;
    DECLARE @rows INT;
    
    -- Periode target
    SET @periode_id = CAST(@target_year AS VARCHAR(4)) + RIGHT('0' + CAST(@target_month AS VARCHAR(2)), 2);
    
    -- Cutoff date (akhir bulan sebelum periode target)
    IF @target_month = 1
    BEGIN
        SET @cutoff_date = DATEFROMPARTS(@target_year - 1, 12, 31);
    END
    ELSE
    BEGIN
        SET @cutoff_date = EOMONTH(DATEFROMPARTS(@target_year, @target_month - 1, 1));
    END
    
    -- Hapus data existing untuk periode ini (jika ada)
    DELETE FROM tr_acc_rekap_balance_sheet_monthly
    WHERE periode_id = @periode_id;
    
    -- Insert saldo awal dari semua transaksi s/d cutoff date
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
        @target_year AS periode_year,
        @target_month AS periode_month,
        @periode_id AS periode_id,
        
        -- COA Info
        coa.coa_code,
        coa.coa_desc,
        ISNULL(main.coa_main_code, '') AS coa_main_code,
        ISNULL(main.coa_main_desc, 'Uncategorized') AS coa_main_desc,
        
        -- Saldo Awal = 0 (ini periode pertama)
        0 AS saldo_awal_debet,
        0 AS saldo_awal_kredit,
        0 AS saldo_awal,
        
        -- Mutasi s/d cutoff date (ini jadi saldo awal periode berikutnya)
        MIN(trans.transcoa_coa_date) AS tanggal_pertama,
        MAX(trans.transcoa_coa_date) AS tanggal_terakhir,
        COUNT(*) AS jumlah_transaksi,
        SUM(ISNULL(trans.transcoa_debet_value, 0)) AS total_debet,
        SUM(ISNULL(trans.transcoa_credit_value, 0)) AS total_kredit,
        SUM(ISNULL(trans.transcoa_debet_value, 0)) - SUM(ISNULL(trans.transcoa_credit_value, 0)) AS mutasi_netto,
        
        -- Saldo Akhir = Saldo Awal (0) + Mutasi
        SUM(ISNULL(trans.transcoa_debet_value, 0)) AS saldo_akhir_debet,
        SUM(ISNULL(trans.transcoa_credit_value, 0)) AS saldo_akhir_kredit,
        SUM(ISNULL(trans.transcoa_debet_value, 0)) - SUM(ISNULL(trans.transcoa_credit_value, 0)) AS saldo_akhir,
        
        -- Audit
        GETDATE() AS created_at,
        SUSER_SNAME() AS usercreated
        
    FROM tr_acc_transaksi_coa trans
    INNER JOIN ms_acc_coa coa ON trans.transcoa_coa_code = coa.coa_code
    LEFT JOIN ms_acc_main_sub2 sub2 ON coa.coa_coasub2code = sub2.coa_main2_code
    LEFT JOIN ms_acc_main_sub1 sub1 ON sub2.coa_sub1_code = sub1.coa_sub1_code
    LEFT JOIN ms_acc_coa_main main ON sub1.coa_main_code = main.coa_main_code
    
    WHERE trans.transcoa_coa_date <= @cutoff_date  -- Semua transaksi s/d cutoff
    
    GROUP BY
        coa.coa_code,
        coa.coa_desc,
        main.coa_main_code,
        main.coa_main_desc
        
    HAVING COUNT(*) > 0  -- Hanya COA yang punya transaksi
        
    ORDER BY coa.coa_code;
    
    SET @rows = @@ROWCOUNT;
    
    -- Return hasil
    SELECT 
        @rows AS RowsGenerated,
        @periode_id AS PeriodeID,
        @cutoff_date AS CutoffDate,
        'Success' AS Status,
        'Setup initial balance for ' + CAST(@rows AS VARCHAR(10)) + ' COA accounts as of ' + CONVERT(VARCHAR, @cutoff_date, 120) AS Message;
END
GO

-- =====================================================
-- Option 2: Import Saldo Awal dari Excel/Sistem Lama
-- =====================================================

-- Buat tabel temporary untuk import
CREATE TABLE #temp_saldo_awal (
    coa_code VARCHAR(50),
    saldo_awal DECIMAL(18,2)
);

-- Import data (via SSMS Import Wizard atau BULK INSERT)
-- Atau insert manual:
INSERT INTO #temp_saldo_awal (coa_code, saldo_awal) VALUES
('1-1001', 100000000.00),
('1-1002', 500000000.00),
('1-1101', 200000000.00);
-- ... dst

-- Kemudian insert ke tabel rekap
INSERT INTO tr_acc_rekap_balance_sheet_monthly
(
    periode_year, periode_month, periode_id,
    coa_code, coa_desc, coa_main_code, coa_main_desc,
    saldo_awal, saldo_akhir,  -- Saldo awal = saldo akhir (belum ada mutasi)
    created_at, usercreated
)
SELECT
    2024 AS periode_year,
    0 AS periode_month,  -- 0 = Opening Balance
    '202400' AS periode_id,  -- Special ID untuk opening balance
    
    coa.coa_code,
    coa.coa_desc,
    ISNULL(main.coa_main_code, '') AS coa_main_code,
    ISNULL(main.coa_main_desc, 'Uncategorized') AS coa_main_desc,
    
    temp.saldo_awal,
    temp.saldo_awal,  -- Saldo akhir = saldo awal (opening)
    
    GETDATE(),
    SUSER_SNAME()
    
FROM #temp_saldo_awal temp
INNER JOIN ms_acc_coa coa ON temp.coa_code = coa.coa_code
LEFT JOIN ms_acc_main_sub2 sub2 ON coa.coa_coasub2code = sub2.coa_main2_code
LEFT JOIN ms_acc_main_sub1 sub1 ON sub2.coa_sub1_code = sub1.coa_sub1_code
LEFT JOIN ms_acc_coa_main main ON sub1.coa_main_code = main.coa_main_code;

DROP TABLE #temp_saldo_awal;

-- =====================================================
-- TESTING
-- =====================================================

-- Test 1: Setup initial balance untuk Januari 2024
-- (Hitung dari semua transaksi s/d 31 Des 2023)
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;

-- Test 2: Verify hasil
SELECT 
    coa_code,
    coa_desc,
    coa_main_desc,
    saldo_akhir,
    jumlah_transaksi,
    tanggal_pertama,
    tanggal_terakhir
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202401'
ORDER BY coa_code;

-- Test 3: Summary per kategori
SELECT 
    coa_main_code,
    coa_main_desc,
    COUNT(*) AS jumlah_akun,
    SUM(saldo_akhir) AS total_saldo
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202401'
GROUP BY coa_main_code, coa_main_desc
ORDER BY coa_main_code;
