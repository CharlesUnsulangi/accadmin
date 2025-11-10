# 🔄 Versioning System Design for Multi-Layer Closing

## 📋 Overview

**Problem**: User ingin data yang sudah di-generate sebelumnya **tidak hilang** jika perlu regenerate.

**Solution**: Implementasi **Version Control System** untuk semua closing tables.

### Key Benefits:
✅ **Complete Audit Trail** - Semua perubahan tercatat  
✅ **Rollback Capability** - Bisa kembali ke version sebelumnya  
✅ **Compare Versions** - Bandingkan perbedaan antar version  
✅ **What-If Analysis** - Test regenerate tanpa hapus data lama  
✅ **Regulatory Compliance** - Memenuhi requirement audit eksternal  

---

## 🎯 Version Lifecycle

```
┌─────────┐
│  DRAFT  │ ← Initial generation (review & verification)
└────┬────┘
     │ approve()
     ↓
┌─────────┐
│ ACTIVE  │ ← Currently used version (only 1 ACTIVE per periode+COA)
└────┬────┘
     │ regenerate() / new version created
     ↓
┌────────────┐
│ SUPERSEDED │ ← Old version (replaced by newer ACTIVE)
└─────┬──────┘
      │ after retention period (optional)
      ↓
┌──────────┐
│ ARCHIVED │ ← Moved to archive table (optional cleanup)
└──────────┘
```

### Status Definitions:

| Status | Description | Can Modify? | Can Delete? | Show in Reports? |
|--------|-------------|-------------|-------------|------------------|
| **DRAFT** | Just generated, under review | ✅ Yes | ✅ Yes | ❌ No |
| **ACTIVE** | Currently in use | ❌ No (locked) | ❌ No | ✅ Yes (default) |
| **SUPERSEDED** | Replaced by newer version | ❌ No | ⚠️ Soft delete only | ⚠️ History only |
| **ARCHIVED** | Old data moved to archive | ❌ No | ⚠️ By admin only | ❌ No |

---

## 🗂️ Version Schema Fields

### Common Fields for All Closing Tables:

```sql
-- Version Control
version_number INT NOT NULL DEFAULT 1,         -- 1, 2, 3, 4...
version_status VARCHAR(20) NOT NULL,           -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
version_note NVARCHAR(500),                    -- Reason: "Koreksi data Feb", "Audit adjustment"

-- Version Metadata
created_at DATETIME DEFAULT GETDATE(),         -- When this version was created
created_by VARCHAR(50),                        -- Who created this version

-- Supersede Tracking
superseded_at DATETIME,                        -- When this version was replaced
superseded_by VARCHAR(50),                     -- Who created the new version
superseded_by_version INT,                     -- New version number that replaced this

-- Approval (for DRAFT → ACTIVE)
approved_at DATETIME,                          -- When approved
approved_by VARCHAR(50),                       -- Who approved

-- Lock (ACTIVE versions cannot be modified)
is_closed BIT DEFAULT 0,                       -- Closed = locked forever
closed_at DATETIME,
closed_by VARCHAR(50)
```

---

## 📊 Unique Constraints with Versioning

### Before (WITHOUT versioning):
```sql
-- Old: Only 1 row per periode + COA
CONSTRAINT UQ_Monthly_Closing UNIQUE (closing_periode_id, coa_code)
```

### After (WITH versioning):
```sql
-- New: Multiple versions allowed, but unique per version number
CONSTRAINT UQ_Monthly_Closing_Version UNIQUE (closing_periode_id, coa_code, version_number)

-- Index untuk performance (filter ACTIVE only)
INDEX IX_Monthly_Active (closing_periode_id, coa_code, version_status) 
    WHERE version_status = 'ACTIVE'
```

---

## ⚙️ Stored Procedures

### 1️⃣ Generate New Version (Create DRAFT)

```sql
CREATE PROCEDURE SP_generate_monthly_closing_new_version
    @year INT,
    @month INT,
    @note NVARCHAR(500) = NULL,
    @created_by VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @periode_id VARCHAR(6) = FORMAT(@year, '0000') + FORMAT(@month, '00');
    DECLARE @next_version INT;
    
    -- Get next version number
    SELECT @next_version = ISNULL(MAX(version_number), 0) + 1
    FROM tr_acc_monthly_closing
    WHERE closing_periode_id = @periode_id;
    
    -- Generate new version with status DRAFT
    INSERT INTO tr_acc_monthly_closing (
        version_number,
        version_status,
        version_note,
        closing_year,
        closing_month,
        closing_periode_id,
        coa_code,
        coa_desc,
        -- ... other fields
        created_at,
        created_by
    )
    SELECT 
        @next_version AS version_number,
        'DRAFT' AS version_status,
        @note AS version_note,
        @year,
        @month,
        @periode_id,
        c.coa_code,
        c.coa_desc,
        -- ... calculate balances
        GETDATE(),
        @created_by
    FROM ms_acc_coa c
    WHERE c.is_active = 1;
    
    -- Return version info
    SELECT 
        @periode_id AS periode_id,
        @next_version AS version_number,
        'DRAFT' AS status,
        COUNT(*) AS total_rows
    FROM tr_acc_monthly_closing
    WHERE closing_periode_id = @periode_id
      AND version_number = @next_version;
END;
GO
```

