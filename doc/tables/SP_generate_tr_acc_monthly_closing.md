# Stored Procedure: SP_generate_tr_acc_monthly_closing

## Deskripsi
SP ini digunakan untuk melakukan proses closing bulanan dan generate data ke tabel `tr_acc_monthly_closing` dengan versioning dan audit trail.

## Parameter
- @closing_month INT
- @created_by VARCHAR(50)
- @version_number INT

## Logika Dasar
1. Validasi apakah closing bulan dan versi sudah ada (prevent duplicate ACTIVE).
2. Ambil saldo awal dan saldo akhir dari transaksi bulanan.
3. Insert data ke `tr_acc_monthly_closing` dengan status DRAFT.
4. Audit: simpan created_by dan timestamp.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_generate_tr_acc_monthly_closing
    @closing_month INT,
    @created_by VARCHAR(50),
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Validasi existing closing
    IF EXISTS (
        SELECT 1 FROM tr_acc_monthly_closing
        WHERE closing_month = @closing_month AND version_number = @version_number AND version_status = 'ACTIVE'
    )
    BEGIN
        RAISERROR('Closing bulan dan versi sudah ada.', 16, 1);
        RETURN;
    END

    -- Generate data dari transaksi
    INSERT INTO tr_acc_monthly_closing (
        closing_month, coa_main_id, coasub1_id, coasub2_id, coa_id,
        saldo_awal, saldo_akhir, version_number, version_status,
        created_at, created_by
    )
    SELECT
        @closing_month, coa_main_id, coasub1_id, coasub2_id, coa_id,
        SUM(saldo_awal), SUM(saldo_akhir), @version_number, 'DRAFT', GETDATE(), @created_by
    FROM transaksi_bulanan
    GROUP BY coa_main_id, coasub1_id, coasub2_id, coa_id;
END
```

## Catatan
- Setelah generate, status dapat diubah ke ACTIVE melalui SP_update_tr_acc_monthly_closing.
- Untuk rollback, gunakan versioning dan status lifecycle.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
