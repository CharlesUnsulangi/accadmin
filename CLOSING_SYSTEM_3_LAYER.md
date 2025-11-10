# Sistem Closing 3 Layer - Documentation

## 📋 Overview

Sistem closing untuk rekap transaksi `tr_acc_transaksi_coa` dengan 3 layer:

1. **Layer 1: Rekap Bulanan** - Closing per bulan
2. **Layer 2: Rekap Tahunan** - Aggregate dari 12 monthly closing  
3. **Layer 3: Audit** - Hitung ulang dari transaksi pertama untuk verifikasi

---

## 🗂️ Database Tables

### 1. `tr_acc_monthly_closing`

**Purpose:** Menyimpan closing bulanan dengan version control

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Primary Key |
| `version_number` | int | Version (1, 2, 3, dst) |
| `version_status` | varchar(20) | DRAFT / ACTIVE / SUPERSEDED / ARCHIVED |
| `closing_year` | int | Tahun (2024, 2025) |
| `closing_month` | int | Bulan (1-12) |
| `closing_periode_id` | varchar(6) | YYYYMM (202401) |
| `coa_code` | varchar(50) | FK to ms_acc_coa |
| `opening_debet` | decimal(18,2) | Saldo awal debet |
| `opening_kredit` | decimal(18,2) | Saldo awal kredit |
| `opening_balance` | decimal(18,2) | Saldo awal netto |
| `mutasi_debet` | decimal(18,2) | Total debet bulan ini |
| `mutasi_kredit` | decimal(18,2) | Total kredit bulan ini |
| `mutasi_netto` | decimal(18,2) | Netto mutasi |
| `jumlah_transaksi` | int | Jumlah transaksi |
| `closing_debet` | decimal(18,2) | Saldo akhir debet |
| `closing_kredit` | decimal(18,2) | Saldo akhir kredit |
| `closing_balance` | decimal(18,2) | Saldo akhir netto |
| `is_closed` | boolean | Lock status |
| `closed_at` | datetime | Waktu di-lock |
| `closed_by` | varchar(50) | User yang lock |

**Formula:**
```
Closing Balance = Opening Balance + Mutasi Netto
Mutasi Netto = Mutasi Debet - Mutasi Kredit
```

---

### 2. `tr_acc_yearly_closing`

**Purpose:** Menyimpan closing tahunan (aggregate dari 12 monthly)

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Primary Key |
| `version_number` | int | Version |
| `version_status` | varchar(20) | Status |
| `closing_year` | int | Tahun |
| `coa_code` | varchar(50) | FK to ms_acc_coa |
| `opening_balance` | decimal(18,2) | Dari yearly closing tahun sebelumnya |
| `mutasi_debet` | decimal(18,2) | Sum dari 12 monthly |
| `mutasi_kredit` | decimal(18,2) | Sum dari 12 monthly |
| `closing_balance` | decimal(18,2) | Saldo akhir tahun |
| `monthly_summary` | json | Summary per bulan (Jan-Dec) |
| `is_closed` | boolean | Lock status |

**Monthly Summary JSON Format:**
```json
[
  {
    "month": 1,
    "opening": 10000000,
    "mutasi_debet": 5000000,
    "mutasi_kredit": 3000000,
    "mutasi_netto": 2000000,
    "closing": 12000000,
    "transaksi": 150
  },
  ...
]
```

---

## 🔧 Service Layer: `ClosingService`

### Methods:

#### 1. `calculateMonthly($year, $month, $saveToDB = false)`

**Purpose:** Hitung closing bulanan

**Process:**
1. Get all active COA
2. Get opening balance dari:
   - Januari → Yearly closing tahun sebelumnya
   - Bulan lain → Monthly closing bulan sebelumnya
3. Hitung mutasi bulan ini dari `tr_acc_transaksi_coa`
4. Calculate closing = opening + mutasi
5. Save ke DB jika `$saveToDB = true`

**Return:** Array of monthly closing data

---

#### 2. `calculateYearly($year, $saveToDB = false)`

**Purpose:** Hitung closing tahunan

**Process:**
1. Get opening balance dari yearly closing tahun sebelumnya
2. Aggregate mutasi dari 12 monthly closing (ACTIVE version)
3. Build monthly_summary JSON
4. Calculate closing tahunan
5. Save ke DB jika `$saveToDB = true`

**Return:** Array of yearly closing data

---

#### 3. `calculateAudit($coaCode = null, $upToYear = null, $upToMonth = null)`

**Purpose:** Hitung dari transaksi pertama untuk audit

**Process:**
1. Query all transactions dari awal
2. SUM debet & kredit per COA
3. Calculate balance
4. **TIDAK disimpan** ke database (hanya untuk verifikasi)

**Return:** Array of audit data

