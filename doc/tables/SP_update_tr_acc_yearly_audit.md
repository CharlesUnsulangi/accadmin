# Stored Procedure: SP_update_tr_acc_yearly_audit

## Deskripsi
SP ini digunakan untuk mengubah status atau memperbarui data pada tabel `tr_acc_yearly_audit`, termasuk perubahan status versioning.

## Parameter
- @audit_id BIGINT
- @version_status VARCHAR(20)
- @updated_by VARCHAR(50)

## Logika Dasar
1. Validasi audit_id dan status perubahan.
2. Update status dan audit field.
3. Pastikan tidak ada versi ACTIVE lain untuk tahun yang sama.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_update_tr_acc_yearly_audit
    @audit_id BIGINT,
    @version_status VARCHAR(20),
    @updated_by VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    IF NOT EXISTS (SELECT 1 FROM tr_acc_yearly_audit WHERE audit_id = @audit_id)
    BEGIN
        RAISERROR('Audit ID tidak ditemukan.', 16, 1);
        RETURN;
    END
    IF @version_status = 'ACTIVE'
    BEGIN
        DECLARE @audit_year INT, @version_number INT;
        SELECT @audit_year = audit_year, @version_number = version_number FROM tr_acc_yearly_audit WHERE audit_id = @audit_id;
        IF EXISTS (
            SELECT 1 FROM tr_acc_yearly_audit
            WHERE audit_year = @audit_year AND version_number = @version_number AND version_status = 'ACTIVE' AND audit_id <> @audit_id
        )
        BEGIN
            RAISERROR('Sudah ada versi ACTIVE untuk tahun ini.', 16, 1);
            RETURN;
        END
    END
    UPDATE tr_acc_yearly_audit
    SET version_status = @version_status,
        updated_at = GETDATE(),
        updated_by = @updated_by
    WHERE audit_id = @audit_id;
END
```

## Catatan
- Perubahan status harus mengikuti lifecycle versioning.
- Audit field wajib diisi setiap update.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
