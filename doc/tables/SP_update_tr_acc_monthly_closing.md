# Stored Procedure: SP_update_tr_acc_monthly_closing

## Deskripsi
SP ini digunakan untuk mengubah status atau memperbarui data pada tabel `tr_acc_monthly_closing`, termasuk perubahan status versioning (DRAFT, ACTIVE, SUPERSEDED, ARCHIVED).

## Parameter
- @closing_id BIGINT
- @version_status VARCHAR(20)
- @updated_by VARCHAR(50)

## Logika Dasar
1. Validasi apakah closing_id valid dan status perubahan sesuai lifecycle.
2. Update status dan audit field (updated_by, updated_at).
3. Jika status diubah ke ACTIVE, pastikan tidak ada versi ACTIVE lain untuk bulan yang sama.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_update_tr_acc_monthly_closing
    @closing_id BIGINT,
    @version_status VARCHAR(20),
    @updated_by VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    -- Validasi closing_id
    IF NOT EXISTS (SELECT 1 FROM tr_acc_monthly_closing WHERE closing_id = @closing_id)
    BEGIN
        RAISERROR('Closing ID tidak ditemukan.', 16, 1);
        RETURN;
    END

    -- Validasi status ACTIVE
    IF @version_status = 'ACTIVE'
    BEGIN
        DECLARE @closing_month INT, @version_number INT;
        SELECT @closing_month = closing_month, @version_number = version_number FROM tr_acc_monthly_closing WHERE closing_id = @closing_id;
        IF EXISTS (
            SELECT 1 FROM tr_acc_monthly_closing
            WHERE closing_month = @closing_month AND version_number = @version_number AND version_status = 'ACTIVE' AND closing_id <> @closing_id
        )
        BEGIN
            RAISERROR('Sudah ada versi ACTIVE untuk bulan ini.', 16, 1);
            RETURN;
        END
    END

    -- Update status dan audit
    UPDATE tr_acc_monthly_closing
    SET version_status = @version_status,
        updated_at = GETDATE(),
        updated_by = @updated_by
    WHERE closing_id = @closing_id;
END
```

## Catatan
- Perubahan status harus mengikuti lifecycle versioning.
- Audit field wajib diisi setiap update.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
