# 🔗 Relationship Design: Transaction vs Closing Tables

## ❓ Question
**Apakah `tr_acc_transaksi_coa` perlu field untuk link ke closing tables?**

---

## 📊 Current Situation Analysis

### Table: `tr_acc_transaksi_coa`
**Purpose**: Transaction detail per COA (setiap baris = 1 transaksi untuk 1 COA)

**Current Fields** (dari Model):
```php
- transcoa_code              // Transaction detail code
- transcoa_transaksi_main_code // FK to main transaction
- transcoa_coa_code          // FK to COA
- transcoa_debet_value       // Debit amount
- transcoa_credit_value      // Credit amount
- transcoa_coa_date          // Transaction date
- transcoa_statusposting     // Posting status
- transcoa_dateposting       // Posted date
- rec_status                 // Record status
// ... other fields
```

### New Tables: Closing System (4 tables)
```
tr_acc_monthly_closing       // Monthly closing (versioned)
tr_acc_yearly_closing        // Yearly closing (versioned)
tr_acc_yearly_audit          // Yearly audit (versioned)
tr_acc_total_audit           // Total audit (versioned)
```

---

## 🤔 Two Possible Approaches

### **Option 1: NO FK from Transaction to Closing** ✅ RECOMMENDED

**Concept**: Transactions are **SOURCE**, Closings are **DERIVED**

```
tr_acc_transaksi_coa (Source Data)
         ↓
    (aggregated by SP)
         ↓
tr_acc_monthly_closing (Derived/Calculated)
         ↓
    (aggregated)
         ↓
tr_acc_yearly_closing (Derived)
```

**Reasoning**:
1. **Transactions are PERMANENT** - tidak berubah setelah posting
2. **Closings are SNAPSHOTS** - hasil agregasi dari transactions
3. **One-to-Many Direction**: 1 closing bisa include banyak transactions
4. **Version Independence**: Jika regenerate closing (new version), transactions tidak perlu diubah

**No New Fields Needed in `tr_acc_transaksi_coa`**

**Benefits**:
- ✅ Transactions tetap independent
- ✅ Bisa regenerate closing berkali-kali tanpa touch transactions
- ✅ Lebih simple, less coupling
- ✅ Closing version bisa berubah, transactions tidak terpengaruh

**How to Link** (when needed):
```sql
-- Find transactions for specific closing periode
SELECT * 
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND YEAR(transcoa_coa_date) = 2024
  AND MONTH(transcoa_coa_date) = 1;

-- This is what SP_generate_monthly_closing_new_version does
```

---

### **Option 2: Add FK from Transaction to Closing** ⚠️ NOT RECOMMENDED

**Concept**: Track which closing "owns" each transaction

**New Field in `tr_acc_transaksi_coa`**:
```sql
ALTER TABLE tr_acc_transaksi_coa
ADD monthly_closing_id INT NULL;  -- FK to tr_acc_monthly_closing.id
```

**Problems**:
1. ❌ **Versioning Complexity**: Which version? Active? Latest?
2. ❌ **Update Overhead**: Perlu update semua transactions saat closing
3. ❌ **Tight Coupling**: Transactions depends on closing
4. ❌ **Regenerate Issues**: Jika regenerate closing (new version), perlu update semua FK
5. ❌ **Historical Confusion**: Old transactions point to SUPERSEDED closing?

**Example Problem**:
```sql
-- January 2024 transactions (1000 rows)
-- Point to monthly_closing_id = 123 (version 1, ACTIVE)

-- Later: regenerate closing (version 2)
-- monthly_closing_id = 456 (version 2, ACTIVE)
-- monthly_closing_id = 123 (version 1, SUPERSEDED)

-- Problem: Do we update 1000 transaction rows?
-- What if we want to rollback to version 1?
```

---

## 🎯 Recommended Design: Option 1 (No FK)

### Data Flow:

