# Stored Procedure: SP_compare_tr_acc_yearly_audit

## Deskripsi
SP ini digunakan untuk membandingkan dua versi audit tahunan pada tabel `tr_acc_yearly_audit` untuk analisis perubahan saldo dan hasil audit.

## Parameter
- @audit_year INT
- @version_number_1 INT
- @version_number_2 INT

## Logika Dasar
1. Ambil data audit untuk dua versi yang dibandingkan.
2. Hitung selisih saldo dan hasil audit per COA.
3. Return hasil perbandingan untuk reporting/audit.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_compare_tr_acc_yearly_audit
    @audit_year INT,
    @version_number_1 INT,
    @version_number_2 INT
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        a.coa_main_id, a.coasub1_id, a.coasub2_id, a.coa_id,
        a.saldo_awal AS saldo_awal_v1,
        b.saldo_awal AS saldo_awal_v2,
        a.saldo_akhir AS saldo_akhir_v1,
        b.saldo_akhir AS saldo_akhir_v2,
        a.audit_result AS audit_result_v1,
        b.audit_result AS audit_result_v2,
        (b.saldo_akhir - a.saldo_akhir) AS selisih_saldo_akhir
    FROM tr_acc_yearly_audit a
    INNER JOIN tr_acc_yearly_audit b
        ON a.audit_year = b.audit_year
        AND a.coa_main_id = b.coa_main_id
        AND a.coasub1_id = b.coasub1_id
        AND a.coasub2_id = b.coasub2_id
        AND a.coa_id = b.coa_id
    WHERE a.audit_year = @audit_year
      AND a.version_number = @version_number_1
      AND b.version_number = @version_number_2;
END
```

## Catatan
- Hasil compare dapat digunakan untuk audit, rollback, dan reporting.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