### 2️⃣ Approve Version (DRAFT → ACTIVE)

```sql
CREATE PROCEDURE SP_update_monthly_closing_approve_version
    @periode_id VARCHAR(6),
    @version_number INT,
    @approved_by VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRANSACTION;
    
    -- Verify version exists and is DRAFT
    IF NOT EXISTS (
        SELECT 1 FROM tr_acc_monthly_closing
        WHERE closing_periode_id = @periode_id
          AND version_number = @version_number
          AND version_status = 'DRAFT'
    )
    BEGIN
        ROLLBACK;
        THROW 50001, 'Version not found or not in DRAFT status', 1;
    END;
    
    -- Supersede current ACTIVE version (if exists)
    UPDATE tr_acc_monthly_closing
    SET version_status = 'SUPERSEDED',
        superseded_at = GETDATE(),
        superseded_by = @approved_by,
        superseded_by_version = @version_number
    WHERE closing_periode_id = @periode_id
      AND version_status = 'ACTIVE';
    
    -- Activate new version
    UPDATE tr_acc_monthly_closing
    SET version_status = 'ACTIVE',
        approved_at = GETDATE(),
        approved_by = @approved_by
    WHERE closing_periode_id = @periode_id
      AND version_number = @version_number;
    
    COMMIT;
    
    -- Return result
    SELECT 
        @periode_id AS periode_id,
        @version_number AS version_number,
        'ACTIVE' AS new_status,
        COUNT(*) AS total_rows
    FROM tr_acc_monthly_closing
    WHERE closing_periode_id = @periode_id
      AND version_number = @version_number;
END;
GO
```

### 3️⃣ Rollback to Previous Version

```sql
CREATE PROCEDURE SP_update_monthly_closing_rollback_version
    @periode_id VARCHAR(6),
    @target_version INT,     -- Version to activate
    @rollback_by VARCHAR(50),
    @reason NVARCHAR(500)
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRANSACTION;
    
    -- Verify target version exists and is SUPERSEDED
    IF NOT EXISTS (
        SELECT 1 FROM tr_acc_monthly_closing
        WHERE closing_periode_id = @periode_id
          AND version_number = @target_version
          AND version_status = 'SUPERSEDED'
    )
    BEGIN
        ROLLBACK;
        THROW 50002, 'Target version not found or not SUPERSEDED', 1;
    END;
    
    -- Supersede current ACTIVE
    UPDATE tr_acc_monthly_closing
    SET version_status = 'SUPERSEDED',
        superseded_at = GETDATE(),
        superseded_by = @rollback_by,
        version_note = ISNULL(version_note + ' | ', '') + 
                      'Superseded by rollback: ' + @reason
    WHERE closing_periode_id = @periode_id
      AND version_status = 'ACTIVE';
    
    -- Reactivate target version
    UPDATE tr_acc_monthly_closing
    SET version_status = 'ACTIVE',
        approved_at = GETDATE(),
        approved_by = @rollback_by,
        version_note = ISNULL(version_note + ' | ', '') + 
                      'Rollback activated: ' + @reason
    WHERE closing_periode_id = @periode_id
      AND version_number = @target_version;
    
    COMMIT;
    
    SELECT 'Rollback successful' AS message,
           @periode_id AS periode_id,
           @target_version AS activated_version;
END;
GO
```

### 4️⃣ Compare Versions

