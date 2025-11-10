# 📋 Table Naming Convention

## 🎯 Standard Pattern

```
[PREFIX]_[MODULE]_[ENTITY]_[TYPE]
```

### Components:

1. **PREFIX**: Table category/type
2. **MODULE**: Business module (acc, hr, inv, etc)
3. **ENTITY**: Main entity name
4. **TYPE**: (Optional) Specific type or sub-category

---

## 📖 Table Prefixes

| Prefix | Purpose | Examples | Notes |
|--------|---------|----------|-------|
| **ms_** | Master Data | `ms_acc_coa`, `ms_acc_bank` | Static/reference data, jarang berubah |
| **tr_** | Transaction Data | `tr_acc_transaksi`, `tr_acc_cheque_h` | Dynamic data, sering insert/update |
| **tr_..._h** | Transaction Header | `tr_acc_cheque_h` | Header dari transaction (1 header → many detail) |
| **tr_..._d** | Transaction Detail | `tr_acc_cheque_d` | Detail dari transaction |
| **vw_** | View | `vw_acc_balance_sheet` | SQL View (virtual table) |
| **tmp_** | Temporary | `tmp_import_coa` | Temporary processing, bisa di-truncate |
| **log_** | Audit/History | `log_acc_closing_history` | Audit trail, tidak di-delete |
| **ref_** | Reference/Lookup | `ref_acc_status`, `ref_acc_category` | Dropdown values, enum-like data |

---

## 🗂️ Module Codes

| Module | Code | Examples |
|--------|------|----------|
| **Accounting** | `acc` | `ms_acc_coa`, `tr_acc_transaksi` |
| **Human Resource** | `hr` | `ms_hr_employee`, `tr_hr_payroll` |
| **Inventory** | `inv` | `ms_inv_item`, `tr_inv_stock_mutation` |
| **Sales** | `sales` | `ms_sales_customer`, `tr_sales_order` |
| **Purchase** | `pur` | `ms_pur_vendor`, `tr_pur_order` |
| **General** | (none) | `users`, `roles`, `permissions` | Laravel default tables |

---

## ✅ Current System Tables (Accounting)

### Master Data Tables (`ms_*`)

```sql
-- Chart of Accounts
ms_acc_coa                    -- Main COA table (all accounts)
ms_acc_coa_main               -- Main category (Asset, Liability, etc)
ms_acc_coa_sub1               -- Sub category level 1
ms_acc_coa_sub2               -- Sub category level 2
ms_acc_coa_sub3               -- Sub category level 3

-- Other Master Data
ms_acc_bank                   -- Bank master
ms_acc_vendor                 -- Vendor master
ms_acc_area                   -- Area/branch master
ms_acc_admin_sp               -- Stored procedure management
```

### Transaction Tables (`tr_*`)

```sql
-- Standard Transactions
tr_acc_transaksi              -- General journal transactions
tr_acc_transaksi_coa          -- Transaction detail per COA

-- Cheque Transactions (Header-Detail pattern)
tr_acc_cheque_h               -- Cheque header (1 cheque)
tr_acc_cheque_d               -- Cheque detail (multiple items per cheque)

-- Balance Sheet & Closing (NEW - with versioning)
tr_acc_balance_sheet_monthly  -- Monthly balance sheet recap
tr_acc_monthly_closing        -- Monthly closing (versioned)
tr_acc_yearly_closing         -- Yearly closing (versioned)
tr_acc_yearly_audit           -- Yearly audit (versioned)
tr_acc_total_audit            -- Total audit from inception (versioned)
```

### Reference Tables (`ref_*`)

```sql
ref_acc_status_cheque         -- Cheque status: ISSUED, CLEARED, VOID, etc
ref_acc_closing_status        -- Closing status: DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
ref_acc_category_type         -- COA category types
```

### Views (`vw_*`)

```sql
vw_acc_balance_sheet          -- Balance sheet view (aggregate)
vw_acc_trial_balance          -- Trial balance view
vw_acc_income_statement       -- Income statement view
vw_acc_coa_hierarchy          -- COA with full hierarchy
vw_acc_transaction_summary    -- Transaction summary view
```

### Temporary Tables (`tmp_*`)

