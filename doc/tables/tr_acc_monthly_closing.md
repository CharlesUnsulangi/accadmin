# Table: tr_acc_monthly_closing

## Deskripsi
Tabel ini menyimpan hasil closing bulanan untuk setiap akun COA, mendukung versioning dan audit trail. Struktur COA hierarchy disertakan untuk kemudahan reporting dan integrasi multi-layer closing.

## Struktur Kolom
- closing_id (PK, bigint, identity)
- closing_month (int) -- format YYYYMM
- coa_main_id (bigint, FK ke ms_acc_coa_main)
- coasub1_id (bigint, FK ke ms_acc_coasub1)
- coasub2_id (bigint, FK ke ms_acc_coasub2)
- coa_id (bigint, FK ke ms_acc_coa)
- saldo_awal (decimal(18,2))
- saldo_akhir (decimal(18,2))
- version_number (int)
- version_status (varchar(20)) -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
- created_at (datetime)
- created_by (varchar(50))
- updated_at (datetime)
- updated_by (varchar(50))

## Primary Key
- closing_id

## Foreign Key
- coa_main_id → ms_acc_coa_main
- coasub1_id → ms_acc_coasub1
- coasub2_id → ms_acc_coasub2
- coa_id → ms_acc_coa

## Extended Properties
- versioning: mendukung multi versi closing bulanan
- audit: created/updated by, timestamp
- hierarchy: seluruh level COA untuk reporting

## Contoh SQL Script
```sql
CREATE TABLE tr_acc_monthly_closing (
    closing_id      BIGINT IDENTITY(1,1) PRIMARY KEY,
    closing_month   INT NOT NULL,
    coa_main_id     BIGINT NOT NULL,
    coasub1_id      BIGINT NOT NULL,
    coasub2_id      BIGINT NOT NULL,
    coa_id          BIGINT NOT NULL,
    saldo_awal      DECIMAL(18,2) NOT NULL,
    saldo_akhir     DECIMAL(18,2) NOT NULL,
    version_number  INT NOT NULL,
    version_status  VARCHAR(20) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT GETDATE(),
    created_by      VARCHAR(50) NOT NULL,
    updated_at      DATETIME NULL,
    updated_by      VARCHAR(50) NULL,
    CONSTRAINT FK_monthly_coa_main FOREIGN KEY (coa_main_id) REFERENCES ms_acc_coa_main(coa_main_id),
    CONSTRAINT FK_monthly_coasub1 FOREIGN KEY (coasub1_id) REFERENCES ms_acc_coasub1(coasub1_id),
    CONSTRAINT FK_monthly_coasub2 FOREIGN KEY (coasub2_id) REFERENCES ms_acc_coasub2(coasub2_id),
    CONSTRAINT FK_monthly_coa FOREIGN KEY (coa_id) REFERENCES ms_acc_coa(coa_id)
);
```

## Sample Data
| closing_month | coa_main_id | coasub1_id | coasub2_id | coa_id | saldo_awal | saldo_akhir | version_number | version_status |
|--------------|------------|------------|------------|--------|------------|-------------|---------------|---------------|
| 202501       | 1          | 10         | 100        | 1000   | 5000000.00 | 5200000.00  | 1             | ACTIVE        |

## Relasi
- Satu closing bulanan per COA per versi
- Dapat di-compare, di-rollback, dan di-update sesuai versioning

## Catatan
- Semua proses closing bulanan harus menggunakan SP dengan naming convention terbaru (lihat STORED_PROCEDURE_NAMING_CONVENTION.md)
