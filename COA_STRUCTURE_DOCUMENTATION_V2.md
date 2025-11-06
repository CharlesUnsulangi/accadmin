# Chart of Accounts (COA) - Flexible Hierarchy System (H1-H6)

**Project:** AccAdmin - Accounting Administration System  
**Database:** RCM_DEV_HGS_SB (MS SQL Server)  
**Last Updated:** November 6, 2025  
**Version:** 2.0 - Flexible Hierarchy

---

## 📊 System Overview

AccAdmin menyediakan **DUA INTERFACE** untuk mengelola COA, tetapi menggunakan **SATU TABEL** yang sama (`ms_acc_coa`):

### 🔄 Dua Halaman COA

| Halaman | Field yang Dipakai | Hierarchy | Target User |
|---------|-------------------|-----------|-------------|
| **COA Legacy** | `id_old_main`, `id_old_sub1`, `id_old_sub_2`<br>`main_desc`, `sub1_desc`, `sub2_desc` | 3 Level Fixed | User yang terbiasa sistem lama |
| **COA Modern** | `ms_coa_h1_id` ... `ms_coa_h6_id`<br>`desc_h1` ... `desc_h6` | 1-6 Level Flexible | User baru, sistem masa depan |

### ⚠️ PENTING: Same Table, Different Views

```
┌─────────────────────────────────────┐
│        ms_acc_coa (1 TABLE)         │
├─────────────────┬───────────────────┤
│  Legacy Fields  │   Modern Fields   │
│  (3 Level)      │   (H1-H6)         │
├─────────────────┼───────────────────┤
│ id_old_main     │ ms_coa_h1_id      │
│ main_desc       │ desc_h1           │
│ id_old_sub1     │ ms_coa_h2_id      │
│ sub1_desc       │ desc_h2           │
│ id_old_sub_2    │ ms_coa_h3_id      │
│ sub2_desc       │ desc_h3           │
│                 │ ms_coa_h4_id      │
│                 │ desc_h4           │
│                 │ ms_coa_h5_id      │
│                 │ desc_h5           │
│                 │ ms_coa_h6_id      │
│                 │ desc_h6           │
└─────────────────┴───────────────────┘
         ↓                 ↓
    COA Legacy        COA Modern
     Page              Page
```

### ✨ Keunggulan Sistem

- ✅ **Dual Interface**: Legacy user tetap nyaman, modern user dapat flexibility
- ✅ **Single Source**: Data tetap konsisten (1 tabel)
- ✅ **No Migration**: Tidak perlu migrasi data paksa
- ✅ **Gradual Adoption**: User bisa pindah bertahap ke sistem modern
- ✅ **Backward Compatible**: Data lama tetap bisa diakses

---

## 🏗️ Struktur Tabel `ms_acc_coa`

### Core Fields

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `coa_code` | varchar(50) | NO | **Primary Key** - Kode akun unik |
| `id` | int | NO | **Identity** - Auto-increment ID |
| `coa_id` | varchar(50) | NO | ID alternatif |
| `coa_desc` | varchar(50) | YES | Deskripsi akun |
| `coa_note` | varchar(50) | YES | Catatan tambahan |
| `arus_kas_code` | varchar(50) | YES | Kode cash flow category |
| `ms_acc_coa_h` | varchar(50) | YES | Header COA |

### Hierarchy Fields (H1-H6) - SISTEM AKTIF

Setiap level punya **3 field**:

| Level | ID String | ID Integer | Description |
|-------|-----------|------------|-------------|
| **H1** | `ms_coa_h1_id` (varchar) | `id_h1` (int) | `desc_h1` (varchar) |
| **H2** | `ms_coa_h2_id` (varchar) | `id_h2` (int) | `desc_h2` (varchar) |
| **H3** | `ms_coa_h3_id` (varchar) | `id_h3` (int) | `desc_h3` (varchar) |
| **H4** | `ms_coa_h4_id` (varchar) | `id_h4` (int) | `desc_h4` (varchar) |
| **H5** | `ms_coa_h5_id` (varchar) | `id_h5` (int) | `desc_h5` (varchar) |
| **H6** | `ms_coa_h6_id` (varchar) | `id_h6` (int) | `desc_h6` (varchar) |