```
┌──────────────────────────────┐
│   tr_acc_transaksi_coa      │ ← Source of Truth
│                              │
│  - transcoa_coa_code         │ ← Which COA
│  - transcoa_coa_date         │ ← Which date (YYYY-MM-DD)
│  - transcoa_debet_value      │ ← Amount
│  - transcoa_credit_value     │ ← Amount
│  - transcoa_statusposting    │ ← Posted or not
└──────────────┬───────────────┘
               │
               │ (aggregate via SP)
               ↓
┌──────────────────────────────┐
│  tr_acc_monthly_closing      │ ← Derived/Snapshot
│                              │
│  - closing_periode_id        │ ← 202401, 202402, etc
│  - coa_code                  │ ← 1-1001, 1-1002, etc
│  - version_number            │ ← 1, 2, 3, etc
│  - version_status            │ ← DRAFT, ACTIVE, SUPERSEDED
│  - opening_balance           │ ← Calculated
│  - mutasi_debet             │ ← SUM from transactions
│  - mutasi_kredit            │ ← SUM from transactions
│  - closing_balance           │ ← opening + mutasi
└──────────────────────────────┘
```

### How to Query (Bidirectional):

**1. From Closing → Find Source Transactions:**
```sql
-- Given closing: periode 202401, COA 1-1001, version 2
-- Find source transactions:

SELECT * 
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND YEAR(transcoa_coa_date) = 2024
  AND MONTH(transcoa_coa_date) = 1
  AND transcoa_statusposting = 'Y'  -- Only posted
ORDER BY transcoa_coa_date;
```

**2. From Transaction → Find Closing:**
```sql
-- Given transaction date: 2024-01-15
-- Find which closing it belongs to:

SELECT * 
FROM tr_acc_monthly_closing
WHERE closing_periode_id = '202401'  -- From transaction date
  AND coa_code = '1-1001'            -- From transaction COA
  AND version_status = 'ACTIVE'      -- Current active version
```

**3. Audit Trail (Compare Closing vs Actual):**
```sql
-- Verify closing matches actual transactions
SELECT 
    c.coa_code,
    c.closing_periode_id,
    c.version_number,
    c.mutasi_debet AS closing_debet,
    c.mutasi_kredit AS closing_kredit,
    
    -- Recalculate from source
    ISNULL(SUM(t.transcoa_debet_value), 0) AS actual_debet,
    ISNULL(SUM(t.transcoa_credit_value), 0) AS actual_kredit,
    
    -- Variance
    c.mutasi_debet - ISNULL(SUM(t.transcoa_debet_value), 0) AS variance_debet,
    c.mutasi_kredit - ISNULL(SUM(t.transcoa_credit_value), 0) AS variance_kredit
    
FROM tr_acc_monthly_closing c
LEFT JOIN tr_acc_transaksi_coa t
    ON t.transcoa_coa_code = c.coa_code
   AND YEAR(t.transcoa_coa_date) = c.closing_year
   AND MONTH(t.transcoa_coa_date) = c.closing_month
   AND t.transcoa_statusposting = 'Y'
WHERE c.closing_periode_id = '202401'
  AND c.version_status = 'ACTIVE'
GROUP BY 
    c.coa_code,
    c.closing_periode_id,
    c.version_number,
    c.mutasi_debet,
    c.mutasi_kredit
HAVING 
    ABS(c.mutasi_debet - ISNULL(SUM(t.transcoa_debet_value), 0)) > 0.01
    OR ABS(c.mutasi_kredit - ISNULL(SUM(t.transcoa_credit_value), 0)) > 0.01;
```

---

## 📋 Period Locking (Alternative to FK)

### Recommended: Use Function-Based Locking

**Purpose**: Prevent modification of transactions after closing (tanpa FK)

**Implementation**:
```sql
-- Function to check if periode is closed
CREATE FUNCTION dbo.FN_is_periode_closed(
    @year INT,
    @month INT
)
RETURNS BIT
AS
BEGIN
    DECLARE @is_closed BIT = 0;
    
    SELECT @is_closed = is_closed
    FROM tr_acc_monthly_closing
    WHERE closing_year = @year
      AND closing_month = @month
      AND version_status = 'ACTIVE';
    
    RETURN ISNULL(@is_closed, 0);
END;
GO

-- Use in transaction posting SP:
CREATE PROCEDURE SP_post_transaction
    @transaction_date DATE,
    @coa_code VARCHAR(50),
    @debet DECIMAL(18,2),
    @kredit DECIMAL(18,2)
AS
BEGIN
    -- Check if periode is closed
    IF dbo.FN_is_periode_closed(
        YEAR(@transaction_date), 
        MONTH(@transaction_date)
    ) = 1
    BEGIN
        THROW 50001, 
            'Cannot post to closed period: ' + 
            FORMAT(@transaction_date, 'yyyy-MM'), 
            1;
    END;
    
    -- Continue with posting...
    INSERT INTO tr_acc_transaksi_coa (...)
    VALUES (...);
END;
GO
```

