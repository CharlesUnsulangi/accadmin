# Multi-Layer Closing & Audit System

## 📋 Overview

Sistem closing berlapis dengan audit trail untuk memastikan data akuntansi terjaga integritas dan bisa di-trace back.

## 🎯 Konsep 4-Layer System

```
┌─────────────────────────────────────────────────────────┐
│                    LAYER STRUCTURE                      │
└─────────────────────────────────────────────────────────┘

Layer 1: MONTHLY CLOSING (Bulanan)
├── Januari 2024 → Saved ✓ (permanent, no recalculate)
├── Februari 2024 → Saved ✓
├── ...
└── Desember 2024 → Saved ✓
         ↓ Aggregate
         
Layer 2: YEARLY CLOSING (Tahunan)
├── 2024 → Aggregate dari 12 monthly closing
├── 2025 → Uses closing 2024 as opening balance
└── ...
         ↓ Freeze for audit
         
Layer 3: YEARLY AUDIT (Audit per Tahun)
├── Audit 2024 → Uses opening balance from yearly closing 2024
├── Verify against monthly closings
└── Locked after audit complete
         ↓ Final verification
         
Layer 4: TOTAL AUDIT (Audit dari Awal)
└── Recalculate from first transaction ever
    Compare with yearly audits
    Final truth source
```

## 🗂️ Database Structure (WITH VERSIONING)

### Versioning Concept:
- **Setiap generate = version baru**
- **Old version tetap tersimpan** (tidak di-delete)
- **Status lifecycle**: DRAFT → ACTIVE → SUPERSEDED → ARCHIVED
- **Bisa compare antar version**
- **Bisa rollback jika perlu**

### Table 1: `tr_acc_monthly_closing`
**Purpose**: Simpan closing per bulan dengan version control

```sql
CREATE TABLE tr_acc_monthly_closing (
    id INT IDENTITY(1,1) PRIMARY KEY,
    
    -- Version Control
    version_number INT NOT NULL DEFAULT 1,         -- Version 1, 2, 3, dst
    version_status VARCHAR(20) NOT NULL,           -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
    version_note NVARCHAR(500),                    -- Reason for new version
    
    -- Periode
    closing_year INT NOT NULL,
    closing_month INT NOT NULL,
    closing_periode_id VARCHAR(6) NOT NULL,        -- YYYYMM (202401)
    
    -- COA
    coa_code VARCHAR(50) NOT NULL,
    coa_desc NVARCHAR(255),
    coa_main_code VARCHAR(10),
    coa_main_desc NVARCHAR(100),
    
    -- Opening Balance (dari bulan/tahun sebelumnya)
    opening_debet DECIMAL(18,2) DEFAULT 0,
    opening_kredit DECIMAL(18,2) DEFAULT 0,
    opening_balance DECIMAL(18,2) DEFAULT 0,
    
    -- Mutasi Bulan Ini
    mutasi_debet DECIMAL(18,2) DEFAULT 0,
    mutasi_kredit DECIMAL(18,2) DEFAULT 0,
    mutasi_netto DECIMAL(18,2) DEFAULT 0,
    jumlah_transaksi INT DEFAULT 0,
    
    -- Closing Balance
    closing_debet DECIMAL(18,2) DEFAULT 0,
    closing_kredit DECIMAL(18,2) DEFAULT 0,
    closing_balance DECIMAL(18,2) DEFAULT 0,
    
    -- Status & Lock
    is_closed BIT DEFAULT 0,                       -- True = locked, cannot modify
    closed_at DATETIME,
    closed_by VARCHAR(50),
    
    -- Superseded Info (jika ada version baru)
    superseded_at DATETIME,                        -- When this version was replaced
    superseded_by VARCHAR(50),                     -- Who created new version
    superseded_by_version INT,                     -- Which version replaced this
    
    -- Audit Trail
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(50),
    
    -- Unique per version
    CONSTRAINT UQ_Monthly_Closing_Version UNIQUE (closing_periode_id, coa_code, version_number),
    INDEX IX_Monthly_Year_Month (closing_year, closing_month),
    INDEX IX_Monthly_COA (coa_code),
    INDEX IX_Monthly_Status (version_status),
    INDEX IX_Monthly_Version (closing_periode_id, version_number),
    INDEX IX_Monthly_Active (closing_periode_id, coa_code, version_status) 
        WHERE version_status = 'ACTIVE'
);
```

**Version Status Flow**:
```
DRAFT 
  ↓ (after review & approve)
ACTIVE (currently used)
  ↓ (when new version generated)
SUPERSEDED (old but kept for history)
  ↓ (after retention period)
ARCHIVED (moved to archive table - optional)
```

### Table 2: `tr_acc_yearly_closing`
**Purpose**: Aggregate dari monthly closing (end of year)