**Total: 18 kolom untuk hierarki** (6 levels × 3 fields)

### Legacy Fields (Referensi Saja) - TIDAK AKTIF

| Field | Type | Description |
|-------|------|-------------|
| `coa_coasub2code` | varchar(50) | Legacy FK ke ms_acc_coasub2.coasub2_code |
| `id_old_sub_2` | varchar(50) | Legacy Sub2 ID |
| `id_old_sub1` | varchar(50) | Legacy Sub1 ID |
| `id_old_main` | varchar(50) | Legacy Main ID |
| `sub2_desc` | varchar(50) | Legacy Sub2 description |
| `sub1_desc` | varchar(50) | Legacy Sub1 description |
| `main_desc` | varchar(50) | Legacy Main description |

**Legacy Table Structure:**
```
ms_acc_coamain (coamain_code)
    ↓ FK: coasub1_coamaincode
ms_acc_coasub1 (coasub1_code)
    ↓ FK: coasub2_coasub1code
ms_acc_coasub2 (coasub2_code)
    ↓ FK: coa_coasub2code
ms_acc_coa (coa_code) ← TABEL AKTIF
```

### Audit Trail

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `rec_usercreated` | varchar(50) | NO | User pembuat |
| `rec_userupdate` | varchar(50) | NO | User yang update |
| `rec_datecreated` | datetime | NO | Timestamp dibuat |
| `rec_dateupdate` | datetime | NO | Timestamp update |
| `rec_status` | char(1) | NO | A=Active, D=Deleted, I=Inactive |

**Total Columns: 37**

---

## 🌳 Hierarki Flexible (H1-H6)

### Konsep Dasar

Sistem COA AccAdmin menggunakan **denormalized hierarchy** di mana setiap record COA menyimpan **seluruh path hierarki**-nya dalam 1 baris.

### Contoh Hierarki 4 Level

```
H1: ASSETS (1)
  ↓
H2: CURRENT ASSETS (11)
  ↓
H3: CASH & BANK (111)
  ↓
H4: Cash in Hand (1111)
```

**Data dalam tabel:**

| coa_code | ms_coa_h1_id | desc_h1 | ms_coa_h2_id | desc_h2 | ms_coa_h3_id | desc_h3 | ms_coa_h4_id | desc_h4 |
|----------|--------------|---------|--------------|---------|--------------|---------|--------------|---------|
| 1111001 | 1 | ASSETS | 11 | CURRENT ASSETS | 111 | CASH & BANK | 1111 | Cash in Hand |

### Contoh Hierarki 6 Level

```
H1: EXPENSES (5)
  ↓
H2: OPERATIONAL EXPENSES (51)
  ↓
H3: PERSONNEL EXPENSES (511)
  ↓
H4: SALARIES (5111)
  ↓
H5: PERMANENT STAFF (51111)
  ↓
H6: MANAGER SALARY (511111)
```

**Data dalam tabel:**

| coa_code | h1_id | desc_h1 | h2_id | desc_h2 | h3_id | desc_h3 | h4_id | desc_h4 | h5_id | desc_h5 | h6_id | desc_h6 |
|----------|-------|---------|-------|---------|-------|---------|-------|---------|-------|---------|-------|---------|
| 511111001 | 5 | EXPENSES | 51 | OPERATIONAL | 511 | PERSONNEL | 5111 | SALARIES | 51111 | PERMANENT | 511111 | MANAGER |

### Keuntungan Denormalized Hierarchy

1. **Query Cepat** - Tidak perlu JOIN banyak tabel
   ```sql
   -- Single query tanpa JOIN
   SELECT coa_code, desc_h1, desc_h2, desc_h3, desc_h4
   FROM ms_acc_coa
   WHERE coa_code = '1111001'
   ```

2. **Full Path Available** - Setiap record punya complete hierarchy path
   ```
   Path: ASSETS > CURRENT ASSETS > CASH & BANK > Cash in Hand
   ```

