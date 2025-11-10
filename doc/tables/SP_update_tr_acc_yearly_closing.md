# Stored Procedure: SP_update_tr_acc_yearly_closing

## Deskripsi
SP ini digunakan untuk mengubah status atau memperbarui data pada tabel `tr_acc_yearly_closing`, termasuk perubahan status versioning.

## Parameter
- @closing_id BIGINT
- @version_status VARCHAR(20)
- @updated_by VARCHAR(50)

## Logika Dasar
1. Validasi closing_id dan status perubahan.
2. Update status dan audit field.
3. Pastikan tidak ada versi ACTIVE lain untuk tahun yang sama.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_update_tr_acc_yearly_closing
    @closing_id BIGINT,
    @version_status VARCHAR(20),
    @updated_by VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    IF NOT EXISTS (SELECT 1 FROM tr_acc_yearly_closing WHERE closing_id = @closing_id)
    BEGIN
        RAISERROR('Closing ID tidak ditemukan.', 16, 1);
        RETURN;
    END
    IF @version_status = 'ACTIVE'
    BEGIN
        DECLARE @closing_year INT, @version_number INT;
        SELECT @closing_year = closing_year, @version_number = version_number FROM tr_acc_yearly_closing WHERE closing_id = @closing_id;
        IF EXISTS (
            SELECT 1 FROM tr_acc_yearly_closing
            WHERE closing_year = @closing_year AND version_number = @version_number AND version_status = 'ACTIVE' AND closing_id <> @closing_id
        )
        BEGIN
            RAISERROR('Sudah ada versi ACTIVE untuk tahun ini.', 16, 1);
            RETURN;
        END
    END
    UPDATE tr_acc_yearly_closing
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
