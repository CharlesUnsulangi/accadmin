# Table: tr_acc_total_audit

## Deskripsi
Tabel ini menyimpan hasil audit total (final) untuk setiap akun COA, mendukung versioning dan audit trail. Struktur COA hierarchy disertakan untuk kemudahan reporting dan integrasi multi-layer closing.

## Struktur Kolom
- total_audit_id (PK, bigint, identity)
- audit_period (varchar(10)) -- format: YYYY, YYYYMM, atau custom
- coa_main_id (bigint, FK ke ms_acc_coa_main)
- coasub1_id (bigint, FK ke ms_acc_coasub1)
- coasub2_id (bigint, FK ke ms_acc_coasub2)
- coa_id (bigint, FK ke ms_acc_coa)
- saldo_awal (decimal(18,2))
- saldo_akhir (decimal(18,2))
- audit_result (varchar(100)) -- hasil audit, misal: MATCH, MISMATCH, ADJUSTED
- version_number (int)
- version_status (varchar(20)) -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
- created_at (datetime)
- created_by (varchar(50))
- updated_at (datetime)
- updated_by (varchar(50))

## Primary Key
- total_audit_id

## Foreign Key
- coa_main_id → ms_acc_coa_main
- coasub1_id → ms_acc_coasub1
- coasub2_id → ms_acc_coasub2
- coa_id → ms_acc_coa

## Extended Properties
- versioning: mendukung multi versi audit total
- audit: created/updated by, timestamp
- hierarchy: seluruh level COA untuk reporting

## Contoh SQL Script
```sql
CREATE TABLE tr_acc_total_audit (
    total_audit_id  BIGINT IDENTITY(1,1) PRIMARY KEY,
    audit_period    VARCHAR(10) NOT NULL,
    coa_main_id     BIGINT NOT NULL,
    coasub1_id      BIGINT NOT NULL,
    coasub2_id      BIGINT NOT NULL,
    coa_id          BIGINT NOT NULL,
    saldo_awal      DECIMAL(18,2) NOT NULL,
    saldo_akhir     DECIMAL(18,2) NOT NULL,
    audit_result    VARCHAR(100) NOT NULL,
    version_number  INT NOT NULL,
    version_status  VARCHAR(20) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT GETDATE(),
    created_by      VARCHAR(50) NOT NULL,
    updated_at      DATETIME NULL,
    updated_by      VARCHAR(50) NULL,
    CONSTRAINT FK_total_coa_main FOREIGN KEY (coa_main_id) REFERENCES ms_acc_coa_main(coa_main_id),
    CONSTRAINT FK_total_coasub1 FOREIGN KEY (coasub1_id) REFERENCES ms_acc_coasub1(coasub1_id),
    CONSTRAINT FK_total_coasub2 FOREIGN KEY (coasub2_id) REFERENCES ms_acc_coasub2(coasub2_id),
    CONSTRAINT FK_total_coa FOREIGN KEY (coa_id) REFERENCES ms_acc_coa(coa_id)
);
```

## Sample Data
| audit_period | coa_main_id | coasub1_id | coasub2_id | coa_id | saldo_awal | saldo_akhir | audit_result | version_number | version_status |
|-------------|------------|------------|------------|--------|------------|-------------|--------------|---------------|---------------|
| 2025        | 1          | 10         | 100        | 1000   | 5000000.00 | 5200000.00  | MATCH        | 1             | ACTIVE        |

## Relasi
- Satu audit total per COA per versi
- Dapat di-compare, di-rollback, dan di-update sesuai versioning

## Catatan
- Semua proses audit total harus menggunakan SP dengan naming convention terbaru (lihat STORED_PROCEDURE_NAMING_CONVENTION.md)
