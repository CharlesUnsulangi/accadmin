# Chart of Accounts (COA) - Struktur Hierarki 4 Level Legacy + Flexible (H1-H6)

## 📊 Overview

Sistem AccAdmin memiliki **DUA SISTEM COA** yang berjalan paralel:

### 🔄 Dua Sistem COA

**1. Sistem LEGACY (4 Level - Tabel Terpisah)** 📦
- Struktur **4 Level** menggunakan **4 tabel terpisah**
- **Level 1:** `ms_acc_coa_main` - Main Category (10 records)
- **Level 2:** `ms_acc_coa_main_sub1` - Sub Category 1 (9 records)
- **Level 3:** `ms_acc_coa_main_sub2` - Sub Category 2 (9 records)
- **Level 4:** `ms_acc_coa` - Detail COA Accounts (501+ records) ✅
- Relationship: Level 4 (`ms_acc_coa`) sambung ke Level 3 via `coa_coasub2code` → `coa_main2_code`
- **MASIH AKTIF** untuk user yang terbiasa dengan sistem lama

**2. Sistem MODERN (Flexible H1-H6)** ✅
- Digunakan untuk operasional modern
- Flexible: bisa 1-6 level sesuai kebutuhan dalam **1 tabel** (`ms_acc_coa`)
- Field: `ms_coa_h1_id` s/d `ms_coa_h6_id`, `desc_h1` s/d `desc_h6`, `id_h1` s/d `id_h6`
- Setiap COA punya 3 field per level: ID string, ID integer, Deskripsi
- Record yang sama di `ms_acc_coa` bisa diakses dengan **2 cara** (Legacy 4-level atau Modern H1-H6)

---

## 🏗️ Struktur Database - 4 Level Legacy

### Level 4: `ms_acc_coa` - Chart of Accounts (Detail Accounts) ✅ AKTIF

**Purpose:** Tabel yang menyimpan **DETAIL AKUN COA** (Level 4) yang tersambung ke Level 3 (`ms_acc_main_sub2`)

**Total Records:** 501+ accounts

**Total Columns:** 37 kolom

### Level 1: `ms_acc_coa_main` (COA Main) 📦 LEGACY
**Tabel induk tertinggi** - Kategori utama akun (Level 1 dari 4)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `coa_main_code` | varchar(50) | **Primary Key** - Kode kategori utama |
| `coa_main_id` | varchar(50) | ID alternatif |
| `coa_main_desc` | varchar(50) | Deskripsi kategori utama |
| `coa_main_coamain2code` | varchar(50) | Field tambahan |
| `rec_status` | char(1) | Status: A=Active, I=Inactive, D=Deleted |
| `rec_usercreated` | varchar(50) | User yang membuat record |
| `rec_datecreated` | datetime | Tanggal pembuatan |
| `rec_userupdate` | varchar(50) | User yang terakhir update |
| `rec_dateupdate` | datetime | Tanggal terakhir update |

**Contoh Data:**
```
coa_main_code | coa_main_desc
1             | ASSETS (Aset/Harta)
2             | LIABILITIES (Kewajiban/Utang)
3             | EQUITY (Modal)
4             | REVENUE (Pendapatan)
5             | EXPENSES (Biaya/Beban)
```

**Total Records:** 10 kategori utama

**Relationship:** 
- Level 1 → Has Many → Level 2 (`ms_acc_coa_main_sub1`)

---

### Level 2: `ms_acc_coa_main_sub1` (COA Sub1) 📦 LEGACY
**Sub-kategori pertama** - Pengelompokan di bawah COA Main (Level 2 dari 4)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `coa_main1_code` | varchar(50) | **Primary Key & FK** - Kode sub-kategori 1 |
| `coa_main1_id` | varchar(50) | ID alternatif |
| `coa_main1_desc` | varchar(50) | Deskripsi sub-kategori 1 |
| `rec_status` | char(1) | Status record |
| `rec_usercreated` | varchar(50) | User pembuat |
| `rec_datecreated` | datetime | Tanggal dibuat |
| `rec_userupdate` | varchar(50) | User update |
| `rec_dateupdate` | datetime | Tanggal update |