3. **Flexible Depth** - Bisa filter by level mana saja
   ```sql
   -- Semua akun di H2 = CURRENT ASSETS
   SELECT * FROM ms_acc_coa WHERE ms_coa_h2_id = '11'
   
   -- Semua akun yang punya sampai H5
   SELECT * FROM ms_acc_coa WHERE ms_coa_h5_id IS NOT NULL
   ```

4. **Easy Reporting** - Group by level mana saja
   ```sql
   -- Group by H1
   SELECT desc_h1, COUNT(*) FROM ms_acc_coa GROUP BY desc_h1
   ```

---

## 🎯 Penggunaan dalam Transaksi

### ⚠️ PENTING: Flexible Transaction Level

**SEMUA level COA (H1-H6) bisa digunakan untuk transaksi!**

Tidak seperti sistem hierarki tradisional yang hanya allow leaf node, sistem flexible ini memperbolehkan transaksi di level mana saja tergantung kebutuhan bisnis.

### Contoh Kasus

**Kasus 1: Transaksi di H2**
```php
// Posting langsung ke CURRENT ASSETS (H2)
Journal::create([
    'coa_code' => '11', // CURRENT ASSETS (H2)
    'debit' => 1000000,
]);
```

**Kasus 2: Transaksi di H4**
```php
// Posting detail ke Cash in Hand (H4)
Journal::create([
    'coa_code' => '1111', // Cash in Hand (H4)
    'debit' => 50000,
]);
```

**Kasus 3: Transaksi di H6**
```php
// Posting sangat detail ke Manager Salary (H6)
Journal::create([
    'coa_code' => '511111001', // Manager Salary (H6)
    'debit' => 15000000,
]);
```

### Business Logic

```php
// Cek apakah COA bisa dipakai
$coa = Coa::find('1111001');

if ($coa->canBeUsed()) {
    // Status = Active? ✅ Bisa dipakai!
    // Level berapa saja OK (H1, H2, H3, H4, H5, atau H6)
}

// Cek level hierarki yang digunakan
$level = $coa->getCurrentHierarchyLevel(); // Return: 1-6

// Cek apakah leaf node (tidak punya child)
if ($coa->isLeafNode()) {
    // Akun ini tidak punya child
    // Cocok untuk transaksi detail
}
```

---

## 🔗 Model Laravel

### Coa Model

```php
// Get COA dengan full hierarchy
$coa = Coa::find('1111001');

// Akses hierarchy fields (H1-H6 - ACTIVE SYSTEM)
echo $coa->desc_h1; // ASSETS
echo $coa->desc_h2; // CURRENT ASSETS
echo $coa->desc_h3; // CASH & BANK
echo $coa->desc_h4; // Cash in Hand

// Akses legacy fields (untuk referensi)
echo $coa->main_desc; // Legacy Main
echo $coa->sub1_desc; // Legacy Sub1
echo $coa->sub2_desc; // Legacy Sub2

// Get full hierarchy path
echo $coa->hierarchy_path;
// Output: "ASSETS > CURRENT ASSETS > CASH & BANK > Cash in Hand"

// Get account type (berdasarkan digit pertama)
echo $coa->account_type; // Asset, Liability, Equity, Revenue, Expense

// Get current level (1-6)
$level = $coa->getCurrentHierarchyLevel(); // 4

// Check if can be used
if ($coa->canBeUsed()) {
    // OK untuk transaksi
}

// Check if leaf node
if ($coa->isLeafNode()) {
    // Tidak punya child
}

// Access legacy relationship (reference only)
$coaSub2 = $coa->coaSub2; // ms_acc_coasub2
$coaSub1 = $coa->coaSub2->coaSub1; // ms_acc_coasub1
$coaMain = $coa->coaSub2->coaSub1->coaMain; // ms_acc_coamain
```

### Legacy Models (Reference Only)

