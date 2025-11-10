# 📋 Stored Procedure Naming Convention

## 🎯 Standard Pattern

```
SP_[ACTION]_[TABLE/MODULE]_[DETAIL]
```

### Components:

1. **Prefix**: `SP_` (all stored procedures)
2. **ACTION**: Verb yang menjelaskan apa yang dilakukan
3. **TABLE/MODULE**: Target table atau module name
4. **DETAIL**: (Optional) Specific detail atau sub-action

---

## 📖 ACTION Verbs

| Action | Usage | Example |
|--------|-------|---------|
| **generate** | Create NEW data (bulk INSERT) | `SP_generate_balance_sheet_monthly` |
| **update** | Modify EXISTING data (UPDATE) | `SP_update_monthly_closing_approve_version` |
| **calculate** | Compute values (SELECT with math) | `SP_calculate_saldo_awal` |
| **setup** | Initial configuration/setup | `SP_setup_initial_balance` |
| **compare** | Compare/analyze data (SELECT) | `SP_compare_monthly_closing_versions` |
| **approve** | Change status to approved | `SP_update_monthly_closing_approve_version` |
| **rollback** | Revert changes | `SP_update_monthly_closing_rollback_version` |
| **archive** | Soft delete/move to archive | `SP_update_monthly_closing_archive_old_versions` |
| **delete** | Hard delete (rare, use archive instead) | `SP_delete_temp_data` |
| **get** | Simple SELECT query | `SP_get_coa_hierarchy` |
| **validate** | Check data integrity | `SP_validate_closing_prerequisites` |

---

## ✅ Examples by Category

### 1. Balance Sheet & Closing System

```sql
-- Generate (INSERT bulk)
SP_generate_balance_sheet_monthly
SP_generate_monthly_closing_new_version
SP_generate_yearly_closing
SP_generate_yearly_audit
SP_generate_total_audit

-- Update (UPDATE existing)
SP_update_monthly_closing_approve_version
SP_update_monthly_closing_rollback_version
SP_update_monthly_closing_archive_old_versions
SP_update_balance_sheet_lock_periode

-- Calculate (Compute values)
SP_calculate_saldo_awal
SP_calculate_running_balance
SP_calculate_yearly_totals

-- Setup (Initial configuration)
SP_setup_initial_balance
SP_setup_opening_balance_from_excel

-- Compare (Analysis)
SP_compare_monthly_closing_versions
SP_compare_balance_sheet_periods
SP_compare_actual_vs_closing

-- Validate (Check)
SP_validate_closing_prerequisites
SP_validate_version_consistency
```

### 2. COA (Chart of Accounts)

```sql
-- Generate
SP_generate_coa_full_hierarchy
SP_generate_coa_trail_balance

-- Update
SP_update_coa_activate
SP_update_coa_deactivate
SP_update_coa_merge

-- Get
SP_get_coa_hierarchy
SP_get_coa_by_category

-- Validate
SP_validate_coa_code_format
SP_validate_coa_parent_child
```

### 3. Transactions

```sql
-- Generate
SP_generate_jurnal_from_transaksi
SP_generate_monthly_summary

-- Update
SP_update_transaksi_void
SP_update_transaksi_reverse

-- Calculate
SP_calculate_transaction_balance
SP_calculate_monthly_mutation

-- Validate
SP_validate_debet_kredit_balance
SP_validate_transaction_periode
```

### 4. Cheque Management

```sql
-- Generate
SP_generate_cheque_register
SP_generate_outstanding_cheques

-- Update
SP_update_cheque_status_cleared
SP_update_cheque_status_void
SP_update_cheque_status_returned

-- Get
SP_get_cheques_by_status
SP_get_cheques_by_bank

-- Validate
SP_validate_cheque_duplicate
```

### 5. Reporting

```sql
-- Generate
SP_generate_trial_balance
SP_generate_income_statement
SP_generate_balance_sheet_report
SP_generate_cash_flow_statement

-- Get
SP_get_report_parameters
SP_get_financial_summary

-- Calculate
SP_calculate_financial_ratios
SP_calculate_variance_analysis
```

---

## 🚫 Anti-Patterns (AVOID)

### ❌ Bad Examples:

```sql
-- Too vague
SP_process_data              -- What process? What data?
SP_do_closing                -- Do what exactly?
SP_fix_balance              -- Fix how?

-- Missing action verb
SP_monthly_closing          -- Generate? Update? Get?
SP_balance_sheet           -- Generate? Calculate?

-- Inconsistent naming
SP_CloseMonthly            -- Use snake_case, not PascalCase
sp_close_monthly           -- Use uppercase SP_
SP_Close_Monthly           -- Don't mix case styles

-- Abbreviations without context
SP_gen_bs_mth              -- Unreadable
SP_upd_mc_ver              -- What is mc?
```

### ✅ Good Examples:

```sql
-- Clear action + target
SP_generate_balance_sheet_monthly
SP_update_monthly_closing_approve_version
SP_calculate_saldo_awal

-- Descriptive and specific
SP_update_cheque_status_cleared
SP_validate_closing_prerequisites
SP_compare_monthly_closing_versions
```

---

## 📝 Naming Rules

