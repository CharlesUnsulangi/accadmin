# Stored Procedure: SP_calculate_tr_acc_monthly_closing

## Deskripsi
SP ini digunakan untuk melakukan kalkulasi saldo akhir bulanan pada tabel `tr_acc_monthly_closing` berdasarkan transaksi dan saldo awal.

## Parameter
- @closing_month INT
- @version_number INT

## Logika Dasar
1. Ambil saldo awal dan transaksi bulanan.
2. Hitung saldo akhir per COA.
3. Update saldo_akhir pada tabel closing.

## Contoh SQL Template
```sql
CREATE PROCEDURE SP_calculate_tr_acc_monthly_closing
    @closing_month INT,
    @version_number INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Kalkulasi saldo akhir
    UPDATE mc
    SET mc.saldo_akhir = mc.saldo_awal + t.total_transaksi
    FROM tr_acc_monthly_closing mc
    INNER JOIN (
        SELECT coa_main_id, coasub1_id, coasub2_id, coa_id, SUM(jumlah) AS total_transaksi
        FROM transaksi_bulanan
        WHERE closing_month = @closing_month
        GROUP BY coa_main_id, coasub1_id, coasub2_id, coa_id
    ) t
    ON mc.coa_main_id = t.coa_main_id
    AND mc.coasub1_id = t.coasub1_id
    AND mc.coasub2_id = t.coasub2_id
    AND mc.coa_id = t.coa_id
    WHERE mc.closing_month = @closing_month AND mc.version_number = @version_number;
END
```

## Catatan
- Kalkulasi saldo akhir harus dilakukan sebelum status diubah ke ACTIVE.
- Ikuti naming convention di STORED_PROCEDURE_NAMING_CONVENTION.md
