# Stored Procedure: SP_calculate_tr_acc_yearly_audit

## Deskripsi
SP ini digunakan untuk melakukan kalkulasi hasil audit tahunan pada tabel `tr_acc_yearly_audit` berdasarkan yearly closing dan audit logic.

## Parameter
- @audit_year INT
- @version_number INT

## Logika Dasar
1. Ambil saldo dari yearly closing.
2. Kalkulasi hasil audit (MATCH, MISMATCH, ADJUSTED).
3. Update audit_result pada tabel audit.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_calculate_tr_acc_yearly_audit
    @audit_year INT,
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Kalkulasi hasil audit
    UPDATE ya
    SET ya.audit_result = CASE WHEN ya.saldo_akhir = ya.saldo_awal THEN 'MATCH' ELSE 'MISMATCH' END
    FROM tr_acc_yearly_audit ya
    WHERE ya.audit_year = @audit_year AND ya.version_number = @version_number;
END
```

## Catatan
- Kalkulasi hasil audit harus dilakukan sebelum status diubah ke ACTIVE.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