**Usage:**
```php
// Audit semua COA sampai bulan ini
$audit = $closingService->calculateAudit(null, 2024, 10);

// Audit 1 COA saja
$audit = $closingService->calculateAudit('1010', 2024, 10);
```

---

#### 4. `compareWithAudit($year, $month)`

**Purpose:** Compare monthly closing dengan audit calculation

**Return:** Array of discrepancies (jika ada perbedaan)

---

#### 5. `lockClosing($year, $month, $type = 'monthly')`

**Purpose:** Lock closing agar tidak bisa diubah

**Process:**
- Set `is_closed = true`
- Set `closed_at = now()`
- Set `closed_by = current user`

---

## 🖥️ UI - Closing Process Page

**Route:** `/closing-process`

**Features:**

### Panel Kiri: Konfigurasi
- Pilih tipe closing (Monthly / Yearly / Audit)
- Pilih tahun
- Pilih bulan (jika monthly/audit)
- Button: Preview Data
- Button: Generate & Simpan (jika bukan audit)

### Panel Kanan: Preview
- Tabel preview hasil perhitungan
- Menampilkan:
  - COA Code
  - Deskripsi
  - Opening Balance
  - Mutasi Debet/Kredit
  - Closing Balance
  - Jumlah Transaksi
- Total summary di footer

### Info Panel:
- Penjelasan 3 layer system
- Workflow closing

---

## 🔄 Workflow Penggunaan

### A. Monthly Closing

```mermaid
1. Pilih "Layer 1: Rekap Bulanan"
2. Pilih Tahun & Bulan
3. Klik "Preview Data"
4. Review hasil perhitungan
5. Klik "Generate & Simpan"
6. Data tersimpan dengan status DRAFT
7. (Optional) Lock closing jika sudah final
```

**Contoh:**
- Closing Januari 2024:
  - Opening: Rp 0 (belum ada data)
  - Mutasi Jan: Debet 100jt, Kredit 80jt
  - Closing: Rp 20jt

- Closing Februari 2024:
  - Opening: Rp 20jt (dari closing Jan)
  - Mutasi Feb: Debet 50jt, Kredit 30jt  
  - Closing: Rp 40jt (20jt + 20jt)

---

### B. Yearly Closing

```mermaid
1. Pastikan 12 monthly closing sudah selesai
2. Pilih "Layer 2: Rekap Tahunan"
3. Pilih Tahun
4. Klik "Preview Data"
5. System aggregate dari 12 monthly
6. Klik "Generate & Simpan"
```

**Contoh:**
- Yearly 2024:
  - Opening: Rp 0 (tahun pertama)
  - Mutasi: Sum dari Jan-Dec
  - Closing: Total akhir tahun
  - Monthly Summary: Detail per bulan tersimpan

---

### C. Audit Verification

```mermaid
1. Pilih "Layer 3: Audit"
2. Pilih periode yang mau di-audit
3. Klik "Preview Data"
4. System hitung dari transaksi pertama
5. Compare dengan monthly/yearly closing
6. Jika ada discrepancy → investigate
```

**Kegunaan:**
- Verifikasi kebenaran closing
- Deteksi kesalahan data
- Audit trail untuk external auditor

---

## 📊 Version Control

### Status Lifecycle:

```
DRAFT → ACTIVE → SUPERSEDED → ARCHIVED
```

- **DRAFT**: Baru dibuat, masih bisa diedit
- **ACTIVE**: Version yang sedang aktif (hanya 1 per periode)
- **SUPERSEDED**: Di-replace oleh version baru
- **ARCHIVED**: Archive lama (untuk history)

### Generate Version Baru:

```php
// Jika ada ACTIVE version, ubah jadi SUPERSEDED
// Buat version baru dengan version_number++
// Set version baru = ACTIVE
```

---

## 🔐 Lock Mechanism

**Locked Closing:**
- ✅ Bisa dibaca
- ❌ Tidak bisa diedit
- ❌ Tidak bisa dihapus
- ℹ️ Bisa buat version baru (jika ada perubahan)

**Lock Process:**
```php
$closingService->lockClosing(2024, 10, 'monthly');
```

---

## ✅ Testing Checklist

- [ ] Run migration untuk 2 tabel
- [ ] Test calculate monthly dengan sample data
- [ ] Test calculate yearly
- [ ] Test audit calculation
- [ ] Test compare with audit
- [ ] Test lock closing
- [ ] Test UI preview
- [ ] Test generate & save
- [ ] Test version control

---

## 🚀 Next Steps

1. Run migration:
   ```bash
   php artisan migrate --path=database/migrations/2025_11_10_120000_create_tr_acc_monthly_closing_table.php
   php artisan migrate --path=database/migrations/2025_11_10_120001_create_tr_acc_yearly_closing_table.php
   ```

2. Test dengan data sample

3. Implement version control logic

4. Add export to Excel/PDF

5. Add email notification saat closing selesai

---

**Created:** 10 November 2025  
**Version:** 1.0
