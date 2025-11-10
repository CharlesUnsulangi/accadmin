-- =====================================================
-- Alternative: Calculate Saldo Awal from Beginning of Year
-- =====================================================
-- Gunakan ini jika belum punya data rekap bulan sebelumnya
-- =====================================================

CREATE OR ALTER PROCEDURE SP_calculate_saldo_awal
    @year INT,
    @month INT,
    @coa_code VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @first_day_of_year DATE;
    DECLARE @last_day_prev_month DATE;
    DECLARE @saldo_awal DECIMAL(18,2);
    
    -- First day of year
    SET @first_day_of_year = DATEFROMPARTS(@year, 1, 1);
    
    -- Last day of previous month
    IF @month = 1
    BEGIN
        -- Januari: ambil dari tahun sebelumnya
        SET @last_day_prev_month = DATEFROMPARTS(@year - 1, 12, 31);
    END
    ELSE
    BEGIN
        -- Bulan lain: ambil dari bulan sebelumnya tahun ini
        SET @last_day_prev_month = EOMONTH(DATEFROMPARTS(@year, @month - 1, 1));
    END
    
    -- Hitung saldo dari awal tahun s/d bulan lalu
    SELECT @saldo_awal = ISNULL(SUM(transcoa_debet_value), 0) - ISNULL(SUM(transcoa_credit_value), 0)
    FROM tr_acc_transaksi_coa
    WHERE transcoa_coa_code = @coa_code
      AND transcoa_coa_date < DATEFROMPARTS(@year, @month, 1); -- Sebelum bulan ini
    
    -- Return saldo awal
    SELECT 
        @coa_code AS coa_code,
        @year AS year,
        @month AS month,
        @first_day_of_year AS first_day_of_year,
        @last_day_prev_month AS last_day_prev_month,
        ISNULL(@saldo_awal, 0) AS saldo_awal;
END
GO

-- Test
EXEC SP_calculate_saldo_awal @year = 2024, @month = 12, @coa_code = '1-1001';
