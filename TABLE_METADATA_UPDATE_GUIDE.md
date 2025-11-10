# Table Metadata Update System

## Overview
Sistem untuk mengupdate metadata table database (jumlah record, tanggal pertama/terakhir) dengan berbagai metode.

---

## 📊 Cara Update Metadata

### 1. **Via Web Interface (Paling Mudah)**

#### Update Semua Table Sekaligus
1. Buka halaman: `/database-tables`
2. Klik tombol **"Update All Metadata"** (hijau) di atas tabel
3. Tunggu proses selesai (ada loading spinner)
4. Muncul notifikasi sukses dengan jumlah yang diupdate

#### Update Table Individual
1. Buka halaman: `/database-tables`
2. Cari table yang ingin diupdate
3. Klik tombol **sync icon** (🔄) di kolom "Actions"
4. Metadata table tersebut akan diupdate

**Screenshot Interface:**
```
┌─────────────────────────────────────────────────────────────┐
│ [Database Tables]              [🔄 Update All Metadata] [...│
├─────────────────────────────────────────────────────────────┤
│ Table Name    │ Records  │ First Record │ Last Record │ 🔄  │
├─────────────────────────────────────────────────────────────┤
│ ms_acc_coa    │ 501      │ 2020-01-15  │ 2024-12-20  │ [🔄] │
│ tr_acc_...    │ 956,663  │ 2019-05-10  │ 2024-12-31  │ [🔄] │
└─────────────────────────────────────────────────────────────┘
```

---

### 2. **Via Artisan Command (Untuk Automation)**

#### Update Semua Table
```bash
php artisan metadata:update --all
```

Output:
```
Fetching all tables from database...
Found 1267 tables
[▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

✓ Complete!
┌─────────┬───────┐
│ Status  │ Count │
├─────────┼───────┤
│ Added   │ 5     │
│ Updated │ 1262  │
│ Errors  │ 0     │
└─────────┴───────┘
```

#### Update Table Spesifik
```bash
php artisan metadata:update ms_acc_coa
```

Output:
```
Updating: ms_acc_coa
✓ Updated! Records: 501
```

#### Update Beberapa Table
```bash
# Buat script batch
php artisan metadata:update ms_acc_coa
php artisan metadata:update tr_acc_closing_d
php artisan metadata:update users
```

---

### 3. **Via Scheduled Task (Auto Update)**

Edit file `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Update semua table metadata setiap hari jam 2 pagi
    $schedule->command('metadata:update --all')
             ->dailyAt('02:00')
             ->withoutOverlapping();
    
    // Atau setiap 6 jam
    $schedule->command('metadata:update --all')
             ->everySixHours();
    
    // Atau setiap Minggu
    $schedule->command('metadata:update --all')
             ->weekly()
             ->sundays()
             ->at('03:00');
}
```

**Aktifkan Windows Task Scheduler:**
```bash
# Buat batch file: update_metadata.bat
cd C:\ProjectSoftwareCWU\laravel\AccAdmin
php artisan schedule:run
```

Kemudian set di Windows Task Scheduler untuk run setiap menit.

---

### 4. **Via PHP Script (Manual Run)**

Gunakan script yang sudah ada: `populate_table_metadata.php`

```bash
php populate_table_metadata.php
```

Script ini akan:
- Insert table baru
- Update existing tables
- Skip yang error

---

## 🔄 Apa yang Diupdate?

Setiap update akan refresh data berikut:

| Field | Description | Example |
|-------|-------------|---------|
| `record` | Jumlah record terkini di table | 956,663 |
| `record_date_start` | Tanggal record pertama | 2019-05-10 |
| `record_date_last` | Tanggal record terakhir | 2024-12-31 |
| `date_updated` | Waktu terakhir metadata diupdate | 2025-11-10 15:30:45 |

---

## ⚙️ Implementasi Teknis

### Livewire Component Methods

