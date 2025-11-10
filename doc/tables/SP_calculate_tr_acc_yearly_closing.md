# Stored Procedure: SP_calculate_tr_acc_yearly_closing

## Deskripsi
SP ini digunakan untuk melakukan kalkulasi saldo akhir tahunan pada tabel `tr_acc_yearly_closing` berdasarkan monthly closing dan saldo awal.

## Parameter
- @closing_year INT
- @version_number INT

## Logika Dasar
1. Ambil saldo awal dan monthly closing.
2. Hitung saldo akhir per COA.
3. Update saldo_akhir pada tabel closing.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_calculate_tr_acc_yearly_closing
    @closing_year INT,
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Kalkulasi saldo akhir
    UPDATE yc
    SET yc.saldo_akhir = yc.saldo_awal + t.total_transaksi
    FROM tr_acc_yearly_closing yc
    INNER JOIN (
        SELECT coa_main_id, coasub1_id, coasub2_id, coa_id, SUM(saldo_akhir) AS total_transaksi
        FROM tr_acc_monthly_closing
        WHERE LEFT(closing_month, 4) = CAST(@closing_year AS VARCHAR)
        GROUP BY coa_main_id, coasub1_id, coasub2_id, coa_id
    ) t
    ON yc.coa_main_id = t.coa_main_id
    AND yc.coasub1_id = t.coasub1_id
    AND yc.coasub2_id = t.coasub2_id
    AND yc.coa_id = t.coa_id
    WHERE yc.closing_year = @closing_year AND yc.version_number = @version_number;
END
```

## Catatan
- Kalkulasi saldo akhir harus dilakukan sebelum status diubah ke ACTIVE.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