```sql
CREATE PROCEDURE SP_compare_monthly_closing_versions
    @periode_id VARCHAR(6),
    @version1 INT,
    @version2 INT
AS
BEGIN
    SET NOCOUNT ON;
    
    SELECT 
        v1.coa_code,
        v1.coa_desc,
        
        -- Version 1
        v1.version_number AS v1_number,
        v1.version_status AS v1_status,
        v1.closing_balance AS v1_balance,
        
        -- Version 2
        v2.version_number AS v2_number,
        v2.version_status AS v2_status,
        v2.closing_balance AS v2_balance,
        
        -- Difference
        (v2.closing_balance - v1.closing_balance) AS balance_diff,
        CASE 
            WHEN v2.closing_balance = v1.closing_balance THEN 'SAME'
            WHEN ABS(v2.closing_balance - v1.closing_balance) < 0.01 THEN 'MINOR'
            ELSE 'DIFFERENT'
        END AS diff_status
        
    FROM tr_acc_monthly_closing v1
    FULL OUTER JOIN tr_acc_monthly_closing v2
        ON v1.closing_periode_id = v2.closing_periode_id
       AND v1.coa_code = v2.coa_code
       AND v2.version_number = @version2
    WHERE v1.closing_periode_id = @periode_id
      AND v1.version_number = @version1
    ORDER BY v1.coa_code;
END;
GO
```

### 5️⃣ Archive Old Versions

```sql
CREATE PROCEDURE SP_update_monthly_closing_archive_old_versions
    @periode_id VARCHAR(6),
    @keep_last_n_versions INT = 5  -- Keep 5 most recent
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Mark old SUPERSEDED versions as ARCHIVED
    UPDATE tr_acc_monthly_closing
    SET version_status = 'ARCHIVED'
    WHERE closing_periode_id = @periode_id
      AND version_status = 'SUPERSEDED'
      AND version_number NOT IN (
          SELECT TOP (@keep_last_n_versions) version_number
          FROM tr_acc_monthly_closing
          WHERE closing_periode_id = @periode_id
          ORDER BY version_number DESC
      );
    
    SELECT @@ROWCOUNT AS rows_archived;
END;
GO
```

---

## 🔍 Query Patterns

### Get Current ACTIVE Version:
```sql
-- Always filter by ACTIVE status
SELECT * 
FROM tr_acc_monthly_closing
WHERE closing_periode_id = '202401'
  AND version_status = 'ACTIVE';
```

### Get Version History:
```sql
SELECT 
    version_number,
    version_status,
    version_note,
    created_at,
    created_by,
    approved_at,
    approved_by,
    COUNT(*) AS row_count,
    SUM(closing_balance) AS total_balance
FROM tr_acc_monthly_closing
WHERE closing_periode_id = '202401'
GROUP BY 
    version_number,
    version_status,
    version_note,
    created_at,
    created_by,
    approved_at,
    approved_by
ORDER BY version_number DESC;
```

### Get All Versions of Specific COA:
```sql
SELECT 
    version_number,
    version_status,
    closing_balance,
    created_at,
    created_by
FROM tr_acc_monthly_closing
WHERE closing_periode_id = '202401'
  AND coa_code = '1-1001'
ORDER BY version_number;
```

---

## 🎨 Laravel Model Integration

### Eloquent Scopes:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyClosing extends Model
{
    protected $table = 'tr_acc_monthly_closing';
    
    protected $fillable = [
        'version_number', 'version_status', 'version_note',
        'closing_year', 'closing_month', 'closing_periode_id',
        'coa_code', 'coa_desc',
        // ... other fields
    ];
    
    protected $casts = [
        'closing_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_closed' => 'boolean',
    ];
    
    // ===== SCOPES =====
    
    /**
     * Get only ACTIVE versions
     */
    public function scopeActive($query)
    {
        return $query->where('version_status', 'ACTIVE');
    }
    
    /**
     * Get specific version
     */
    public function scopeVersion($query, $versionNumber)
    {
        return $query->where('version_number', $versionNumber);
    }
    
    /**
     * Get periode
     */
    public function scopePeriode($query, $year, $month = null)
    {
        $query->where('closing_year', $year);
        if ($month) {
            $query->where('closing_month', $month);
        }
        return $query;
    }
    
    /**
     * Get version history for periode
     */
    public function scopeVersionHistory($query, $periodeId)
    {
        return $query->where('closing_periode_id', $periodeId)
                    ->orderBy('version_number', 'desc');
    }
    
    // ===== HELPER METHODS =====
    
    /**
     * Check if this version can be modified
     */
    public function canModify(): bool
    {
        return $this->version_status === 'DRAFT' && !$this->is_closed;
    }
    
    /**
     * Check if this is the active version
     */
    public function isActive(): bool
    {
        return $this->version_status === 'ACTIVE';
    }
    
    /**
     * Get previous version
     */
    public function previousVersion()
    {
        return self::where('closing_periode_id', $this->closing_periode_id)
                  ->where('coa_code', $this->coa_code)
                  ->where('version_number', '<', $this->version_number)
                  ->orderBy('version_number', 'desc')
                  ->first();
    }
    
    /**
     * Get next version
     */
    public function nextVersion()
    {
        return self::where('closing_periode_id', $this->closing_periode_id)
                  ->where('coa_code', $this->coa_code)
                  ->where('version_number', '>', $this->version_number)
                  ->orderBy('version_number')
                  ->first();
    }
}
```

---

## 🎭 UI/UX Considerations

### Version Selector Component:

```html
<!-- Version History Dropdown -->
<div class="version-selector">
    <label>Version History:</label>
    <select wire:model="selectedVersion">
        <option value="active">Current (ACTIVE)</option>
        @foreach($versionHistory as $v)
            <option value="{{ $v->version_number }}">
                v{{ $v->version_number }} 
                - {{ $v->version_status }}
                - {{ $v->created_at->format('Y-m-d H:i') }}
                @if($v->version_note)
                    - {{ Str::limit($v->version_note, 30) }}
                @endif
            </option>
        @endforeach
    </select>
    
    @if($selectedVersion !== 'active')
        <button wire:click="compareWithActive" class="btn-compare">
            Compare with Current
        </button>
        
        @if($canRollback)
            <button wire:click="rollbackToVersion" class="btn-rollback">
                Rollback to This Version
            </button>
        @endif
    @endif