```sql
CREATE TABLE tr_acc_yearly_closing (
    id INT IDENTITY(1,1) PRIMARY KEY,
    
    -- Periode
    closing_year INT NOT NULL,
    closing_periode_id VARCHAR(4) NOT NULL,        -- YYYY (2024)
    
    -- COA
    coa_code VARCHAR(50) NOT NULL,
    coa_desc NVARCHAR(255),
    coa_main_code VARCHAR(10),
    coa_main_desc NVARCHAR(100),
    
    -- Opening Balance (dari tahun sebelumnya)
    opening_debet DECIMAL(18,2) DEFAULT 0,
    opening_kredit DECIMAL(18,2) DEFAULT 0,
    opening_balance DECIMAL(18,2) DEFAULT 0,
    
    -- Mutasi Setahun (aggregate 12 bulan)
    mutasi_debet DECIMAL(18,2) DEFAULT 0,
    mutasi_kredit DECIMAL(18,2) DEFAULT 0,
    mutasi_netto DECIMAL(18,2) DEFAULT 0,
    jumlah_transaksi INT DEFAULT 0,
    
    -- Closing Balance
    closing_debet DECIMAL(18,2) DEFAULT 0,
    closing_kredit DECIMAL(18,2) DEFAULT 0,
    closing_balance DECIMAL(18,2) DEFAULT 0,
    
    -- References ke monthly closings
    jan_closing_id INT,
    feb_closing_id INT,
    mar_closing_id INT,
    apr_closing_id INT,
    may_closing_id INT,
    jun_closing_id INT,
    jul_closing_id INT,
    aug_closing_id INT,
    sep_closing_id INT,
    oct_closing_id INT,
    nov_closing_id INT,
    dec_closing_id INT,
    
    -- Status & Lock
    is_closed BIT DEFAULT 0,
    closed_at DATETIME,
    closed_by VARCHAR(50),
    
    -- Audit Trail
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(50),
    
    CONSTRAINT UQ_Yearly_Closing UNIQUE (closing_periode_id, coa_code),
    INDEX IX_Yearly_Year (closing_year),
    INDEX IX_Yearly_COA (coa_code)
);
```

### Table 3: `tr_acc_yearly_audit`
**Purpose**: Audit per tahun dengan opening dari yearly closing

```sql
CREATE TABLE tr_acc_yearly_audit (
    id INT IDENTITY(1,1) PRIMARY KEY,
    
    -- Audit Info
    audit_id VARCHAR(20) NOT NULL UNIQUE,          -- AUDIT-2024-001
    audit_year INT NOT NULL,
    audit_date DATE NOT NULL,
    auditor_name VARCHAR(100),
    
    -- COA
    coa_code VARCHAR(50) NOT NULL,
    coa_desc NVARCHAR(255),
    coa_main_code VARCHAR(10),
    coa_main_desc NVARCHAR(100),
    
    -- Balance from Yearly Closing (what was reported)
    yearly_closing_balance DECIMAL(18,2),
    yearly_closing_id INT,                         -- FK to tr_acc_yearly_closing
    
    -- Balance from Recalculation (audit verification)
    audit_calculated_balance DECIMAL(18,2),
    
    -- Variance
    variance DECIMAL(18,2),                        -- Difference
    variance_percentage DECIMAL(5,2),
    
    -- Audit Status
    is_matched BIT DEFAULT 0,                      -- True if variance = 0
    audit_status VARCHAR(20),                      -- PASS, FAIL, REVIEW
    audit_notes NVARCHAR(500),
    
    -- Approval
    is_approved BIT DEFAULT 0,
    approved_at DATETIME,
    approved_by VARCHAR(50),
    
    -- Audit Trail
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(50),
    
    INDEX IX_Audit_Year (audit_year),
    INDEX IX_Audit_COA (coa_code),
    INDEX IX_Audit_Status (audit_status)
);
```

### Table 4: `tr_acc_total_audit`
**Purpose**: Audit total dari awal sekali (ultimate verification)

```sql
CREATE TABLE tr_acc_total_audit (
    id INT IDENTITY(1,1) PRIMARY KEY,
    
    -- Audit Info
    audit_id VARCHAR(20) NOT NULL UNIQUE,          -- TOTAL-AUDIT-2024-001
    audit_date DATE NOT NULL,
    audit_cutoff_date DATE NOT NULL,               -- Calculate up to this date
    auditor_name VARCHAR(100),
    
    -- COA
    coa_code VARCHAR(50) NOT NULL,
    coa_desc NVARCHAR(255),
    coa_main_code VARCHAR(10),
    coa_main_desc NVARCHAR(100),
    
    -- Calculation from First Transaction Ever
    first_transaction_date DATE,
    last_transaction_date DATE,
    total_transactions INT,
    total_debet DECIMAL(18,2),
    total_kredit DECIMAL(18,2),
    calculated_balance DECIMAL(18,2),
    
    -- Compare with Latest Yearly Closing
    latest_yearly_closing_balance DECIMAL(18,2),
    latest_yearly_closing_year INT,
    variance DECIMAL(18,2),
    
    -- Audit Result
    is_verified BIT DEFAULT 0,
    audit_status VARCHAR(20),                      -- VERIFIED, MISMATCH, PENDING
    audit_notes NVARCHAR(1000),
    
    -- Approval
    is_approved BIT DEFAULT 0,
    approved_at DATETIME,
    approved_by VARCHAR(50),
    
    -- Audit Trail
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(50),
    
    INDEX IX_TotalAudit_Date (audit_date),
    INDEX IX_TotalAudit_COA (coa_code)
);
```

