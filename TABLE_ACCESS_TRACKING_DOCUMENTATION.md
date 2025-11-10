# Table Access Tracking System

## Overview
Sistem tracking akses table database yang mencatat setiap kali table diakses dari berbagai frontend (web, mobile, API, dll).

## Database Schema

### Table: `tr_admin_it_table_access_log`

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| table_name | VARCHAR(255) | Nama table yang diakses |
| access_type | VARCHAR(50) | Tipe akses: view, query, export, detail_view |
| frontend_type | VARCHAR(100) | Jenis frontend: Web - Chrome, Mobile - Android, API, dll |
| user_agent | VARCHAR(500) | User agent string lengkap |
| ip_address | VARCHAR(45) | IP address pengakses |
| user_id | BIGINT | ID user yang mengakses (nullable) |
| user_name | VARCHAR(100) | Nama user (nullable) |
| additional_info | TEXT | JSON data untuk info tambahan (nullable) |
| accessed_at | TIMESTAMP | Waktu akses |

**Indexes:**
- table_name
- access_type
- frontend_type
- user_id
- accessed_at

## Features

### 1. Automatic Access Logging
Setiap kali halaman Database Tables diakses, sistem otomatis mencatat:
- Table mana yang dilihat
- Dari frontend apa (browser, mobile app, API client)
- User siapa yang mengakses
- Kapan diakses
- IP address
- Device/browser details (dari user agent)

### 2. Frontend Type Detection
Sistem otomatis mendeteksi jenis frontend dari User Agent:

**Web Browsers:**
- Web - Chrome
- Web - Firefox
- Web - Safari
- Web - Edge
- Web - Opera
- Web - Other

**Mobile Devices:**
- Mobile - Android
- Mobile - iOS
- Mobile - Other

**API Clients:**
- API - Postman
- API - Insomnia
- API (generic)

**Others:**
- Bot/Crawler

### 3. Access Statistics Dashboard
URL: `/table-access-stats`

**Fitur:**
- Filter by time range (24h, 7d, 30d, all time)
- Overall statistics (total access, unique tables, unique users, frontend types)
- Most accessed tables (top 10 dengan ranking)
- Access by frontend type (dengan percentage)
- Recent access activity (15 terakhir)

### 4. Available Methods

#### TableAccessLog Model

```php
// Log manual access
TableAccessLog::logAccess(
    'table_name',
    'access_type',  // view, query, export, etc
    ['key' => 'value']  // additional info (optional)
);

// Get statistics for specific table
$stats = TableAccessLog::getTableStats('ms_acc_coa');
// Returns: total_access, unique_users, frontend_types, first_access, last_access

// Get most accessed tables
$top10 = TableAccessLog::getMostAccessedTables(10);

// Get access by frontend type
$byFrontend = TableAccessLog::getAccessByFrontend();
```

## Usage Examples

### Example 1: Manual Logging in Controller
```php
use App\Models\TableAccessLog;

public function show($tableName)
{
    TableAccessLog::logAccess(
        $tableName,
        'detail_view',
        [
            'from_page' => 'dashboard',
            'filters' => request()->all()
        ]
    );
    
    // Your logic here
}
```

### Example 2: Logging with Livewire
```php
use App\Models\TableAccessLog;

public function render()
{
    TableAccessLog::logAccess(
        'database_tables_page',
        'view',
        [
            'search' => $this->search,
            'sort_by' => $this->sortBy
        ]
    );
    
    return view('livewire.component');
}
```

### Example 3: Export Logging
```php
public function exportTable($tableName)
{
    TableAccessLog::logAccess(
        $tableName,
        'export',
        [
            'format' => 'excel',
            'rows' => 1000
        ]
    );
    
    // Export logic
}
```

## Implementation in DatabaseTables Component

File: `app/Livewire/DatabaseTables.php`

```php
public function render()
{
    // Automatically log page access
    TableAccessLog::logAccess(
        'database_tables_page',
        'view',
        [
            'search' => $this->search,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
            'per_page' => $this->perPage
        ]
    );
    
    return view('livewire.database-tables', [
        'tables' => $this->getTables(),
        'stats' => $this->getTableStats(),
    ])->layout('layouts.admin');
}
```

## Access Statistics Component

File: `app/Livewire/TableAccessStats.php`

### Properties:
- `timeRange`: Filter waktu (24h, 7d, 30d, all)
- `limit`: Jumlah data yang ditampilkan

### Methods:
- `getMostAccessedTables()`: Top accessed tables
- `getAccessByFrontend()`: Breakdown by frontend type
- `getRecentAccess()`: 15 akses terakhir
- `getOverallStats()`: Statistik keseluruhan

## Routes

```php
// View database tables (with automatic logging)
Route::get('/database-tables', \App\Livewire\DatabaseTables::class)
    ->name('database.tables');

// View access statistics
Route::get('/table-access-stats', \App\Livewire\TableAccessStats::class)
    ->name('table.access.stats');
```

## Menu Location

**Sidebar → Diagnostic Tools → Access Statistics**

## Benefits

1. **Usage Monitoring**: Tahu table mana yang paling sering diakses
2. **User Behavior**: Analisis pola penggunaan user
3. **Performance Planning**: Identifikasi table yang perlu optimasi
4. **Security Audit**: Track akses mencurigakan
5. **Platform Analytics**: Lihat platform mana yang paling banyak digunakan
6. **Time-based Analysis**: Filter by periode waktu

## Privacy & Performance Notes

- IP addresses disimpan untuk security audit
- User agent disimpan untuk platform analytics
- Gunakan indexes untuk performa query yang baik
- Consider cleanup policy untuk data lama (optional)
- Additional info dalam JSON untuk fleksibilitas

## Future Enhancements

1. Export access logs to CSV/Excel
2. Email alerts untuk akses mencurigakan
3. Grafik trends over time
4. Per-user access report
5. API endpoint untuk monitoring eksternal
6. Auto-cleanup old logs (retention policy)
7. Real-time dashboard dengan WebSocket

## Testing

Run test script:
```bash
php test_access_log.php
```

Akan mencatat sample access dari berbagai frontend dan menampilkan statistik.

## Monitoring Query Examples

```sql
-- Most accessed tables today
SELECT table_name, COUNT(*) as access_count
FROM tr_admin_it_table_access_log
WHERE CAST(accessed_at AS DATE) = CAST(GETDATE() AS DATE)
GROUP BY table_name
ORDER BY access_count DESC;

-- Access by frontend type
SELECT frontend_type, COUNT(*) as total
FROM tr_admin_it_table_access_log
GROUP BY frontend_type
ORDER BY total DESC;

-- User access pattern
SELECT user_name, COUNT(*) as total_access, 
       COUNT(DISTINCT table_name) as tables_accessed
FROM tr_admin_it_table_access_log
WHERE user_id IS NOT NULL
GROUP BY user_name
ORDER BY total_access DESC;

-- Hourly access pattern
SELECT DATEPART(hour, accessed_at) as hour, 
       COUNT(*) as access_count
FROM tr_admin_it_table_access_log
WHERE accessed_at >= DATEADD(day, -1, GETDATE())
GROUP BY DATEPART(hour, accessed_at)
ORDER BY hour;
```
