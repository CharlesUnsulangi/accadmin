# Stored Procedure: SP_generate_tr_acc_total_audit

## Deskripsi
SP ini digunakan untuk proses audit total/final dan generate data ke tabel `tr_acc_total_audit` dengan versioning dan audit trail.

## Parameter
- @audit_period VARCHAR(10)
- @created_by VARCHAR(50)
- @version_number INT

## Logika Dasar
1. Validasi existing audit total dan versi.
2. Ambil saldo dari yearly audit.
3. Insert data ke `tr_acc_total_audit` dengan status DRAFT.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_generate_tr_acc_total_audit
    @audit_period VARCHAR(10),
    @created_by VARCHAR(50),
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    IF EXISTS (
        SELECT 1 FROM tr_acc_total_audit
        WHERE audit_period = @audit_period AND version_number = @version_number AND version_status = 'ACTIVE'
    )
    BEGIN
        RAISERROR('Audit total dan versi sudah ada.', 16, 1);
        RETURN;
    END
    INSERT INTO tr_acc_total_audit (
        audit_period, coa_main_id, coasub1_id, coasub2_id, coa_id,
        saldo_awal, saldo_akhir, audit_result, version_number, version_status,
        created_at, created_by
    )
    SELECT
        @audit_period, coa_main_id, coasub1_id, coasub2_id, coa_id,
        saldo_awal, saldo_akhir, 'MATCH', @version_number, 'DRAFT', GETDATE(), @created_by
    FROM tr_acc_yearly_audit
    WHERE audit_year = LEFT(@audit_period, 4) AND version_number = @version_number;
END
```

## Catatan
- Status dapat diubah ke ACTIVE melalui SP_update_tr_acc_total_audit.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