```php
// CoaMain (Level 1 - Legacy)
$main = CoaMain::find('1'); // ASSETS
$sub1s = $main->coaSub1s; // Children

// CoaSub1 (Level 2 - Legacy)
$sub1 = CoaSub1::find('11'); // CURRENT ASSETS
$parent = $sub1->coaMain; // Parent
$sub2s = $sub1->coaSub2s; // Children

// CoaSub2 (Level 3 - Legacy)
$sub2 = CoaSub2::find('111'); // CASH & BANK
$parent = $sub2->coaSub1; // Parent
$coas = $sub2->coas; // Children (ms_acc_coa)
```
```

### Query Examples

```php
// Filter by hierarchy level
$coas = Coa::byHierarchyLevel(1, '1')->get(); // Semua ASSETS
$coas = Coa::byHierarchyLevel(2, '11')->get(); // Semua CURRENT ASSETS

// Get COA yang punya hierarchy sampai level tertentu
$coas = Coa::hasHierarchyLevel(4)->get(); // Yang punya sampai H4
$coas = Coa::hasHierarchyLevel(6)->get(); // Yang punya sampai H6

// Active accounts only
$coas = Coa::active()->get();

// Search
$coas = Coa::search('cash')->get();

// Combine filters
$coas = Coa::active()
           ->byHierarchyLevel(1, '1')
           ->hasHierarchyLevel(4)
           ->get();
```

---

## 📋 Tabel Legacy (Referensi Saja)

Tabel-tabel ini masih ada di database untuk **backward compatibility** dan **referensi historis**, tetapi **TIDAK digunakan** dalam operasional sistem baru.

### 1. `ms_acc_coa_main` (Legacy Level 1)
- **Status:** 📦 Reference Only
- **Records:** 10
- **Pengganti:** Field `ms_coa_h1_id`, `desc_h1` di `ms_acc_coa`

### 2. `ms_acc_coa_main_sub1` (Legacy Level 2)
- **Status:** 📦 Reference Only
- **Records:** 9
- **Pengganti:** Field `ms_coa_h2_id`, `desc_h2` di `ms_acc_coa`

### 3. `ms_acc_coa_main_sub2` (Legacy Level 3)
- **Status:** 📦 Reference Only
- **Records:** 9
- **Pengganti:** Field `ms_coa_h3_id`, `desc_h3` di `ms_acc_coa`

**⚠️ PENTING:** 
- Jangan menambah data baru ke tabel legacy
- Jangan delete tabel legacy (untuk audit trail)
- Gunakan **ms_acc_coa** dengan H1-H6 untuk semua operasi baru

---

## 📊 Contoh Reports

### Trial Balance (By H1)

```sql
SELECT 
    desc_h1 AS Category,
    SUM(CASE WHEN account_type IN ('Asset','Expense') THEN amount ELSE 0 END) AS Debit,
    SUM(CASE WHEN account_type IN ('Liability','Equity','Revenue') THEN amount ELSE 0 END) AS Credit
FROM ms_acc_coa c
JOIN journal_entries j ON c.coa_code = j.coa_code
GROUP BY desc_h1
```

### Hierarchy Breakdown (All Levels)

```sql
SELECT 
    desc_h1 AS L1,
    desc_h2 AS L2,
    desc_h3 AS L3,
    desc_h4 AS L4,
    desc_h5 AS L5,
    desc_h6 AS L6,
    coa_code,
    coa_desc,
    SUM(amount) AS Total
FROM ms_acc_coa c
JOIN journal_entries j ON c.coa_code = j.coa_code
GROUP BY desc_h1, desc_h2, desc_h3, desc_h4, desc_h5, desc_h6, coa_code, coa_desc
ORDER BY desc_h1, desc_h2, desc_h3, desc_h4, desc_h5, desc_h6
```

### Balance Sheet (Nested Hierarchy)

```php
// Laravel Example
$assets = Coa::where('ms_coa_h1_id', '1') // ASSETS
             ->with('transactions')
             ->get()
             ->groupBy('desc_h2') // Group by H2
             ->map(function($group) {
                 return $group->groupBy('desc_h3') // Nested by H3
                              ->map(function($subgroup) {
                                  return $subgroup->sum('balance');
                              });
             });