```sql
tmp_acc_import_coa            -- Temporary storage for COA import
tmp_acc_import_opening_balance -- Temporary for opening balance import
tmp_acc_calculate_buffer      -- Buffer for complex calculations
```

### Log/Audit Tables (`log_*`)

```sql
log_acc_closing_history       -- History of closing operations
log_acc_version_changes       -- Version change audit trail
log_acc_coa_changes           -- COA modification history
log_acc_transaction_audit     -- Transaction audit log
```

---

## 🎨 Naming Patterns by Use Case

### 1️⃣ Header-Detail Pattern

```sql
-- Format: tr_[module]_[entity]_h and tr_[module]_[entity]_d

tr_acc_cheque_h               -- Header: 1 cheque
tr_acc_cheque_d               -- Detail: multiple items

tr_sales_order_h              -- Header: 1 sales order
tr_sales_order_d              -- Detail: multiple line items

tr_pur_invoice_h              -- Header: 1 purchase invoice
tr_pur_invoice_d              -- Detail: multiple invoice lines
```

**Benefits**: 
- Clear parent-child relationship
- Standard pattern across all modules
- Easy to understand data structure

### 2️⃣ Versioned Tables (Multi-Version)

```sql
-- Format: tr_[module]_[entity]_[period_type]

tr_acc_monthly_closing        -- Monthly closing with version_number
tr_acc_yearly_closing         -- Yearly closing with version_number
tr_acc_yearly_audit           -- Yearly audit with version_number
tr_acc_total_audit            -- Total audit with version_number
```

**Key Fields**:
- `version_number INT`
- `version_status VARCHAR(20)` -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
- `superseded_at DATETIME`
- `approved_at DATETIME`

### 3️⃣ Recap/Summary Tables

```sql
-- Format: tr_[module]_[type]_[entity]_[period]

tr_acc_rekap_balance_sheet_monthly    -- Monthly recap (old naming)
tr_acc_balance_sheet_monthly          -- Better: shorter, clearer

tr_acc_trial_balance_monthly
tr_acc_income_statement_monthly
tr_acc_cash_flow_monthly
```

**Purpose**: Pre-aggregated data untuk performance

### 4️⃣ Lookup/Reference Tables

```sql
-- Format: ref_[module]_[entity]

ref_acc_status_cheque         -- Cheque status lookup
ref_acc_closing_status        -- Closing status lookup
ref_acc_transaction_type      -- Transaction type lookup
ref_acc_category              -- Category lookup
```

**Characteristics**:
- Small tables (< 100 rows typically)
- Rarely change
- Used in dropdowns/enums
- Can have sort_order field

---

## 📝 Naming Rules

### 1. **Use Lowercase + Underscores**

```sql
✅ ms_acc_coa
✅ tr_acc_transaksi
❌ MS_ACC_COA                 (all caps)
❌ MsAccCoa                   (PascalCase)
❌ msAccCoa                   (camelCase)
❌ ms-acc-coa                 (hyphens not allowed)
```

### 2. **Be Descriptive, But Not Too Long**

```sql
✅ tr_acc_monthly_closing
✅ ms_acc_coa
✅ ref_acc_status_cheque

⚠️ tr_acc_monthly_closing_balance_sheet_report  (too long!)
⚠️ tr_acc_mc                                     (too cryptic!)
```

**Guideline**: Max 4-5 parts, total < 40 characters

### 3. **Use Singular Form for Entity Names**

```sql
✅ ms_acc_bank                (not banks)
✅ ms_acc_vendor              (not vendors)
✅ tr_acc_transaksi           (not transaksis)

Exception: If naturally plural
✅ ms_acc_closing_prerequisites
✅ ref_acc_categories
```

### 4. **Prefix Indicates Table Type Immediately**

```sql
-- You can tell the type just from prefix:
ms_acc_coa        → Master data
tr_acc_transaksi  → Transaction
vw_acc_balance    → View
ref_acc_status    → Reference
tmp_acc_import    → Temporary
log_acc_changes   → Audit log
```

### 5. **Module Code After Prefix**

```sql
-- Always: [prefix]_[module]_[entity]

✅ ms_acc_coa                 (accounting module)
✅ ms_hr_employee             (HR module)
✅ tr_inv_stock_mutation      (inventory module)

❌ ms_coa_acc                 (module should be second)
❌ coa_ms_acc                 (prefix must be first)
```