**Benefits**:
- ✅ No new field in transaction table
- ✅ Centralized locking logic
- ✅ Easy to unlock (change closing.is_closed flag)
- ✅ Works with versioning system

---

## 🎨 Add Metadata to Closing (Track Source)

**Fields to add in closing table** (for audit trail):

```sql
-- In tr_acc_monthly_closing:
jumlah_transaksi INT DEFAULT 0,           -- Count of transactions
transaction_date_min DATE,                -- First transaction date
transaction_date_max DATE,                -- Last transaction date
transaction_ids NVARCHAR(MAX)             -- Optional: comma-separated IDs (if needed)
```

**Populated by SP**:
```sql
-- In SP_generate_monthly_closing_new_version:
INSERT INTO tr_acc_monthly_closing (
    closing_year,
    closing_month,
    coa_code,
    mutasi_debet,
    mutasi_kredit,
    jumlah_transaksi,            -- ← Track count
    transaction_date_min,        -- ← Track date range
    transaction_date_max,        -- ← Track date range
    ...
)
SELECT 
    @year,
    @month,
    transcoa_coa_code,
    SUM(transcoa_debet_value) AS mutasi_debet,
    SUM(transcoa_credit_value) AS mutasi_kredit,
    COUNT(*) AS jumlah_transaksi,
    MIN(transcoa_coa_date) AS transaction_date_min,
    MAX(transcoa_coa_date) AS transaction_date_max
FROM tr_acc_transaksi_coa
WHERE YEAR(transcoa_coa_date) = @year
  AND MONTH(transcoa_coa_date) = @month
  AND transcoa_statusposting = 'Y'
GROUP BY transcoa_coa_code;
```

---

## 📊 Comparison Table

| Aspect | No FK (Recommended) | With FK (Not Recommended) |
|--------|---------------------|---------------------------|
| **Coupling** | ✅ Loose | ❌ Tight |
| **Versioning** | ✅ Simple (closing only) | ❌ Complex (update all FK) |
| **Regenerate** | ✅ Easy (no transaction update) | ❌ Hard (update 1000s of rows) |
| **Query Performance** | ✅ Use date+COA index | ⚠️ Need FK index |
| **Data Integrity** | ✅ Use function check | ⚠️ Need trigger + FK maintenance |
| **Rollback** | ✅ Easy (just change version status) | ❌ Hard (update all FK again) |
| **Storage** | ✅ No extra field | ❌ Extra INT per transaction |
| **Maintenance** | ✅ Low | ❌ High |

---

## 🚀 Final Recommendation

### ✅ **DO NOT add FK from `tr_acc_transaksi_coa` to closing tables**

**Reasons**:
1. **Separation of Concerns**: Transactions = source, Closing = derived
2. **Versioning**: Closing has versions, transactions don't need to know
3. **Regenerate Freedom**: Bisa regenerate closing berkali-kali
4. **Simple Query**: Link via date + COA (already indexed)
5. **Less Maintenance**: No need to update transactions when closing changes

### ✅ **DO add periode locking mechanism**

**Implementation**: Use function + application logic (NOT transaction field)

### ✅ **DO add metadata to closing table**

**Fields**: `jumlah_transaksi`, `transaction_date_min`, `transaction_date_max`

---

## 📝 Implementation Summary

### What to DO:
1. ✅ **Keep transactions independent** - no FK to closing
2. ✅ **Link via query** - use date + COA (already indexed)
3. ✅ **Add period locking** - via function, not transaction field
4. ✅ **Add metadata to closing** - track source count, date range
5. ✅ **Use SP for consistency** - all closing operations via SP

### What NOT to do:
1. ❌ Add `monthly_closing_id` FK to transactions
2. ❌ Add `is_closed` field to transactions (use function instead)
3. ❌ Tight coupling between source and derived data
4. ❌ Update transactions when regenerating closing

---

**Conclusion**: **NO FK needed** in `tr_acc_transaksi_coa`. 

Use **function-based locking** + **metadata in closing table** instead! ✅
