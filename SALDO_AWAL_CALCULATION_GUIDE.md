# Saldo Awal Calculation - Quick Reference

## 📊 Tiga Metode Perhitungan Saldo Awal

### Method 1: Running Balance (Recommended) ✅

**Konsep**: Chain of balances - setiap bulan ambil saldo akhir bulan lalu

```
┌─────────────┐
│ Jan 2024    │
│ Saldo Awal: 0 (first period)
│ Mutasi: +100
│ Saldo Akhir: 100
└──────┬──────┘
       │ Carried forward
       ↓
┌─────────────┐
│ Feb 2024    │
│ Saldo Awal: 100 ← from Jan
│ Mutasi: +50
│ Saldo Akhir: 150
└──────┬──────┘
       │ Carried forward
       ↓
┌─────────────┐
│ Mar 2024    │
│ Saldo Awal: 150 ← from Feb
│ Mutasi: -20
│ Saldo Akhir: 130
└─────────────┘
```

**SQL Query**:
```sql
-- Get saldo awal from previous month
SELECT saldo_akhir
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202401'  -- Previous month
  AND coa_code = '1-1001';

-- Use it as saldo_awal for current month
```

**Pros**:
- ✅ Fast (only read previous month)
- ✅ Efficient
- ✅ Accurate if chain not broken

**Cons**:
- ❌ Need initial setup first
- ❌ If one month wrong, all subsequent wrong

---

### Method 2: Calculate from Beginning of Year

**Konsep**: Hitung dari 1 Januari s/d bulan sebelumnya setiap kali

```
For March 2024:
┌────────────────────────────────────┐
│ Sum all transactions:              │
│ 1 Jan 2024 → 29 Feb 2024          │
│                                    │
│ Total Debet:   1,000,000           │
│ Total Kredit:    500,000           │
│ Saldo Awal March = 500,000         │
└────────────────────────────────────┘
```

**SQL Query**:
```sql
-- For March 2024
SELECT 
    SUM(transcoa_debet_value) - SUM(transcoa_credit_value) AS saldo_awal
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND transcoa_coa_date >= '2024-01-01'
  AND transcoa_coa_date < '2024-03-01';  -- Before March
```

**Pros**:
- ✅ Self-correcting (always recalculate)
- ✅ Don't need previous month data

**Cons**:
- ❌ Slower (scan more transactions)
- ❌ Resource intensive for large data

---

### Method 3: Calculate from All Time (Initial Balance)

**Konsep**: Hitung dari transaksi pertama kali sampai cutoff date

```
For Initial Balance (31 Dec 2023):
┌────────────────────────────────────┐
│ Sum ALL transactions:              │
│ 2020 → 2021 → 2022 → 2023         │
│                                    │
│ Total Debet:   10,000,000          │
│ Total Kredit:   7,000,000          │
│ Opening Balance = 3,000,000        │
└────────────────────────────────────┘

This becomes Saldo Awal for Jan 2024
```

**SQL Query**:
```sql
-- Opening Balance as of 31 Dec 2023
SELECT 
    SUM(transcoa_debet_value) - SUM(transcoa_credit_value) AS opening_balance
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND transcoa_coa_date <= '2023-12-31';
```

**Pros**:
- ✅ Most accurate for first period
- ✅ Includes historical data

**Cons**:
- ❌ Very slow for old data
- ❌ Only needed once

---

## 🎯 Recommended Workflow

### Step 1: Initial Setup (One Time Only)

```sql
-- Option A: Calculate from all historical transactions
EXEC SP_setup_initial_balance 
    @target_year = 2024, 
    @target_month = 1;

-- Option B: Import from Excel/Previous System
-- See: database_setup_initial_balance.sql
```

Result:
```
periodo_id: 202401
coa_code: 1-1001
saldo_awal: 0
saldo_akhir: 3,000,000 ← This becomes opening balance
```

### Step 2: Monthly Generation (Every Month)

```sql
-- February
EXEC SP_generate_balance_sheet_monthly 
    @year = 2024, 
    @month = 2;

-- Uses saldo_akhir from 202401 as saldo_awal for 202402
```

### Step 3: Loop for Multiple Months