**Contoh Data:**
```
coa_main1_code | coa_main1_desc              | Parent (coa_main_code)
11             | CURRENT ASSETS              | 1 (ASSETS)
12             | FIXED ASSETS                | 1 (ASSETS)
21             | CURRENT LIABILITIES         | 2 (LIABILITIES)
22             | LONG TERM LIABILITIES       | 2 (LIABILITIES)
31             | OWNER'S EQUITY              | 3 (EQUITY)
```

**Relationship Logic:**
- `coa_main1_code` adalah **PK sekaligus FK** ke parent
- Prefix matching: karakter pertama dari `coa_main1_code` = `coa_main_code`
- Contoh: `11` (CURRENT ASSETS) → parent `1` (ASSETS)
- Contoh: `21` (CURRENT LIABILITIES) → parent `2` (LIABILITIES)

**Total Records:** 9 sub-kategori

**Relationship:** 
- Level 2 → Has Many → Level 3 (`ms_acc_coa_main_sub2`)

---

### Level 3: `ms_acc_coa_main_sub2` (COA Sub2) 📦 LEGACY
**Sub-kategori kedua** - Pengelompokan lebih detail (Level 3 dari 4)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `coa_main2_code` | varchar(50) | **Primary Key & FK** - Kode sub-kategori 2 |
| `coa_main2_id` | varchar(50) | ID alternatif |
| `coa_main2_desc` | varchar(50) | Deskripsi sub-kategori 2 |
| `rec_status` | char(1) | Status record |
| `rec_usercreated` | varchar(50) | User pembuat |
| `rec_datecreated` | datetime | Tanggal dibuat |
| `rec_userupdate` | varchar(50) | User update |
| `rec_dateupdate` | datetime | Tanggal update |

**Contoh Data:**
```
111 - CASH & BANK (Current Assets → Kas dan Bank)
112 - ACCOUNTS RECEIVABLE (Current Assets → Piutang)
113 - INVENTORY (Current Assets → Persediaan)
121 - LAND & BUILDING (Fixed Assets → Tanah dan Bangunan)
122 - VEHICLES (Fixed Assets → Kendaraan)
```

**Relationship Logic:**
- `coa_main2_code` adalah **PK sekaligus FK** ke parent
- Prefix matching: 2 karakter pertama dari `coa_main2_code` = `coa_main1_code`
- Contoh: `111` (CASH & BANK) → parent `11` (CURRENT ASSETS)
- Contoh: `121` (LAND & BUILDING) → parent `12` (FIXED ASSETS)

**Total Records:** 9 sub-kategori detail

**Relationship:** 
- Level 3 → Has Many → Level 4 (`ms_acc_coa` via `coa_coasub2code`)

---

### Level 4: `ms_acc_coa` (COA Detail) ⭐ AKTIF
**Akun detail** - Akun yang digunakan dalam transaksi (Level 4 dari 4)