</div>
```

### Version Comparison View:

```html
<!-- Side-by-Side Comparison -->
<div class="version-compare">
    <div class="version-col">
        <h3>Version {{ $version1 }} (OLD)</h3>
        <!-- Data table -->
    </div>
    
    <div class="diff-col">
        <h3>Changes</h3>
        <!-- Diff indicators -->
    </div>
    
    <div class="version-col">
        <h3>Version {{ $version2 }} (NEW)</h3>
        <!-- Data table -->
    </div>
</div>
```

---

## 📝 Workflow Examples

### Scenario 1: Generate New Monthly Closing

```sql
-- 1. Generate version 1 (DRAFT)
EXEC SP_generate_monthly_closing_new_version 
    @year = 2024, 
    @month = 1, 
    @note = 'Initial monthly closing Jan 2024',
    @created_by = 'admin';

-- 2. Review & approve
EXEC SP_update_monthly_closing_approve_version 
    @periode_id = '202401',
    @version_number = 1,
    @approved_by = 'supervisor';

-- Now version 1 is ACTIVE
```

### Scenario 2: Found Error, Need to Regenerate

```sql
-- 3. Generate version 2 (DRAFT) with corrections
EXEC SP_generate_monthly_closing_new_version 
    @year = 2024, 
    @month = 1, 
    @note = 'Koreksi: Ada transaksi yang terlewat',
    @created_by = 'admin';

-- 4. Compare versions
EXEC SP_compare_monthly_closing_versions
    @periode_id = '202401',
    @version1 = 1,  -- old
    @version2 = 2;  -- new

-- 5. Approve new version
EXEC SP_update_monthly_closing_approve_version 
    @periode_id = '202401',
    @version_number = 2,
    @approved_by = 'supervisor';

-- Result:
-- Version 1 → SUPERSEDED (history preserved!)
-- Version 2 → ACTIVE (current)
```

### Scenario 3: Rollback (Version 2 was wrong)

```sql
-- 6. Rollback to version 1
EXEC SP_update_monthly_closing_rollback_version
    @periode_id = '202401',
    @target_version = 1,
    @rollback_by = 'supervisor',
    @reason = 'Version 2 calculation error detected';

-- Result:
-- Version 1 → ACTIVE (reactivated)
-- Version 2 → SUPERSEDED
```

---

## 🎯 Benefits Summary

| Feature | Without Versioning | With Versioning |
|---------|-------------------|-----------------|
| **Data Preservation** | ❌ Old data deleted on regenerate | ✅ All versions kept |
| **Audit Trail** | ⚠️ Limited (only current state) | ✅ Complete history |
| **Rollback** | ❌ Manual restore from backup | ✅ One-click rollback |
| **Compare** | ❌ Not possible | ✅ Built-in comparison |
| **What-If** | ❌ Risky (destroys data) | ✅ Safe testing |
| **Compliance** | ⚠️ May not meet regulations | ✅ Meets audit requirements |

---

## 🚀 Implementation Checklist

- [x] Design version schema
- [x] Design version lifecycle
- [x] Create SPs for version management
- [ ] Update all 4 closing tables with version fields
- [ ] Create migration scripts
- [ ] Update Laravel models with version scopes
- [ ] Create UI components for version history
- [ ] Add version comparison views
- [ ] Test rollback functionality
- [ ] Document user guide
- [ ] Train users on versioning workflow

---

**Next Steps**: 
1. Update schema SQL scripts untuk semua 4 tables
2. Implement version management SPs
3. Create Laravel models dengan version scopes
4. Build UI untuk version history & comparison
