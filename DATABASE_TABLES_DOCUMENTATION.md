# Database Tables Documentation
**Project:** AccAdmin - Accounting Administration System  
**Database:** RCM_DEV_HGS_SB (MS SQL Server)  
**Server:** 66.96.240.131:26402  
**Last Updated:** November 6, 2025

---

## 📋 Table of Contents
1. [COA Tables (Chart of Accounts)](#coa-tables)
2. [Transaction Tables](#transaction-tables)
3. [Master Data Tables](#master-data-tables)
4. [User Management Tables](#user-management-tables)
5. [System Tables](#system-tables)

---

## 🗂️ COA Tables (Chart of Accounts)

### 4-Level Legacy Hierarchy + Flexible Modern System

**Legacy System (4 Tabel Terpisah):**
- **Level 1:** `ms_acc_coa_main` - Main Category (10 records)
- **Level 2:** `ms_acc_coasub1` - Sub Category 1 (18 records)
- **Level 3:** `ms_acc_coasub2` - Sub Category 2 (58 records)
- **Level 4:** `ms_acc_coa` - Detail COA Accounts (501+ records)

**Modern System (1 Tabel):**
- `ms_acc_coa` dengan flexible hierarchy H1-H6 (1-6 levels)

**Relationship Chain:**
```
ms_acc_coa_main (coa_main_code)
    ↓ 
ms_acc_coasub1 (coasub1_maincode → coa_main_code)
    ↓ 
ms_acc_coasub2 (coasub2_coasub1code → coasub1_code)
    ↓ 
ms_acc_coa (coa_coasub2code → coasub2_code)
```

---

### 1. `ms_acc_coa_main` - COA Level 1 (Main Category)

**Purpose:** Tabel kategori utama COA (Level tertinggi) - mengelompokkan akun berdasarkan kategori besar (Asset, Liability, Equity, Revenue, Expense)

**Total Records:** 10

**Status:** ✅ AKTIF - Digunakan untuk Legacy System

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `coa_main_code` | varchar | 50 | NO | - | **Primary Key** - Kode kategori utama (contoh: 10000, 20000, 50000) |
| `coa_main_desc` | varchar | 50 | YES | NULL | Deskripsi kategori utama (contoh: "Asset", "Liability") |
| `coa_main_id` | varchar | 50 | YES | NULL | ID alternatif (sama dengan coa_main_code) |
| `coa_main_coamain2code` | varchar | 50 | YES | NULL | Reference code (sama dengan coa_main_code) |
| `rec_status` | char | 1 | NO | - | Status: '1'=Active, '0'=Inactive |
| `rec_usercreated` | varchar | 50 | NO | - | User yang membuat record |
| `rec_datecreated` | datetime | - | NO | - | Timestamp pembuatan |
| `rec_userupdate` | varchar | 50 | NO | - | User yang terakhir update |
| `rec_dateupdate` | datetime | - | NO | - | Timestamp update terakhir |

**Sample Data:**
```
coa_main_code | coa_main_desc | rec_status
10000         | Asset         | 1
20000         | Liability     | 1
50000         | Expense       | 1
```

**Relationships:**
- Has Many: `ms_acc_coasub1` via `coa_main_code` = `coasub1_maincode`

**Laravel Model:** `App\Models\CoaMain`

---

### 2. `ms_acc_coasub1` - COA Level 2 (Sub Category 1)

**Purpose:** Tabel sub kategori pertama - membagi kategori utama menjadi sub-kategori lebih spesifik

**Total Records:** 18

**Status:** ✅ AKTIF - Digunakan untuk Legacy System

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `coasub1_code` | varchar | 50 | NO | - | **Primary Key** - Kode sub kategori 1 (contoh: 1, 10, 11, 14, 16) |
| `coasub1_desc` | varchar | 255 | YES | NULL | Deskripsi sub kategori 1 |
| `coasub1_id` | int | - | YES | NULL | ID numerik (contoh: 53000, 52000, 54000) |
| `coasub1_maincode` | varchar | 50 | YES | NULL | **Foreign Key** → `ms_acc_coa_main.coa_main_code` |
| `rec_status` | char | 1 | NO | - | Status: '1'=Active, '0'=Inactive |
| `rec_usercreated` | varchar | 50 | NO | - | User yang membuat record |
| `rec_datecreated` | datetime | - | NO | - | Timestamp pembuatan |
| `rec_userupdate` | varchar | 50 | NO | - | User yang terakhir update |
| `rec_dateupdate` | datetime | - | NO | - | Timestamp update terakhir |

**Sample Data:**
```
coasub1_code | coasub1_desc                  | coasub1_maincode | rec_status
1            | Biaya Operasi tak terduga     | 50000            | 1
10           | Biaya Operasi (fix cost)      | 50000            | 1
11           | Damage & lost                 | 50000            | 1
14           | Revenue                       | 40000            | 1
16           | Aktiva tetap                  | 10000            | 1
```

**Relationships:**
- Belongs To: `ms_acc_coa_main` via `coasub1_maincode` = `coa_main_code`
- Has Many: `ms_acc_coasub2` via `coasub1_code` = `coasub2_coasub1code`

**Laravel Model:** `App\Models\CoaSub1`

---

### 3. `ms_acc_coasub2` - COA Level 3 (Sub Category 2)

**Purpose:** Tabel sub kategori kedua - pembagian lebih detail dari sub kategori 1

**Total Records:** 58

**Status:** ✅ AKTIF - Digunakan untuk Legacy System

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `coasub2_code` | varchar | 50 | NO | - | **Primary Key** - Kode sub kategori 2 (contoh: 1, 10, 11, 12, 56) |
| `coasub2_desc` | varchar | 255 | YES | NULL | Deskripsi sub kategori 2 |
| `coasub2_id` | int | - | YES | NULL | ID numerik (contoh: 52100, 41400, 12200) |
| `coasub2_coasub1code` | varchar | 50 | YES | NULL | **Foreign Key** → `ms_acc_coasub1.coasub1_code` |
| `rec_status` | char | 1 | NO | - | Status: '1'=Active, '0'=Inactive |
| `rec_usercreated` | varchar | 50 | NO | - | User yang membuat record |
| `rec_datecreated` | datetime | - | NO | - | Timestamp pembuatan |
| `rec_userupdate` | varchar | 50 | NO | - | User yang terakhir update |
| `rec_dateupdate` | datetime | - | NO | - | Timestamp update terakhir |

**Sample Data:**
```
coasub2_code | coasub2_desc              | coasub2_coasub1code | rec_status
1            | Pajak kendaraan           | 10                  | 1
10           | Pendapatan                | 14                  | 1
11           | Kendaraan non trucking    | 16                  | 1
12           | Peralatan kantor          | 16                  | 1
56           | Biaya komunikasi          | 10                  | 1
```

**Relationships:**
- Belongs To: `ms_acc_coasub1` via `coasub2_coasub1code` = `coasub1_code`
- Has Many: `ms_acc_coa` via `coasub2_code` = `coa_coasub2code`

**Join Example:**
```sql
SELECT s2.coasub2_code, s2.coasub2_desc, s1.coasub1_desc, m.coa_main_desc
FROM ms_acc_coasub2 s2
LEFT JOIN ms_acc_coasub1 s1 ON s2.coasub2_coasub1code = s1.coasub1_code
LEFT JOIN ms_acc_coa_main m ON s1.coasub1_maincode = m.coa_main_code
WHERE s2.rec_status = '1'
```

**Laravel Model:** `App\Models\CoaSub2`

---

### 4. `ms_acc_coa` - COA Level 4 (Detail Accounts) + Modern H1-H6

**Purpose:** Tabel utama COA - berisi detail akun transaksi. Tabel ini mendukung **DUAL SYSTEM:**
1. **Legacy System:** Via FK `coa_coasub2code` (4-level hierarchy)
2. **Modern System:** Via kolom H1-H6 (flexible 1-6 level hierarchy)

**Total Records:** 501

**Status:** ✅ AKTIF - Digunakan untuk kedua sistem (Legacy & Modern)

#### **Core Columns (Legacy System)**

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `coa_code` | varchar | 50 | NO | - | **Primary Key** - Kode akun COA (contoh: 10, 100, 1000) |
| `coa_id` | int | - | YES | NULL | ID numerik unik (contoh: 62505, 51123) |
| `coa_desc` | varchar | 255 | YES | NULL | Deskripsi akun (contoh: "B. Tlpn KP HGS", "B. Transport Kantung Parkir HGS") |
| `coa_note` | text | - | YES | NULL | Catatan tambahan untuk akun |
| `coa_coasub2code` | varchar | 50 | YES | NULL | **Foreign Key (Legacy)** → `ms_acc_coasub2.coasub2_code` |
| `arus_kas_code` | varchar | 50 | YES | NULL | Kode arus kas |
| `rec_status` | char | 1 | NO | - | Status: '1'=Active, '0'=Inactive |
| `rec_usercreated` | varchar | 50 | NO | - | User yang membuat record |
| `rec_datecreated` | datetime | - | NO | - | Timestamp pembuatan |
| `rec_userupdate` | varchar | 50 | NO | - | User yang terakhir update |
| `rec_dateupdate` | datetime | - | NO | - | Timestamp update terakhir |

#### **Modern System Columns (H1-H6 Flexible Hierarchy)**

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `ms_coa_h1_id` | int | - | YES | NULL | ID Hierarchy Level 1 |
| `ms_coa_h2_id` | int | - | YES | NULL | ID Hierarchy Level 2 |
| `ms_coa_h3_id` | int | - | YES | NULL | ID Hierarchy Level 3 |
| `ms_coa_h4_id` | int | - | YES | NULL | ID Hierarchy Level 4 |
| `ms_coa_h5_id` | int | - | YES | NULL | ID Hierarchy Level 5 |
| `ms_coa_h6_id` | int | - | YES | NULL | ID Hierarchy Level 6 |
| `desc_h1` | varchar | 255 | YES | NULL | Deskripsi Level 1 |
| `desc_h2` | varchar | 255 | YES | NULL | Deskripsi Level 2 |
| `desc_h3` | varchar | 255 | YES | NULL | Deskripsi Level 3 |
| `desc_h4` | varchar | 255 | YES | NULL | Deskripsi Level 4 |
| `desc_h5` | varchar | 255 | YES | NULL | Deskripsi Level 5 |
| `desc_h6` | varchar | 255 | YES | NULL | Deskripsi Level 6 |
| `id_h1` | int | - | YES | NULL | Integer ID Level 1 |
| `id_h2` | int | - | YES | NULL | Integer ID Level 2 |
| `id_h3` | int | - | YES | NULL | Integer ID Level 3 |
| `id_h4` | int | - | YES | NULL | Integer ID Level 4 |
| `id_h5` | int | - | YES | NULL | Integer ID Level 5 |
| `id_h6` | int | - | YES | NULL | Integer ID Level 6 |

#### **Legacy Reference Columns (Old System - Not Used)**

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `id_old_sub_2` | varchar | 50 | YES | NULL | Legacy reference - ID old sub 2 |
| `id_old_sub1` | varchar | 50 | YES | NULL | Legacy reference - ID old sub 1 |
| `id_old_main` | varchar | 50 | YES | NULL | Legacy reference - ID old main |
| `sub2_desc` | varchar | 255 | YES | NULL | Legacy reference - Sub2 description |
| `sub1_desc` | varchar | 255 | YES | NULL | Legacy reference - Sub1 description |
| `main_desc` | varchar | 255 | YES | NULL | Legacy reference - Main description |
| `ms_acc_coa_h` | varchar | 255 | YES | NULL | Legacy reference column |

**Sample Data:**
```
coa_code | coa_desc                        | coa_coasub2code | rec_status
10       | B. Tlpn KP HGS                  | 56              | 1
100      | B. Transport Kantung Parkir HGS | 12              | 1
1000     | Cash in Hand                    | 22              | 1
```

**Relationships:**
- Belongs To (Legacy): `ms_acc_coasub2` via `coa_coasub2code` = `coasub2_code`

**Dual System Access Pattern:**

**1. Legacy Access (4-Level):**
```sql
-- Get COA with full hierarchy via legacy FK
SELECT 
    c.coa_code, c.coa_desc,
    s2.coasub2_desc AS level3,
    s1.coasub1_desc AS level2,
    m.coa_main_desc AS level1
FROM ms_acc_coa c
LEFT JOIN ms_acc_coasub2 s2 ON c.coa_coasub2code = s2.coasub2_code
LEFT JOIN ms_acc_coasub1 s1 ON s2.coasub2_coasub1code = s1.coasub1_code
LEFT JOIN ms_acc_coa_main m ON s1.coasub1_maincode = m.coa_main_code
WHERE c.rec_status = '1'
```

**2. Modern Access (H1-H6 Flexible):**
```sql
-- Get COA with flexible hierarchy (1-6 levels)
SELECT 
    coa_code, coa_desc,
    desc_h1, desc_h2, desc_h3, desc_h4, desc_h5, desc_h6
FROM ms_acc_coa
WHERE rec_status = '1'
  AND ms_coa_h1_id IS NOT NULL
```

**Business Rules:**
- Status '1' = Active accounts dapat digunakan untuk transaksi
- Status '0' = Inactive accounts tidak dapat digunakan
- Kolom H1-H6 masih NULL untuk semua record (modern system belum digunakan)
- Semua record saat ini menggunakan legacy system via `coa_coasub2code`

**Laravel Model:** `App\Models\Coa`

**Important Notes:**
- ⚠️ **Jangan hapus kolom legacy (`coa_coasub2code`)** - masih digunakan
- ⚠️ **Kolom H1-H6 masih kosong** - untuk migrasi sistem modern di masa depan
- ✅ Sistem saat ini 100% menggunakan legacy 4-level hierarchy
- ✅ Modern system (H1-H6) siap untuk implementasi bertahap

---

**Relationships:**
- **Belongs To:** `ms_acc_coa_main_sub1` (N:1) - Via `coa_main2_code` prefix matching
- **Has Many:** `ms_acc_coa` (1:N)

**Relationship Logic:**
- `coa_main2_code` adalah **PK sekaligus FK** ke parent
- Prefix matching: 2 karakter pertama dari `coa_main2_code` = `coa_main1_code`
- Contoh: `111` (CASH & BANK) → parent `11` (CURRENT ASSETS)
- Contoh: `121` (LAND & BUILDING) → parent `12` (FIXED ASSETS)

**Sample Data:**
```sql
coa_main2_code | coa_main2_desc              | Parent (coa_main1_code)
111            | CASH & BANK                 | 11 (CURRENT ASSETS)
112            | ACCOUNTS RECEIVABLE         | 11 (CURRENT ASSETS)
121            | LAND & BUILDING             | 12 (FIXED ASSETS)
```

---

### 4. `ms_acc_coa` - COA Level 4 (Detail Accounts) ⭐ AKTIF

**Purpose:** Tabel UTAMA yang menyimpan akun detail untuk transaksi. Tabel ini digunakan di DUA sistem sekaligus:
1. **Legacy System**: Sebagai Level 4 yang tersambung ke Level 3 (ms_acc_coa_main_sub2) via `coa_coasub2code`
2. **Modern System**: Menyimpan flexible hierarchy H1-H6 dalam field `ms_coa_h1_id` sampai `ms_coa_h6_id`

**Total Records:** 501+

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `id` | int | - | NO | IDENTITY | Auto-increment ID |
| `coa_code` | varchar | 50 | NO | - | **Primary Key** - Kode akun detail |
| `coa_id` | varchar | 50 | YES | NULL | ID alternatif akun |
| `coa_desc` | varchar | 255 | YES | NULL | Deskripsi akun |
| `coa_note` | text | - | YES | NULL | Catatan tambahan (optional) |
| `arus_kas_code` | varchar | 50 | YES | NULL | Kode untuk mapping laporan arus kas |
| `ms_acc_coa_h` | varchar | 50 | YES | NULL | Header COA |
| **LEGACY LINK** | | | | | |
| `coa_coasub2code` | varchar | 50 | YES | NULL | **Foreign Key** → ms_acc_coa_main_sub2.coa_main2_code (Legacy Level 3) |
| **MODERN HIERARCHY (H1-H6)** | | | | | |
| `ms_coa_h1_id` | varchar | 50 | YES | NULL | Level 1 ID (string) |
| `id_h1` | int | - | YES | NULL | Level 1 ID (integer) |
| `desc_h1` | varchar | 255 | YES | NULL | Level 1 Description |
| `ms_coa_h2_id` | varchar | 50 | YES | NULL | Level 2 ID (string) |
| `id_h2` | int | - | YES | NULL | Level 2 ID (integer) |
| `desc_h2` | varchar | 255 | YES | NULL | Level 2 Description |
| `ms_coa_h3_id` | varchar | 50 | YES | NULL | Level 3 ID (string) |
| `id_h3` | int | - | YES | NULL | Level 3 ID (integer) |
| `desc_h3` | varchar | 255 | YES | NULL | Level 3 Description |
| `ms_coa_h4_id` | varchar | 50 | YES | NULL | Level 4 ID (string) |
| `id_h4` | int | - | YES | NULL | Level 4 ID (integer) |
| `desc_h4` | varchar | 255 | YES | NULL | Level 4 Description |
| `ms_coa_h5_id` | varchar | 50 | YES | NULL | Level 5 ID (string) |
| `id_h5` | int | - | YES | NULL | Level 5 ID (integer) |
| `desc_h5` | varchar | 255 | YES | NULL | Level 5 Description |
| `ms_coa_h6_id` | varchar | 50 | YES | NULL | Level 6 ID (string) |
| `id_h6` | int | - | YES | NULL | Level 6 ID (integer) |
| `desc_h6` | varchar | 255 | YES | NULL | Level 6 Description |
| **AUDIT FIELDS** | | | | | |
| `rec_status` | char | 1 | NO | - | Status: A=Active, I=Inactive, D=Deleted |
| `rec_usercreated` | varchar | 50 | NO | - | User pembuat |
| `rec_datecreated` | datetime | - | NO | - | Timestamp pembuatan |
| `rec_userupdate` | varchar | 50 | NO | - | User update |
| `rec_dateupdate` | datetime | - | NO | - | Timestamp update |

**Indexes:**
- PRIMARY KEY: `coa_code`
- UNIQUE: `id` (Auto-increment)
- FOREIGN KEY: `coa_coasub2code` REFERENCES `ms_acc_coa_main_sub2(coa_main2_code)`
- INDEX: `idx_parent` ON (`coa_coasub2code`)
- INDEX: `idx_status` ON (`rec_status`)

**Relationships:**
- **LEGACY**: Belongs To `ms_acc_coa_main_sub2` (N:1) via `coa_coasub2code`
- **Has Many:** Transaction details (1:N)

**Dual System Access:**
- **Legacy View**: User navigates Main → Sub1 → Sub2 → COA
- **Modern View**: User navigates H1 → H2 → H3 → H4 → H5 → H6 (flexible)

**Business Rules:**
- Record yang sama bisa diakses via 2 cara (Legacy 4-level atau Modern H1-H6)
- Akun bisa digunakan dalam transaksi di level mana saja (tergantung kebutuhan)
- Field `coa_coasub2code` menghubungkan ke Legacy Level 3
- Field `ms_coa_h1_id` sampai `ms_coa_h6_id` untuk Modern flexible hierarchy

**Sample Data:**
```sql
-- Example: Same record accessible via 2 systems
coa_code    | coa_desc      | coa_coasub2code | ms_coa_h1_id | desc_h1  | ms_coa_h2_id | desc_h2
1111001     | Kas Kecil     | 111             | 1            | Assets   | 11           | Current Assets
1111002     | Bank BCA      | 111             | 1            | Assets   | 11           | Current Assets
```
coa_code  | coa_coasub2code | coa_desc        | account_type
1111001   | 111             | Kas Kecil       | Asset
1111002   | 111             | Bank BCA        | Asset
```

---

## 💼 Transaction Tables

### 5. `[journal_header_table]` - Journal Entry Header

**Purpose:** [Lengkapi - Header jurnal umum]

**Total Records:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi kolom-kolom] |
| `` | | | | | |
| `` | | | | | |

**Indexes:**
- [Lengkapi]

**Relationships:**
- [Lengkapi]

**Business Rules:**
- [Lengkapi]

---

### 6. `[journal_detail_table]` - Journal Entry Detail

**Purpose:** [Lengkapi - Detail jurnal (debit/kredit)]

**Total Records:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |
| `coa_code` | varchar | 50 | | | Foreign Key → ms_acc_coa |
| `debit` | decimal | (18,2) | | | Nilai debit |
| `credit` | decimal | (18,2) | | | Nilai kredit |

**Indexes:**
- [Lengkapi]

**Relationships:**
- **Belongs To:** `ms_acc_coa` via `coa_code`
- [Lengkapi lainnya]

**Business Rules:**
- Setiap transaksi harus balance (total debit = total credit)
- [Lengkapi]

---

## 👥 User Management Tables

### 7. `users` - User Accounts

**Purpose:** [Lengkapi - Akun user sistem]

**Total Records:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `id` | bigint | - | NO | AUTO | Primary Key |
| `name` | varchar | 255 | NO | - | Nama user |
| `email` | varchar | 255 | NO | - | Email (unique) |
| `password` | varchar | 255 | NO | - | Hashed password |
| `remember_token` | varchar | 100 | YES | NULL | Remember me token |
| `email_verified_at` | datetime | - | YES | NULL | Verifikasi email |
| `created_at` | datetime | - | YES | NULL | Timestamp dibuat |
| `updated_at` | datetime | - | YES | NULL | Timestamp update |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE: `email`

**Relationships:**
- [Lengkapi jika ada role/permission tables]

**Business Rules:**
- Password harus di-hash dengan bcrypt
- Email harus unique
- [Lengkapi]

---

### 8. `[roles_table]` - User Roles (Optional)

**Purpose:** [Lengkapi - Role management]

**Total Records:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |

---

## 📊 Master Data Tables

### 9. `[customer_table]` - Customer Master (Optional)

**Purpose:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |

---

### 10. `[vendor_table]` - Vendor/Supplier Master (Optional)

**Purpose:** [Lengkapi]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |

---

### 11. `[cash_flow_category_table]` - Cash Flow Categories

**Purpose:** [Lengkapi - Kategori untuk laporan arus kas]

**Related to:** `ms_acc_coa.arus_kas_code`

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `arus_kas_code` | varchar | 50 | NO | - | Primary Key |
| `` | | | | | [Lengkapi] |

**Categories:**
- Operating Activities
- Investing Activities  
- Financing Activities

---

## ⚙️ System Tables

### 12. `[audit_log_table]` - Audit Trail (Optional)

**Purpose:** [Lengkapi - Log aktivitas sistem]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |

---

### 13. `[settings_table]` - System Settings (Optional)

**Purpose:** [Lengkapi - Konfigurasi sistem]

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `` | | | | | [Lengkapi] |

---

## 📐 Database Diagram

```
[Buat diagram relasi antar tabel di sini]

ms_acc_coa_main (1)
    ↓ (1:N)
ms_acc_coa_main_sub1 (1)
    ↓ (1:N)
ms_acc_coa_main_sub2 (1)
    ↓ (1:N)
ms_acc_coa (501)
    ↓ (1:N)
[journal_detail] ← (N:1) → [journal_header]
```

---

## 🔐 Standard Columns (Audit Trail)

Semua tabel master menggunakan kolom standar:

| Column | Type | Description |
|--------|------|-------------|
| `rec_status` | char(1) | A=Active, I=Inactive, D=Deleted |
| `rec_usercreated` | varchar(50) | User pembuat |
| `rec_datecreated` | datetime | Timestamp dibuat |
| `rec_userupdate` | varchar(50) | User update terakhir |
| `rec_dateupdate` | datetime | Timestamp update |

---

## 📝 Naming Conventions

### Tables
- Master data: `ms_acc_*` (contoh: `ms_acc_coa`)
- Transaction: `tr_acc_*` atau sesuai konvensi
- System: `sys_*`

### Columns
- Primary Key: `[table]_code` atau `[table]_id`
- Foreign Key: sesuai nama kolom di parent table
- Status: `rec_status`
- Audit: `rec_user*`, `rec_date*`

---

## 🚀 To-Do List (Untuk dilengkapi)

- [ ] Lengkapi nama tabel transaksi (journal header/detail)
- [ ] Lengkapi struktur tabel customer/vendor (jika ada)
- [ ] Lengkapi tabel roles/permissions
- [ ] Lengkapi business rules di setiap tabel
- [ ] Lengkapi sample data
- [ ] Buat database diagram visual
- [ ] Tambahkan stored procedures (jika ada)
- [ ] Tambahkan views (jika ada)
- [ ] Tambahkan triggers (jika ada)

---

## � Notes

**Instructions untuk melengkapi:**
1. Jalankan query di SQL Server untuk mendapatkan struktur lengkap:
```sql
-- List semua tabel
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

-- Detail kolom untuk tabel tertentu
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'nama_tabel'
ORDER BY ORDINAL_POSITION;

-- Foreign Keys
SELECT 
    fk.name AS FK_Name,
    tp.name AS Parent_Table,
    cp.name AS Parent_Column,
    tr.name AS Referenced_Table,
    cr.name AS Referenced_Column
FROM sys.foreign_keys AS fk
INNER JOIN sys.tables AS tp ON fk.parent_object_id = tp.object_id
INNER JOIN sys.tables AS tr ON fk.referenced_object_id = tr.object_id
INNER JOIN sys.foreign_key_columns AS fkc ON fk.object_id = fkc.constraint_object_id
INNER JOIN sys.columns AS cp ON fkc.parent_column_id = cp.column_id AND fkc.parent_object_id = cp.object_id
INNER JOIN sys.columns AS cr ON fkc.referenced_column_id = cr.column_id AND fkc.referenced_object_id = cr.object_id;
```

2. Copy struktur ke dokumentasi ini
3. Tambahkan business rules dan keterangan
4. Update diagram relasi

---

## 🚨 CRITICAL: Database Safety Rules

**⛔ DILARANG KERAS - TIDAK BOLEH DILANGGAR:**

### Operasi yang TIDAK BOLEH dilakukan:

1. **❌ DROP DATABASE**
   ```sql
   DROP DATABASE RCM_DEV_HGS_SB; -- JANGAN PERNAH!
   ```

2. **❌ DROP TABLE**
   ```sql
   DROP TABLE ms_acc_coa; -- JANGAN PERNAH!
   ```

3. **❌ TRUNCATE TABLE**
   ```sql
   TRUNCATE TABLE ms_acc_coa; -- JANGAN PERNAH!
   ```

4. **❌ DELETE Tanpa WHERE**
   ```sql
   DELETE FROM ms_acc_coa; -- SANGAT BERBAHAYA!
   ```

5. **❌ ALTER TABLE di Production tanpa Backup**
   ```sql
   ALTER TABLE ms_acc_coa DROP COLUMN coa_desc; -- Backup dulu!
   ```

### ✅ Yang BOLEH dilakukan:

1. **Soft Delete** (Update status)
   ```sql
   UPDATE ms_acc_coa 
   SET rec_status = 'D', 
       rec_userupdate = 'username',
       rec_dateupdate = GETDATE()
   WHERE coa_code = 'xxx';
   ```

2. **SELECT untuk Query**
   ```sql
   SELECT * FROM ms_acc_coa WHERE rec_status = 'A';
   ```

3. **INSERT Data Baru**
   ```sql
   INSERT INTO ms_acc_coa (...) VALUES (...);
   ```

4. **UPDATE dengan WHERE**
   ```sql
   UPDATE ms_acc_coa 
   SET coa_desc = 'New Description'
   WHERE coa_code = 'specific_code';
   ```

### 📋 Checklist Sebelum Perubahan Besar:

- [ ] Backup database lengkap
- [ ] Test di development environment
- [ ] Dokumentasi perubahan
- [ ] Review dengan team
- [ ] Siapkan rollback plan
- [ ] Konfirmasi eksplisit dari user/supervisor

### 🔐 Prinsip Keamanan Data:

1. **Audit Trail Integrity** - Jangan hapus data historis
2. **Soft Delete Always** - Gunakan rec_status = 'D'
3. **Backup Before Change** - Selalu backup sebelum ALTER
4. **Test Before Production** - Jangan langsung di production
5. **Document Everything** - Catat semua perubahan

**INGAT:** Data akuntansi adalah data kritis. Kehilangan data bisa berakibat fatal untuk audit dan compliance!

---

**Version:** 1.0 (Draft)  
**Status:** 🟡 In Progress - Needs Completion  
**Maintained by:** [Nama Anda]