**Purpose:** Tabel utama yang menyimpan SEMUA akun COA detail. Tabel ini adalah **INTI** dari sistem accounting, yang tersambung ke Level 3 (`ms_acc_main_sub2`) untuk hierarchy legacy, DAN juga memiliki field H1-H6 untuk hierarchy modern flexible.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `coa_code` | varchar(50) | **Primary Key** - Kode akun detail |
| `id` | int | Auto-increment ID |
| `coa_id` | varchar(50) | ID akun alternatif |
| `coa_coasub2code` | varchar(50) | **Foreign Key** → `ms_acc_main_sub2.coa_main2_code` (Legacy Link) |
| `coa_desc` | varchar(255) | Deskripsi akun |
| `coa_note` | text | Catatan tambahan (optional) |
| `arus_kas_code` | varchar(50) | Kode arus kas (optional) |
| `ms_acc_coa_h` | varchar(50) | Header COA |
| **MODERN H1-H6** | | **Flexible Hierarchy (1-6 levels)** |
| `ms_coa_h1_id` | varchar(50) | Level 1 ID (string) |
| `id_h1` | int | Level 1 ID (integer) |
| `desc_h1` | varchar(255) | Level 1 Description |
| `ms_coa_h2_id` | varchar(50) | Level 2 ID (string) |
| `id_h2` | int | Level 2 ID (integer) |
| `desc_h2` | varchar(255) | Level 2 Description |
| `ms_coa_h3_id` | varchar(50) | Level 3 ID (string) |
| `id_h3` | int | Level 3 ID (integer) |
| `desc_h3` | varchar(255) | Level 3 Description |
| `ms_coa_h4_id` | varchar(50) | Level 4 ID (string) |
| `id_h4` | int | Level 4 ID (integer) |
| `desc_h4` | varchar(255) | Level 4 Description |
| `ms_coa_h5_id` | varchar(50) | Level 5 ID (string) |
| `id_h5` | int | Level 5 ID (integer) |
| `desc_h5` | varchar(255) | Level 5 Description |
| `ms_coa_h6_id` | varchar(50) | Level 6 ID (string) |
| `id_h6` | int | Level 6 ID (integer) |
| `desc_h6` | varchar(255) | Level 6 Description |
| `rec_status` | char(1) | Status: A=Active, I=Inactive, D=Deleted |
| `rec_usercreated` | varchar(50) | User pembuat |
| `rec_datecreated` | datetime | Tanggal dibuat |
| `rec_userupdate` | varchar(50) | User update |
| `rec_dateupdate` | datetime | Tanggal update |
| `rec_dateupdate` | datetime | Tanggal update |

**Contoh Data:**
```
1111001 - Kas Kecil (Cash & Bank → Kas untuk operasional harian)
1111002 - Bank BCA (Cash & Bank → Rekening Bank BCA)
1111003 - Bank Mandiri (Cash & Bank → Rekening Bank Mandiri)
1121001 - Piutang Dagang (Accounts Receivable → Piutang dari penjualan)
1131001 - Persediaan Barang Jadi (Inventory → Stok barang siap jual)
```

**Total Records:** 501 akun detail

> **⚠️ Ini adalah level yang digunakan untuk transaksi (Journal Entry)**

---

## 🔗 Relationship Diagram

```
┌─────────────────────┐
│   COA MAIN (L1)     │  ← Level 1: Kategori Utama (10 records)
│   coa_main_code PK  │     Contoh: 1-ASSETS, 2-LIABILITIES
└──────────┬──────────┘
           │ 1:N (via prefix matching)
           │ coa_main1_code starts with coa_main_code
           ↓
┌─────────────────────┐
│   COA SUB1 (L2)     │  ← Level 2: Sub Kategori (9 records)
│   coa_main1_code PK │     Contoh: 11-CURRENT ASSETS, 12-FIXED ASSETS
│   (juga berfungsi   │     coa_main1_code = PK & FK sekaligus
│    sebagai FK)      │
└──────────┬──────────┘
           │ 1:N (via FK eksplisit)
           │ coa_main2_coamainsub1code = coa_main1_code
           ↓
┌─────────────────────┐
│   COA SUB2 (L3)     │  ← Level 3: Sub Detail (9 records)
│   coa_main2_code PK │     Contoh: 111-CASH & BANK, 112-ACCOUNTS RECEIVABLE
│   coa_main2_coamain │     FK: coa_main2_coamainsub1code
│   sub1code FK       │
└──────────┬──────────┘
           │ 1:N (via FK eksplisit)
           │ coa_coasub2code = coa_main2_code
           ↓
┌─────────────────────┐
│   COA DETAIL (L4)   │  ← Level 4: Akun Transaksi (501 records)
│   coa_code PK       │     Contoh: 1111001-Kas Kecil, 1111002-Bank BCA
│   coa_coasub2code FK│     ⭐ DIGUNAKAN UNTUK JOURNAL ENTRY
└─────────────────────┘
```