```sql
-- Generate all months for 2024
DECLARE @month INT = 1;
WHILE @month <= 12
BEGIN
    EXEC SP_generate_balance_sheet_monthly 
        @year = 2024, 
        @month = @month;
    SET @month = @month + 1;
END
```

---

## 🔍 Examples with Real Data

### Example 1: COA "1-1001" Cash Account

**Scenario**: Generate Balance Sheet untuk Maret 2024

```sql
-- Step 1: Get saldo akhir Februari (previous month)
SELECT saldo_akhir 
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202402' AND coa_code = '1-1001';
-- Result: 5,000,000

-- Step 2: Get mutasi Maret (current month)
SELECT 
    SUM(transcoa_debet_value) AS debet,
    SUM(transcoa_credit_value) AS kredit
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND transcoa_coa_date >= '2024-03-01'
  AND transcoa_coa_date <= '2024-03-31';
-- Result: Debet 2,000,000, Kredit 1,500,000

-- Step 3: Calculate
Saldo Awal Maret  = 5,000,000 (from Feb)
Mutasi Maret      = 2,000,000 - 1,500,000 = 500,000
Saldo Akhir Maret = 5,000,000 + 500,000 = 5,500,000
```

### Example 2: First Time Setup (Jan 2024)

```sql
-- Calculate from all historical transactions
SELECT 
    SUM(transcoa_debet_value) - SUM(transcoa_credit_value) AS opening
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND transcoa_coa_date <= '2023-12-31';
-- Result: 3,500,000

-- Then for January transactions:
SELECT 
    SUM(transcoa_debet_value) - SUM(transcoa_credit_value) AS mutasi
FROM tr_acc_transaksi_coa
WHERE transcoa_coa_code = '1-1001'
  AND transcoa_coa_date >= '2024-01-01'
  AND transcoa_coa_date <= '2024-01-31';
-- Result: 800,000

-- Final calculation:
Saldo Awal Jan 2024  = 3,500,000 (opening balance)
Mutasi Jan 2024      = 800,000
Saldo Akhir Jan 2024 = 4,300,000
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: Saldo Awal NULL or 0

**Cause**: Previous month data not exists

**Solution**:
```sql
-- Check if previous month exists
SELECT * FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202401';

-- If not exists, run initial setup
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;
```

### Issue 2: Saldo Not Balance

**Cause**: Missing transactions or wrong calculation

**Solution**:
```sql
-- Recalculate from scratch
DELETE FROM tr_acc_rekap_balance_sheet_monthly WHERE periode_year = 2024;

-- Setup initial
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;

-- Loop all months
DECLARE @m INT = 2;
WHILE @m <= 12
BEGIN
    EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = @m;
    SET @m = @m + 1;
END
```

### Issue 3: Performance Slow

**Cause**: Calculating from all time for every month

**Solution**: Use running balance (Method 1) instead of calculating from beginning each time

---

## 📝 Summary Table

| Method | Speed | Accuracy | Use Case |
|--------|-------|----------|----------|
| Running Balance | ⚡⚡⚡ Fast | ✅ High (if chain OK) | Monthly routine |
| From Year Start | ⚡⚡ Medium | ✅ High | Verification |
| From All Time | ⚡ Slow | ✅✅ Very High | Initial setup only |

---

## 🚀 Quick Start Commands

### First Time Setup:
```sql
-- 1. Create tables and SP
-- Run: database_balance_sheet_setup.sql

-- 2. Setup opening balance
EXEC SP_setup_initial_balance @target_year = 2024, @target_month = 1;

-- 3. Generate all months
DECLARE @m INT = 2;
WHILE @m <= 12
BEGIN
    EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = @m;
    SET @m = @m + 1;
END
```

### Monthly Routine:
```sql
-- End of month, generate new period
EXEC SP_generate_balance_sheet_monthly @year = 2024, @month = 12;
```

### Verification:
```sql
-- Check balance
SELECT 
    coa_main_desc,
    SUM(saldo_akhir) AS total
FROM tr_acc_rekap_balance_sheet_monthly
WHERE periode_id = '202412'
GROUP BY coa_main_desc
ORDER BY coa_main_desc;

-- Should balance:
-- Asset = Liability + Equity
```
