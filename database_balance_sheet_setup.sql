-- =====================================================
-- Balance Sheet Monthly Rekap - Database Objects
-- =====================================================
-- Created: 2025-11-09
-- Purpose: Generate monthly balance sheet from transaction data
-- =====================================================

USE [RCM_DEV_HGS_SB]
GO

-- =====================================================
-- 1. CREATE TABLE: tr_acc_rekap_balance_sheet_monthly
-- =====================================================

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'tr_acc_rekap_balance_sheet_monthly')
BEGIN
    CREATE TABLE tr_acc_rekap_balance_sheet_monthly (
        id INT IDENTITY(1,1) PRIMARY KEY,
        
        -- Periode Information
        periode_year INT NOT NULL,                      -- Tahun (2024, 2025)
        periode_month INT NOT NULL,                     -- Bulan (1-12)
        periode_id VARCHAR(6) NOT NULL,                 -- Format: YYYYMM (202401, 202412)
        
        -- COA Information
        coa_code VARCHAR(50) NOT NULL,                  -- FK ke ms_acc_coa
        coa_desc NVARCHAR(255),                         -- Deskripsi COA
        coa_main_code VARCHAR(10),                      -- FK ke ms_acc_coa_main (kategori utama)
        coa_main_desc NVARCHAR(100),                    -- Asset/Liability/Equity/Revenue/Expense
        
        -- Saldo Awal Bulan (dari bulan sebelumnya)
        saldo_awal_debet DECIMAL(18,2) DEFAULT 0,
        saldo_awal_kredit DECIMAL(18,2) DEFAULT 0,
        saldo_awal DECIMAL(18,2) DEFAULT 0,             -- Debet - Kredit
        
        -- Mutasi Bulan Berjalan
        tanggal_pertama DATE,                           -- Transaksi pertama bulan ini
        tanggal_terakhir DATE,                          -- Transaksi terakhir bulan ini
        jumlah_transaksi INT DEFAULT 0,                 -- Count transaksi
        total_debet DECIMAL(18,2) DEFAULT 0,            -- Total debet bulan ini
        total_kredit DECIMAL(18,2) DEFAULT 0,           -- Total kredit bulan ini
        mutasi_netto DECIMAL(18,2) DEFAULT 0,           -- Debet - Kredit
        
        -- Saldo Akhir Bulan
        saldo_akhir_debet DECIMAL(18,2) DEFAULT 0,
        saldo_akhir_kredit DECIMAL(18,2) DEFAULT 0,
        saldo_akhir DECIMAL(18,2) DEFAULT 0,            -- Saldo awal + Mutasi netto
        
        -- Audit Trail
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME,
        usercreated VARCHAR(50),
        
        -- Constraints dan Indexes
        CONSTRAINT UQ_Rekap_BS_Monthly UNIQUE (periode_id, coa_code)
    );
    
    -- Create Indexes for Performance
    CREATE INDEX IX_Rekap_BS_Periode ON tr_acc_rekap_balance_sheet_monthly(periode_year, periode_month);
    CREATE INDEX IX_Rekap_BS_COA ON tr_acc_rekap_balance_sheet_monthly(coa_code);
    CREATE INDEX IX_Rekap_BS_MainCategory ON tr_acc_rekap_balance_sheet_monthly(coa_main_code);
    CREATE INDEX IX_Rekap_BS_PeriodeID ON tr_acc_rekap_balance_sheet_monthly(periode_id);
    
    PRINT 'Table tr_acc_rekap_balance_sheet_monthly created successfully!';
END
ELSE
BEGIN
    PRINT 'Table tr_acc_rekap_balance_sheet_monthly already exists.';
END
GO

-- =====================================================
-- 2. CREATE STORED PROCEDURE: SP_generate_balance_sheet_monthly
-- =====================================================

IF EXISTS (SELECT * FROM sys.objects WHERE type = 'P' AND name = 'SP_generate_balance_sheet_monthly')
    DROP PROCEDURE SP_generate_balance_sheet_monthly;
GO

CREATE PROCEDURE SP_generate_balance_sheet_monthly
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
    DECLARE @prev_year INT;
    DECLARE @prev_month INT;
    
    BEGIN TRY
        -- Validasi input
        IF @month < 1 OR @month > 12
        BEGIN
            SELECT 
                0 AS RowsGenerated,
                '' AS PeriodeID,
                NULL AS FirstDay,
                NULL AS LastDay,
                'Error' AS Status,
                'Month must be between 1 and 12' AS Message;
            RETURN;
        END
        
        -- Hitung periode
        SET @periode_id = CAST(@year AS VARCHAR(4)) + RIGHT('0' + CAST(@month AS VARCHAR(2)), 2);
        SET @first_day = DATEFROMPARTS(@year, @month, 1);
        SET @last_day = EOMONTH(@first_day);
        
        -- Hitung periode sebelumnya (untuk saldo awal)
        IF @month = 1
        BEGIN
            SET @prev_year = @year - 1;
            SET @prev_month = 12;
        END
        ELSE
        BEGIN
            SET @prev_year = @year;
            SET @prev_month = @month - 1;
        END
        SET @prev_periode_id = CAST(@prev_year AS VARCHAR(4)) + RIGHT('0' + CAST(@prev_month AS VARCHAR(2)), 2);
        
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
            ISNULL(main.coa_main_code, '') AS coa_main_code,
            ISNULL(main.coa_main_desc, 'Uncategorized') AS coa_main_desc,
            
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
        LEFT JOIN tr_acc_rekap_balance_sheet_monthly prev 
            ON prev.periode_id = @prev_periode_id 
            AND prev.coa_code = coa.coa_code
        
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
            
    END TRY
    BEGIN CATCH
        -- Return error info
        SELECT
            0 AS RowsGenerated,
            @periode_id AS PeriodeID,
            @first_day AS FirstDay,
            @last_day AS LastDay,
            'Error' AS Status,
            ERROR_MESSAGE() AS Message,
            ERROR_NUMBER() AS ErrorNumber,
            ERROR_LINE() AS ErrorLine;
    END CATCH
END
GO

PRINT 'Stored Procedure SP_generate_balance_sheet_monthly created successfully!';
GO

-- =====================================================
-- 3. TEST STORED PROCEDURE
-- =====================================================

-- Test untuk Desember 2024
PRINT '=================================================';
PRINT 'Testing SP_generate_balance_sheet_monthly';
PRINT '=================================================';

EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = 12;

-- Verify hasil
SELECT TOP 10 
    periode_id,
    coa_code,
    coa_desc,
    coa_main_desc,
    saldo_awal,
    total_debet,
    total_kredit,
    saldo_akhir
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202412'
ORDER BY coa_code;

-- Summary per kategori
SELECT 
    coa_main_code,
    coa_main_desc,
    COUNT(*) AS jumlah_akun,
    SUM(saldo_akhir) AS total_saldo_akhir
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202412'
GROUP BY coa_main_code, coa_main_desc
ORDER BY coa_main_code;

PRINT 'Test completed!';
GO