### 6. **Add Suffixes for Clarity**

```sql
-- Temporal suffixes
_monthly                      (monthly data)
_yearly                       (yearly data)
_daily                        (daily data)

-- Structural suffixes
_h                           (header)
_d                           (detail)
_temp                        (temporary)
_backup                      (backup table)
_archive                     (archived data)

Examples:
tr_acc_balance_sheet_monthly
tr_acc_cheque_h
tr_acc_cheque_d
ms_acc_coa_backup
tr_acc_transaksi_2024_archive
```

---

## 🔍 Discovery Patterns

### Find Tables by Type:

```sql
-- All master data tables
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME LIKE 'ms_%'
ORDER BY TABLE_NAME;

-- All accounting transactions
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME LIKE 'tr_acc_%'
ORDER BY TABLE_NAME;

-- All views
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.VIEWS 
WHERE TABLE_NAME LIKE 'vw_%'
ORDER BY TABLE_NAME;

-- All header-detail pairs
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME LIKE '%_h' 
   OR TABLE_NAME LIKE '%_d'
ORDER BY TABLE_NAME;
```

---

## 🎯 Migration from Old Names

### Current Issues:

```sql
-- Old naming (inconsistent)
RekapBalanceSheetMonthly      ❌ PascalCase
tr_acc_rekap_balance_sheet_monthly  ⚠️ Too long, "rekap" redundant

-- Better naming
tr_acc_balance_sheet_monthly  ✅ Clear, concise
```

### Recommended Changes:

| Old Name | New Name | Reason |
|----------|----------|--------|
| `RekapBalanceSheetMonthly` | `tr_acc_balance_sheet_monthly` | Follow convention |
| `ms_coa` | `ms_acc_coa` | Add module code |
| `cheque_header` | `tr_acc_cheque_h` | Follow prefix convention |
| `cheque_detail` | `tr_acc_cheque_d` | Follow prefix convention |

**Migration Strategy**:
1. Create new table with correct name
2. Create view with old name → points to new table
3. Update Laravel models to use new table
4. Deprecate old name over time

---

## 📊 Complete Table Structure (Current System)

```
Accounting Module Tables:

ms_acc_*                      (Master Data - 8 tables)
├── ms_acc_coa                Main COA
├── ms_acc_coa_main          Main categories
├── ms_acc_coa_sub1          Sub level 1
├── ms_acc_coa_sub2          Sub level 2
├── ms_acc_coa_sub3          Sub level 3
├── ms_acc_bank              Banks
├── ms_acc_vendor            Vendors
└── ms_acc_area              Areas/Branches

tr_acc_*                      (Transactions - 10 tables)
├── tr_acc_transaksi          General transactions
├── tr_acc_transaksi_coa      Transaction details
├── tr_acc_cheque_h           Cheque headers
├── tr_acc_cheque_d           Cheque details
├── tr_acc_balance_sheet_monthly    Balance sheet recap
├── tr_acc_monthly_closing    Monthly closing (versioned)
├── tr_acc_yearly_closing     Yearly closing (versioned)
├── tr_acc_yearly_audit       Yearly audit (versioned)
└── tr_acc_total_audit        Total audit (versioned)

ref_acc_*                     (Reference - 3 tables)
├── ref_acc_status_cheque     Cheque status values
├── ref_acc_closing_status    Closing status values
└── ref_acc_category_type     Category types

vw_acc_*                      (Views - 5 views)
├── vw_acc_balance_sheet      Balance sheet report
├── vw_acc_trial_balance      Trial balance
├── vw_acc_income_statement   Income statement
├── vw_acc_coa_hierarchy      COA with hierarchy
└── vw_acc_transaction_summary Transaction summary

log_acc_*                     (Audit - 3 tables)
├── log_acc_closing_history   Closing operations log
├── log_acc_version_changes   Version changes log
└── log_acc_coa_changes       COA modification log

tmp_acc_*                     (Temporary - 2 tables)
├── tmp_acc_import_coa        COA import buffer
└── tmp_acc_import_opening_balance Opening balance import

Laravel Default:              (Auth/System)
├── users
├── password_reset_tokens
├── sessions
├── cache
└── failed_jobs
```