**Catatan Penting:**
- **L1 → L2**: Relationship via **prefix matching** (tidak ada kolom FK eksplisit)
  - Query: `LEFT(coa_main1_code, LEN(coa_main_code)) = coa_main_code`
  - Contoh: `11` belongs to `1`, `12` belongs to `1`, `21` belongs to `2`
  
- **L2 → L3**: FK eksplisit `coa_main2_coamainsub1code` → `coa_main1_code`

- **L3 → L4**: FK eksplisit `coa_coasub2code` → `coa_main2_code`

---

## 🎯 Tipe Akun (Account Type)

Berdasarkan kode COA, sistem otomatis menentukan tipe akun:

| Digit Awal | Tipe Akun | Sifat Normal | Contoh |
|------------|-----------|--------------|---------|
| **1xxx** | **Asset** | Debit | Kas, Bank, Piutang, Inventory |
| **2xxx** | **Liability** | Kredit | Utang Dagang, Utang Bank |
| **3xxx** | **Equity** | Kredit | Modal, Laba Ditahan |
| **4xxx** | **Revenue** | Kredit | Penjualan, Pendapatan Jasa |
| **5xxx** | **Expense** | Debit | Gaji, Listrik, Sewa |

> **Logic:** Kode diambil dari 3 karakter pertama `coa_code`, kemudian digit pertama menentukan tipe.

---

## 📝 Contoh Hierarki Lengkap

### Contoh 1: Kas Kecil
```
Level 1 (Main):       1 - ASSETS
    ↓
Level 2 (Sub1):       11 - CURRENT ASSETS
    ↓
Level 3 (Sub2):       111 - CASH & BANK
    ↓
Level 4 (Detail):     1111001 - Kas Kecil ⭐
```

### Contoh 2: Utang Dagang
```
Level 1 (Main):       2 - LIABILITIES
    ↓
Level 2 (Sub1):       21 - CURRENT LIABILITIES
    ↓
Level 3 (Sub2):       211 - ACCOUNTS PAYABLE
    ↓
Level 4 (Detail):     2111001 - Utang Supplier A ⭐
```

### Contoh 3: Beban Gaji
```
Level 1 (Main):       5 - EXPENSES
    ↓
Level 2 (Sub1):       51 - OPERATIONAL EXPENSES
    ↓
Level 3 (Sub2):       511 - PERSONNEL EXPENSES
    ↓
Level 4 (Detail):     5111001 - Gaji Karyawan ⭐
```

---

## 🔐 Audit Trail

Semua tabel dilengkapi dengan **audit trail** untuk tracking:

| Field | Keterangan |
|-------|------------|
| `rec_usercreated` | User yang membuat record |
| `rec_datecreated` | Waktu pembuatan record |
| `rec_userupdate` | User yang terakhir mengubah |
| `rec_dateupdate` | Waktu perubahan terakhir |
| `rec_status` | Status record (A/I/D) |

**Status Values:**
- `A` = **Active** (Aktif, bisa digunakan)
- `I` = **Inactive** (Non-aktif, tidak bisa digunakan)
- `D` = **Deleted** (Soft delete, tersembunyi)

> **Note:** Sistem tidak menggunakan hard delete untuk menjaga integritas data historis.

---

## 🎨 Tampilan UI

### Dashboard
Menampilkan statistik:
- Total COA (Level 4)
- Active/Inactive accounts
- Hierarchy breakdown (L1, L2, L3, L4)
- Account type distribution
- Recent activities

### COA Management Table
Setiap baris menampilkan:

| Column | Keterangan |
|--------|------------|
| **COA Code** | Kode akun (bold) + ID |
| **Description** | Nama akun + note (jika ada) |
| **Hierarchy (4 Levels)** | Badge L1, L2, L3, L4 dengan warna & indent |
| **Type** | Badge warna (Asset/Liability/Equity/Revenue/Expense) |
| **Status** | Active (hijau) / Inactive (merah) |
| **Actions** | Edit / Delete |