```

---

## ⚙️ Best Practices

### DO ✅

1. **Gunakan H1-H6 untuk sistem baru**
   ```php
   // ✅ BENAR
   $coa->ms_coa_h1_id = '1';
   $coa->desc_h1 = 'ASSETS';
   ```

2. **Isi hierarki dari H1 ke bawah secara berurutan**
   ```php
   // ✅ BENAR - Sequential
   $coa->ms_coa_h1_id = '1';
   $coa->ms_coa_h2_id = '11';
   $coa->ms_coa_h3_id = '111';
   $coa->ms_coa_h4_id = '1111';
   ```

3. **Soft delete dengan rec_status**
   ```php
   // ✅ BENAR
   $coa->update(['rec_status' => 'D']);
   ```

4. **Query tanpa JOIN**
   ```php
   // ✅ BENAR - Fast query
   $coas = Coa::where('desc_h1', 'ASSETS')->get();
   ```

### DON'T ❌

1. **Jangan skip level hierarki**
   ```php
   // ❌ SALAH - Skip H2
   $coa->ms_coa_h1_id = '1';
   $coa->ms_coa_h3_id = '111'; // H2 kosong!
   ```

2. **Jangan gunakan tabel legacy untuk data baru**
   ```sql
   -- ❌ SALAH
   INSERT INTO ms_acc_coa_main VALUES (...) -- Legacy table!
   
   -- ✅ BENAR
   INSERT INTO ms_acc_coa (ms_coa_h1_id, desc_h1, ...) VALUES (...)
   ```

3. **Jangan hard delete**
   ```php
   // ❌ SALAH
   $coa->delete(); // Hard delete!
   
   // ✅ BENAR
   $coa->update(['rec_status' => 'D']); // Soft delete
   ```

4. **Jangan TRUNCATE atau DROP**
   ```sql
   -- ❌ SANGAT BERBAHAYA
   TRUNCATE TABLE ms_acc_coa;
   DROP TABLE ms_acc_coa;
   ```

---

## 🔒 Security Rules

Mengikuti **AI_DEVELOPMENT_GUIDELINES.md**:

- ❌ **TIDAK BOLEH** DROP database/table
- ❌ **TIDAK BOLEH** TRUNCATE table
- ❌ **TIDAK BOLEH** DELETE tanpa WHERE
- ❌ **TIDAK BOLEH** ALTER TABLE tanpa backup
- ✅ **BOLEH** Soft delete (rec_status = 'D')
- ✅ **BOLEH** SELECT/INSERT/UPDATE dengan WHERE
- ✅ **BOLEH** Backup sebelum perubahan struktur

---

## 📈 Migration dari Sistem Lama

Jika masih ada data di tabel legacy:

```sql
-- Sync data dari legacy ke H1-H3
UPDATE ms_acc_coa 
SET 
    ms_coa_h1_id = id_old_main,
    desc_h1 = main_desc,
    ms_coa_h2_id = id_old_sub1,
    desc_h2 = sub1_desc,
    ms_coa_h3_id = id_old_sub_2,
    desc_h3 = sub2_desc
WHERE 
    ms_coa_h1_id IS NULL 
    AND id_old_main IS NOT NULL;
```

---

## 📞 Summary

| Aspect | Details |
|--------|---------|
| **Sistem Aktif** | H1-H6 (18 fields untuk hierarki) |
| **Sistem Legacy** | CoaMain, CoaSub1, CoaSub2 (referensi saja) |
| **Tabel Utama** | `ms_acc_coa` (1 tabel, 37 kolom) |
| **Flexible Depth** | 1-6 levels sesuai kebutuhan |
| **Transaction Level** | Bisa di level mana saja (H1-H6) |
| **Performance** | Fast (no complex joins) |
| **Total Records** | 501+ accounts |

---

**Version:** 2.0 - Flexible Hierarchy System  
**Last Updated:** November 6, 2025  
**Documentation:** COA_STRUCTURE_DOCUMENTATION_V2.md