---

## 🚫 Anti-Patterns (AVOID)

### ❌ Bad Examples:

```sql
-- Mixed case
Coa                          ❌ Use ms_acc_coa
ChequeMaster                 ❌ Use ms_acc_cheque

-- No prefix
coa                          ❌ Use ms_acc_coa
transactions                 ❌ Use tr_acc_transaksi

-- Unclear purpose
data_table                   ❌ Too vague
temp1                        ❌ Not descriptive
backup_20241109              ❌ Use proper backup strategy

-- Abbreviations without context
tr_acc_mc                    ❌ Use tr_acc_monthly_closing
ms_acc_v                     ❌ Use ms_acc_vendor

-- Wrong prefix usage
ms_acc_transaksi             ❌ Transaksi is transaction, use tr_
tr_acc_coa                   ❌ COA is master, use ms_
```

### ✅ Good Examples:

```sql
-- Clear and descriptive
ms_acc_coa                   ✅ Master → Chart of Accounts
tr_acc_transaksi             ✅ Transaction → Accounting transactions
ref_acc_status_cheque        ✅ Reference → Cheque status lookup
vw_acc_balance_sheet         ✅ View → Balance sheet report
log_acc_closing_history      ✅ Log → Closing audit trail

-- Proper header-detail
tr_acc_cheque_h              ✅ Header
tr_acc_cheque_d              ✅ Detail

-- Versioned tables
tr_acc_monthly_closing       ✅ Has version_number field
tr_acc_yearly_closing        ✅ Has version_number field
```

---

## 🎨 Laravel Model Naming

### Table → Model Mapping:

```php
// Laravel automatically pluralizes model names to find tables
// OR you can specify explicitly

// Master Data
ms_acc_coa           → CoaModel or Coa (specify table name)
ms_acc_bank          → BankModel or Bank
ms_acc_vendor        → VendorModel or Vendor

// Transactions
tr_acc_transaksi     → TransaksiModel or Transaksi
tr_acc_cheque_h      → ChequeH or ChequeHeader
tr_acc_cheque_d      → ChequeD or ChequeDetail

// Closing (Versioned)
tr_acc_monthly_closing → MonthlyClosing
tr_acc_yearly_closing  → YearlyClosing
tr_acc_yearly_audit    → YearlyAudit
tr_acc_total_audit     → TotalAudit
```

### Model Example:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyClosing extends Model
{
    // Explicitly set table name (since Laravel can't auto-detect)
    protected $table = 'tr_acc_monthly_closing';
    
    // Primary key (if not 'id')
    protected $primaryKey = 'id';
    
    // Disable timestamps if table doesn't have them
    // public $timestamps = false;
    
    // Or specify custom timestamp columns
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // No updated_at column
}
```

---

## 📋 Quick Reference Card

| Table Type | Prefix | Example | Purpose |
|------------|--------|---------|---------|
| Master Data | `ms_` | `ms_acc_coa` | Static reference data |
| Transaction | `tr_` | `tr_acc_transaksi` | Dynamic business data |
| Trans Header | `tr_..._h` | `tr_acc_cheque_h` | Transaction header |
| Trans Detail | `tr_..._d` | `tr_acc_cheque_d` | Transaction detail |
| Reference | `ref_` | `ref_acc_status` | Lookup values |
| View | `vw_` | `vw_acc_balance` | SQL view |
| Temporary | `tmp_` | `tmp_acc_import` | Temp processing |
| Audit Log | `log_` | `log_acc_changes` | Audit trail |

---

## ✨ Benefits of This Convention

1. ✅ **Instant Recognition**: Prefix tells you table type immediately
2. ✅ **Easy Discovery**: Find related tables with LIKE queries
3. ✅ **Consistent Structure**: Same pattern across all modules
4. ✅ **Self-Documenting**: Name explains purpose
5. ✅ **Module Separation**: Clear module boundaries
6. ✅ **Maintainable**: Easy to understand and modify
7. ✅ **Scalable**: Works for small and large databases
8. ✅ **Team-Friendly**: New developers quickly understand structure

---

**Last Updated**: 2024-01-09  
**Version**: 1.0  
**Author**: AccAdmin Development Team