**Badge Colors:**
- 🔵 **L1 (Blue)** - COA Main
- 🟢 **L2 (Green)** - COA Sub1
- 🟣 **L3 (Purple)** - COA Sub2
- 🟠 **L4 (Orange)** - COA Detail (current)

---

## 🔍 Fitur Pencarian & Filter

### Search
Mencari berdasarkan:
- `coa_code` (kode akun)
- `coa_desc` (deskripsi)
- `coa_note` (catatan)

### Filters
- **Status**: All / Active / Inactive / Deleted
- **Parent COA Sub2**: Filter by level 3 parent
- **Per Page**: 10 / 25 / 50 / 100 records

---

## 💾 Laravel Models

### Model Relationships

```php
// Coa (Level 4)
class Coa extends Model {
    public function coaMainSub2() // Parent L3
    public function getAccountTypeAttribute() // Auto-detect type
    public function getHierarchyPathAttribute() // Full path
}

// CoaMainSub2 (Level 3)
class CoaMainSub2 extends Model {
    public function coaMainSub1() // Parent L2
    public function coas() // Children L4
}

// CoaMainSub1 (Level 2)
class CoaMainSub1 extends Model {
    public function coaMain() // Parent L1
    public function coaMainSub2s() // Children L3
}

// CoaMain (Level 1)
class CoaMain extends Model {
    public function coaMainSub1s() // Children L2
}
```

### Scopes yang Tersedia

```php
// Active records only
Coa::active()->get();

// Search by code/description/note
Coa::search('kas')->get();

// Filter by parent
Coa::byParent('111')->get();

// Eager load full hierarchy (4 levels)
Coa::withHierarchy()->get();
```

---

## 🚀 Penggunaan dalam Transaksi

### Journal Entry
Hanya **Level 4 (COA Detail)** yang bisa digunakan dalam journal entry:

```php
// ✅ BENAR - Menggunakan Level 4
Journal::create([
    'coa_code' => '1111001', // Kas Kecil (Level 4)
    'debit' => 1000000,
    'credit' => 0,
]);

// ❌ SALAH - Menggunakan Level 1/2/3
Journal::create([
    'coa_code' => '111', // Cash & Bank (Level 3) - TIDAK BISA!
]);
```

### Validation Rules
```php
// Cek apakah COA bisa digunakan untuk transaksi
$coa = Coa::find('1111001');
if ($coa->canBeUsed()) {
    // Proses transaksi
}
```

Method `canBeUsed()` akan cek:
1. Apakah record ada
2. Apakah status = 'A' (Active)
3. Apakah punya parent valid (coa_coasub2code exists)

---

## 📊 Reports yang Dapat Dihasilkan

Dengan struktur 4 tingkat ini, dapat menghasilkan berbagai laporan:

### 1. Trial Balance
Menampilkan saldo **Level 4** dengan grouping by Level 3/2/1

### 2. Balance Sheet (Neraca)
```
ASSETS (L1)
  Current Assets (L2)
    Cash & Bank (L3)
      - Kas Kecil (L4): Rp 5,000,000
      - Bank BCA (L4): Rp 50,000,000
    Accounts Receivable (L3)
      - Piutang Dagang (L4): Rp 25,000,000
```

### 3. Income Statement (Laba Rugi)
```
REVENUE (L1)
  Sales (L2)
    Product Sales (L3)
      - Sales Product A (L4): Rp 100,000,000
      
EXPENSES (L1)
  Operational (L2)
    Personnel (L3)
      - Gaji Karyawan (L4): Rp 30,000,000
```

### 4. Cash Flow Statement
Menggunakan field `arus_kas_code` untuk mapping aktivitas:
- Operating Activities
- Investing Activities
- Financing Activities

---

## ⚙️ Best Practices

### DO ✅
- Gunakan **Level 4** untuk semua transaksi
- Maintain parent-child relationship integrity
- Gunakan `rec_status = 'D'` untuk soft delete
- Set `rec_status = 'I'` jika akun sudah tidak dipakai tapi punya histori
- Eager load hierarchy dengan `withHierarchy()` untuk performa
- Gunakan scope `active()` untuk filter akun aktif
- Backup database sebelum perubahan struktur besar
- Test query di development sebelum production

