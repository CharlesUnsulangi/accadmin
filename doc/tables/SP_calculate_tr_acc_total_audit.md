# Stored Procedure: SP_calculate_tr_acc_total_audit

## Deskripsi
SP ini digunakan untuk melakukan kalkulasi hasil audit total pada tabel `tr_acc_total_audit` berdasarkan yearly audit dan audit logic.

## Parameter
- @audit_period VARCHAR(10)
- @version_number INT

## Logika Dasar
1. Ambil saldo dari yearly audit.
2. Kalkulasi hasil audit (MATCH, MISMATCH, ADJUSTED).
3. Update audit_result pada tabel audit total.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_calculate_tr_acc_total_audit
    @audit_period VARCHAR(10),
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Kalkulasi hasil audit
    UPDATE ta
    SET ta.audit_result = CASE WHEN ta.saldo_akhir = ta.saldo_awal THEN 'MATCH' ELSE 'MISMATCH' END
    FROM tr_acc_total_audit ta
    WHERE ta.audit_period = @audit_period AND ta.version_number = @version_number;
END
```

## Catatan
- Kalkulasi hasil audit harus dilakukan sebelum status diubah ke ACTIVE.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