### 1. **Always Start with `SP_`**
```sql
✅ SP_generate_balance_sheet_monthly
❌ generate_balance_sheet_monthly
❌ proc_generate_balance_sheet
```

### 2. **Use Action Verb First**
```sql
✅ SP_update_monthly_closing_approve
❌ SP_monthly_closing_update_approve
❌ SP_approve_monthly_closing  (use update_...approve)
```

### 3. **Use Underscores (snake_case)**
```sql
✅ SP_generate_balance_sheet_monthly
❌ SP_GenerateBalanceSheetMonthly  (PascalCase)
❌ SP_generateBalanceSheetMonthly  (camelCase)
```

### 4. **Be Descriptive, Not Cryptic**
```sql
✅ SP_generate_balance_sheet_monthly
❌ SP_gen_bs_mth
❌ SP_create_report
```

### 5. **Add Context When Needed**
```sql
✅ SP_update_monthly_closing_approve_version  (version approval)
✅ SP_update_cheque_status_cleared            (status update)
❌ SP_update_monthly_closing                  (update what?)
❌ SP_update_cheque                          (update what field?)
```

### 6. **Group Related SPs**
```sql
-- Versioning group
SP_generate_monthly_closing_new_version
SP_update_monthly_closing_approve_version
SP_update_monthly_closing_rollback_version
SP_compare_monthly_closing_versions
SP_update_monthly_closing_archive_old_versions

-- All start with same prefix for easy discovery
```

---

## 🔍 Discovery & Documentation

### Use Prefix to Find Related SPs:

```sql
-- Find all balance sheet related SPs
SELECT name FROM sys.procedures 
WHERE name LIKE 'SP_%balance_sheet%'
ORDER BY name;

-- Find all UPDATE operations
SELECT name FROM sys.procedures 
WHERE name LIKE 'SP_update%'
ORDER BY name;

-- Find all monthly closing SPs
SELECT name FROM sys.procedures 
WHERE name LIKE 'SP_%monthly_closing%'
ORDER BY name;
```

### Add Extended Properties:

```sql
EXEC sp_addextendedproperty 
    @name = N'MS_Description',
    @value = N'Generate new version of monthly closing data with status DRAFT. 
              Creates snapshot of current month balances for review before approval.',
    @level0type = N'SCHEMA', @level0name = 'dbo',
    @level1type = N'PROCEDURE', @level1name = 'SP_generate_monthly_closing_new_version';
```

---

## 📊 Complete SP List (Current System)

### Balance Sheet & Closing:
```sql
-- Generate
SP_generate_balance_sheet_monthly
SP_generate_monthly_closing_new_version
SP_generate_yearly_closing
SP_generate_yearly_audit
SP_generate_total_audit

-- Update
SP_update_monthly_closing_approve_version
SP_update_monthly_closing_rollback_version
SP_update_monthly_closing_archive_old_versions

-- Calculate
SP_calculate_saldo_awal
SP_calculate_running_balance

-- Setup
SP_setup_initial_balance

-- Compare
SP_compare_monthly_closing_versions

-- Validate
SP_validate_closing_prerequisites
```

### Future SPs (To Be Created):
```sql
-- Yearly Closing
SP_generate_yearly_closing_new_version
SP_update_yearly_closing_approve_version
SP_compare_yearly_closing_versions

-- Yearly Audit
SP_generate_yearly_audit_new_version
SP_update_yearly_audit_approve_version
SP_calculate_audit_variance

-- Total Audit
SP_generate_total_audit_from_inception
SP_compare_total_audit_vs_yearly

-- Reporting
SP_generate_trial_balance
SP_generate_income_statement
SP_generate_cash_flow_statement

-- Utilities
SP_validate_transaction_integrity
SP_calculate_financial_ratios
SP_archive_old_transactions
```

---

## 🎓 Quick Reference Card

### When to use each ACTION:

| Scenario | Action | Example |
|----------|--------|---------|
| Create new closing data | **generate** | `SP_generate_monthly_closing_new_version` |
| Change status DRAFT→ACTIVE | **update** | `SP_update_monthly_closing_approve_version` |
| Revert to old version | **update** | `SP_update_monthly_closing_rollback_version` |
| Move to archive | **update** | `SP_update_monthly_closing_archive_old_versions` |
| Compare 2 versions | **compare** | `SP_compare_monthly_closing_versions` |
| Compute opening balance | **calculate** | `SP_calculate_saldo_awal` |
| First-time setup | **setup** | `SP_setup_initial_balance` |
| Check before closing | **validate** | `SP_validate_closing_prerequisites` |
| Simple data retrieval | **get** | `SP_get_coa_hierarchy` |

---

## ✨ Benefits of This Convention

1. ✅ **Self-Documenting**: Name tells you what it does
2. ✅ **Easy Discovery**: Use prefixes to find related SPs
3. ✅ **Consistent**: Same pattern everywhere
4. ✅ **Clear Intent**: Action verb makes purpose obvious
5. ✅ **Maintainable**: Easy to understand months later
6. ✅ **Team-Friendly**: New developers can quickly understand

---

**Last Updated**: 2024-01-09  
**Version**: 1.0  
**Author**: AccAdmin Development Team