## 🔄 Process Flow

### Flow 1: Monthly Closing Process

```
1. End of Month (e.g., 31 Jan 2024)
   ↓
2. Generate Monthly Closing
   EXEC SP_close_monthly @year=2024, @month=1
   - Calculate from transactions
   - Save to tr_acc_monthly_closing
   - Set is_closed = 0 (not locked yet)
   ↓
3. Review & Verify
   - Check balance
   - Verify all transactions posted
   ↓
4. Lock Monthly Closing
   EXEC SP_lock_monthly_closing @periode='202401'
   - Set is_closed = 1
   - Cannot modify anymore
   ↓
5. Use for Next Month
   - Opening Feb = Closing Jan
```

### Flow 2: Yearly Closing Process

```
1. End of Year (31 Dec 2024)
   ↓
2. Verify All Monthly Closings Locked
   - Check Jan-Dec all is_closed = 1
   ↓
3. Generate Yearly Closing
   EXEC SP_close_yearly @year=2024
   - Aggregate 12 monthly closings
   - Save to tr_acc_yearly_closing
   - Link to monthly closing IDs
   ↓
4. Review & Approve
   ↓
5. Lock Yearly Closing
   - Set is_closed = 1
   ↓
6. Use for Next Year
   - Opening 2025 = Closing 2024
```

### Flow 3: Yearly Audit Process

```
1. Audit Time (after yearly closing)
   ↓
2. Generate Yearly Audit
   EXEC SP_audit_yearly @year=2024
   - Read yearly_closing_balance
   - Recalculate from monthly closings
   - Compare variance
   ↓
3. Review Variances
   - If variance ≠ 0 → investigate
   ↓
4. Approve Audit
   - Set is_approved = 1
```

### Flow 4: Total Audit Process

```
1. Special Audit Time (quarterly/annually)
   ↓
2. Generate Total Audit
   EXEC SP_audit_total @cutoff_date='2024-12-31'
   - Calculate from FIRST transaction ever
   - Compare with yearly closings
   ↓
3. Ultimate Verification
   - Should match yearly closing
   - If not → investigate all years
   ↓
4. Approve
   - This is final truth
```

## 🔒 Locking Mechanism

### Rules:
1. **Monthly Closing**: Cannot be modified after `is_closed = 1`
2. **Yearly Closing**: Cannot be generated if monthly not all locked
3. **Yearly Audit**: Uses locked yearly closing data
4. **Total Audit**: Read-only verification

### Enforcement:
```sql
-- Trigger to prevent modification of closed data
CREATE TRIGGER trg_prevent_modify_closed_monthly
ON tr_acc_monthly_closing
INSTEAD OF UPDATE, DELETE
AS
BEGIN
    IF EXISTS (SELECT 1 FROM deleted WHERE is_closed = 1)
    BEGIN
        RAISERROR('Cannot modify or delete closed monthly closing!', 16, 1);
        ROLLBACK TRANSACTION;
    END
END
```

## 📊 Benefits of This Design

### ✅ Advantages:

1. **Performance**
   - Monthly closing saved permanently
   - No need to recalculate every time
   - Fast retrieval for reports

2. **Data Integrity**
   - Locked closings cannot be changed
   - Audit trail complete
   - Multiple verification layers

3. **Compliance**
   - Yearly audit for external auditors
   - Total audit for ultimate verification
   - Full history preserved

4. **Flexibility**
   - Can review any month anytime
   - Can trace back to source
   - Multiple audit points

### ⚠️ Considerations:

1. **Storage**
   - Need more tables
   - Historical data grows
   - Solution: Archive old audits

2. **Complexity**
   - More SPs to maintain
   - More validation rules
   - Solution: Good documentation

3. **User Training**
   - Users need to understand flow
   - Different permission levels
   - Solution: Clear UI and guides

## 🎯 Recommended Implementation Order

1. ✅ Create all 4 tables
2. ✅ Create SP_close_monthly (with lock)
3. ✅ Create SP_close_yearly (aggregate)
4. ✅ Create SP_audit_yearly (verification)
5. ✅ Create SP_audit_total (ultimate check)
6. ✅ Create triggers for locking
7. ✅ Create UI for each process
8. ✅ Test with real data
9. ✅ Train users
10. ✅ Go live

## 📝 Summary Comparison

| Layer | Frequency | Data Source | Purpose | Can Modify? |
|-------|-----------|-------------|---------|-------------|
| Monthly Closing | Monthly | Transactions | Daily operations | No (after lock) |
| Yearly Closing | Yearly | 12 Monthly Closings | Annual report | No (after lock) |
| Yearly Audit | Yearly | Yearly Closing | External audit | No (read-only) |
| Total Audit | On-demand | All Transactions | Ultimate verification | No (read-only) |

## 🚀 Next Steps

Would you like me to:
1. Create the complete SQL scripts for all tables?
2. Create all stored procedures?
3. Design the UI/workflow for each process?
4. Create the locking mechanism and triggers?

This is a solid enterprise-grade accounting closure system! 🎯
