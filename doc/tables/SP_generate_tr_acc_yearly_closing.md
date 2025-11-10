# Stored Procedure: SP_generate_tr_acc_yearly_closing

## Deskripsi
SP ini digunakan untuk proses closing tahunan dan generate data ke tabel `tr_acc_yearly_closing` dengan versioning dan audit trail.

## Parameter
- @closing_year INT
- @created_by VARCHAR(50)
- @version_number INT

## Logika Dasar
1. Validasi existing closing tahunan dan versi.
2. Ambil saldo awal dan saldo akhir dari monthly closing.
3. Insert data ke `tr_acc_yearly_closing` dengan status DRAFT.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_generate_tr_acc_yearly_closing
    @closing_year INT,
    @created_by VARCHAR(50),
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Validasi existing closing
    IF EXISTS (
        SELECT 1 FROM tr_acc_yearly_closing
        WHERE closing_year = @closing_year AND version_number = @version_number AND version_status = 'ACTIVE'
    )
    BEGIN
        RAISERROR('Closing tahun dan versi sudah ada.', 16, 1);
        RETURN;
    END

    -- Generate data dari monthly closing
    INSERT INTO tr_acc_yearly_closing (
        closing_year, coa_main_id, coasub1_id, coasub2_id, coa_id,
        saldo_awal, saldo_akhir, version_number, version_status,
        created_at, created_by
    )
    SELECT
        @closing_year, coa_main_id, coasub1_id, coasub2_id, coa_id,
        SUM(saldo_awal), SUM(saldo_akhir), @version_number, 'DRAFT', GETDATE(), @created_by
    FROM tr_acc_monthly_closing
    WHERE LEFT(closing_month, 4) = CAST(@closing_year AS VARCHAR)
    GROUP BY coa_main_id, coasub1_id, coasub2_id, coa_id;
END
```

## Catatan
- Status dapat diubah ke ACTIVE melalui SP_update_tr_acc_yearly_closing.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