### DON'T ❌
- ❌ **JANGAN PERNAH** hard delete records (preserve audit trail)
- ❌ **JANGAN PERNAH** menjalankan `TRUNCATE` atau `DROP TABLE`
- ❌ **JANGAN PERNAH** `DELETE FROM` tanpa WHERE clause
- ❌ Jangan gunakan Level 1/2/3 untuk transaksi
- ❌ Jangan delete parent yang masih punya active children
- ❌ Jangan ubah `coa_code` setelah ada transaksi
- ❌ Jangan skip validation rules
- ❌ Jangan ALTER TABLE di production tanpa backup

### 🔒 DATA PROTECTION RULES
**CRITICAL: Untuk menjaga integritas data akuntansi**

1. **No Hard Delete** - Selalu gunakan soft delete (rec_status = 'D')
2. **No Truncate** - Data historis harus preserved untuk audit
3. **No Mass Delete** - Hapus data harus satu per satu dengan validasi
4. **Backup First** - Selalu backup sebelum migration yang mengubah struktur
5. **Test First** - Test di development sebelum apply di production

**Jika perlu "menghapus" data:**
```php
// ✅ BENAR - Soft Delete
$coa->update(['rec_status' => 'D']);

// ❌ SALAH - Hard Delete
$coa->delete(); // Jangan!
Coa::truncate(); // SANGAT BERBAHAYA!
```

---

## 🔒 Authorization (Policy)

```php
// View - Semua authenticated user
$this->authorize('view', $coa);

// Create/Update - Admin & Accountant only
$this->authorize('create', Coa::class);
$this->authorize('update', $coa);

// Delete - Admin only
$this->authorize('delete', $coa);

// Force Delete - TIDAK DIPERBOLEHKAN
// Untuk maintain audit trail
```

---

## 📈 Performance Optimization

### Eager Loading
```php
// ❌ N+1 Query Problem
foreach($coas as $coa) {
    echo $coa->coaMainSub2->coaMainSub1->coaMain->coa_main_desc;
}

// ✅ Eager Load (1 Query)
$coas = Coa::withHierarchy()->get();
foreach($coas as $coa) {
    echo $coa->coaMainSub2->coaMainSub1->coaMain->coa_main_desc;
}
```

### Indexing
Pastikan ada index pada:
- Primary Keys: `coa_code`, `coa_main2_code`, `coa_main1_code`, `coa_main_code`
- Foreign Keys: `coa_coasub2code`, `coa_main1_code`, `coa_main_code`
- Status: `rec_status` (untuk filter active/inactive)

---

## 🎓 Summary

| Level | Table | Records | Digunakan untuk |
|-------|-------|---------|----------------|
| **L1** | ms_acc_coa_main | 10 | Kategori utama (grouping) |
| **L2** | ms_acc_coa_main_sub1 | 9 | Sub-kategori (grouping) |
| **L3** | ms_acc_coa_main_sub2 | 9 | Sub-detail (grouping) |
| **L4** | ms_acc_coa | 501 | **Transaksi aktual** ⭐ |

**Total Struktur:** 529 records dengan hierarki 4 tingkat untuk pengelolaan akuntansi yang fleksibel dan detail.

---

## 📞 Technical Support

Untuk pertanyaan lebih lanjut tentang struktur COA, silakan refer ke:
- **Models:** `app/Models/Coa.php`, `CoaMainSub2.php`, `CoaMainSub1.php`, `CoaMain.php`
- **Component:** `app/Livewire/CoaManagement.php`
- **Guidelines:** `AI_DEVELOPMENT_GUIDELINES.md`
- **Database:** MS SQL Server - `RCM_DEV_HGS_SB`

---

**Last Updated:** November 6, 2025  
**Version:** 1.0  
**Project:** AccAdmin - Accounting Administration System