```php
// File: app/Livewire/DatabaseTables.php

// Update all tables
public function updateAllMetadata()
{
    // Loops through all tables
    // Updates record count, date range
    // Shows flash message
}

// Update single table
public function updateTableMetadata($tableName)
{
    // Counts records for specific table
    // Gets min/max date from date columns
    // Updates database
}
```

### Artisan Command

```php
// File: app/Console/Commands/UpdateTableMetadata.php

// Signature
protected $signature = 'metadata:update {table?} {--all}';

// Usage
php artisan metadata:update           # Show help
php artisan metadata:update table_name  # Update specific
php artisan metadata:update --all      # Update all
```

---

## 📅 Rekomendasi Schedule

### Development
- **Manual update** saat ada perubahan besar
- Via web interface saat testing

### Production
- **Daily update** jam 2 pagi (low traffic)
- **Weekly full update** untuk deep scan
- **On-demand** via web untuk table tertentu

### High-Traffic Applications
- **Hourly update** untuk table transaksional
- **Daily update** untuk master data
- **Real-time** untuk critical tables (via trigger)

---

## 🛡️ Error Handling

Semua method sudah include error handling:

```php
try {
    // Update metadata
    $count = DB::table($tableName)->count();
    // ...
} catch (\Exception $e) {
    session()->flash('error', 'Error: ' . $e->getMessage());
    return;
}
```

**Common Errors:**
- Table tidak exists → Skip
- Permission denied → Log error
- Invalid date column → Use NULL
- SQL timeout → Retry atau skip

---

## 🎯 Best Practices

### 1. **Update Frequency**
```php
// Transactional tables: Often
metadata:update tr_acc_closing_d  // Daily

// Master data: Rarely
metadata:update ms_acc_coa  // Weekly or on-demand

// System tables: Very rarely
metadata:update users  // On-demand only
```

### 2. **Performance**
- Update saat traffic rendah (malam/dini hari)
- Batch update pakai `--all` lebih efisien dari individual
- Monitor execution time untuk table besar

### 3. **Monitoring**
```sql
-- Check last update time
SELECT table_name, date_updated 
FROM tr_admin_it_aplikasi_table 
ORDER BY date_updated DESC;

-- Find outdated metadata (>7 days)
SELECT table_name, DATEDIFF(day, date_updated, GETDATE()) as days_old
FROM tr_admin_it_aplikasi_table 
WHERE date_updated < DATEADD(day, -7, GETDATE())
ORDER BY days_old DESC;
```

### 4. **Logging**
Enable logging untuk track update history:
```php
\Log::info("Metadata updated for {$tableName}", [
    'records' => $count,
    'date_range' => [$dateStart, $dateLast],
    'user' => auth()->user()?->name
]);
```

---

## 🔧 Troubleshooting

### Problem: Button tidak muncul
**Solution:** Refresh browser, clear cache

### Problem: Update timeout
**Solution:** 
```php
// Increase timeout di config/database.php
'options' => [
    PDO::ATTR_TIMEOUT => 300  // 5 minutes
]
```

### Problem: Permission error
**Solution:** Check database user permissions
```sql
GRANT SELECT, UPDATE, INSERT ON tr_admin_it_aplikasi_table TO your_user;
```

### Problem: Date tidak terupdate
**Solution:** Table tidak punya kolom date, will show NULL (normal)

---

## 📊 Monitoring Dashboard

Lihat statistik update di halaman Access Statistics:
- URL: `/table-access-stats`
- Shows: Most updated tables, update frequency, errors

---

## 🚀 Quick Reference

```bash
# Command Line
php artisan metadata:update --all              # Update all
php artisan metadata:update table_name         # Update one
php populate_table_metadata.php                # Full populate

# Web Interface
/database-tables                               # View & update
Click "Update All Metadata"                    # Batch update
Click individual sync icon                     # Single update

# Schedule (add to Kernel.php)
$schedule->command('metadata:update --all')->daily();
```

---

## 📞 Support

Jika ada masalah:
1. Check error message di web interface
2. Run `php artisan metadata:update table_name` untuk detail error
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database connection dan permissions

---

Metadata selalu up-to-date = Dashboard selalu akurat! 🎯
