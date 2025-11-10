# Database Connection Selector - Docs Tables

## Overview
Fitur untuk memilih database connection yang berbeda pada halaman Docs-Tables, memungkinkan user untuk melihat struktur tabel dari berbagai database tanpa perlu mengubah konfigurasi aplikasi.

## Features

### 1. Multiple Database Connections
Aplikasi sekarang mendukung koneksi ke beberapa database SQL Server secara bersamaan:

- **sqlsrv** - Default connection (RCM_DEV_HGS_SB)
- **sqlsrv_rcm_dev** - RCM Development Database
- **sqlsrv_rcm_prod** - RCM Production Database

**Note**: Hanya database yang accessible yang akan ditampilkan di dropdown. Database yang tidak dapat diakses (misal: credentials berbeda atau tidak exist) akan otomatis di-skip saat mount.

### 2. Dynamic Connection Switching with Auto-Validation
- Dropdown selector di header halaman Docs-Tables
- Real-time switching tanpa reload page (Livewire)
- URL persistence (query string: `?selectedConnection=sqlsrv_acc`)
- Auto-close modal saat switch database untuk menghindari konfusi

### 3. Connection-Aware Queries
Semua query database menggunakan connection yang dipilih:
- Table list
- Table schema
- Column details
- Primary/Foreign keys
- Extended properties
- Last record date

## Configuration

### Database Connections (config/database.php)

```php
'connections' => [
    'sqlsrv' => [
        'driver' => 'sqlsrv',
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '1433'),
        'database' => env('DB_DATABASE', 'accadmin'),
        // ... other config
    ],
    
    'sqlsrv_rcm_dev' => [
        'driver' => 'sqlsrv',
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'database' => 'RCM_DEV_HGS_SB',
        // ... same credentials
    ],
    
    'sqlsrv_rcm_prod' => [
        'driver' => 'sqlsrv',
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'database' => 'RCM_PROD_HGS_SB',
        // ... same credentials
    ],
    
    'sqlsrv_acc' => [
        'driver' => 'sqlsrv',
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'database' => 'ACC',
        // ... same credentials
    ],
]
```

## Implementation Details

### Livewire Component (DocsTables.php)

#### Properties
```php
public $selectedConnection = 'sqlsrv'; // Default connection
public $availableConnections = []; // Populated from config
```

#### Mount Method
```php
public function mount()
{
    // Get available SQL Server connections from config and test accessibility
    $allConnections = config('database.connections');
    $accessibleConnections = [];
    
    foreach ($allConnections as $name => $config) {
        // Only include SQL Server connections
        if (!isset($config['driver']) || $config['driver'] !== 'sqlsrv') {
            continue;
        }
        
        // Test if connection is accessible
        try {
            DB::connection($name)->select('SELECT 1');
            $dbName = $config['database'] ?? $name;
            $accessibleConnections[$name] = $dbName;
        } catch (\Exception $e) {
            // Skip connections that fail
            \Log::warning("Database connection '{$name}' is not accessible: " . $e->getMessage());
        }
    }
    
    $this->availableConnections = $accessibleConnections;

    // Set default if not in query string or not accessible
    if (!$this->selectedConnection || !isset($this->availableConnections[$this->selectedConnection])) {
        $this->selectedConnection = config('database.default');
        
        // If default is not accessible, use first available
        if (!isset($this->availableConnections[$this->selectedConnection]) && !empty($this->availableConnections)) {
            $this->selectedConnection = array_key_first($this->availableConnections);
        }
    }
}
```

**Key Features**:
- Auto-tests each connection dengan `SELECT 1` query
- Hanya menampilkan database yang accessible
- Log warning untuk database yang gagal (cek `storage/logs/laravel.log`)
- Fallback ke default atau first available connection

#### Connection-Aware Queries
All database queries use the selected connection:

```php
// Example: Get table info
DB::connection($this->selectedConnection)->selectOne("...", [$tableName]);

// Example: Get columns
DB::connection($this->selectedConnection)->select("...", [$tableName]);

// Example: Extended properties
DB::connection($this->selectedConnection)->select("
    SELECT CAST(value AS NVARCHAR(MAX)) as value 
    FROM sys.extended_properties 
    WHERE major_id = OBJECT_ID(?) 
    AND name = 'MS_Description'
    AND minor_id = 0
", [$tableName]);
```

#### Lifecycle Hooks
```php
public function updatingSelectedConnection()
{
    $this->resetPage();
    $this->closeModal(); // Close any open modal when switching
}

public function switchConnection($connection)
{
    // Validate connection is accessible before switching
    if (!isset($this->availableConnections[$connection])) {
        session()->flash('error', 'Database connection not accessible: ' . $connection);
        return;
    }
    
    $this->selectedConnection = $connection;
    $this->resetPage();
    $this->closeModal();
}
```

**Error Handling**:
- Validates connection exists before switching
- Shows error flash message jika connection tidak accessible
- Prevents crash dari invalid connections

### View (docs-tables.blade.php)

#### Flash Messages
```blade
<!-- Flash Messages -->
@if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

#### Database Selector Dropdown
```blade
<div style="min-width: 250px;">
    <label class="form-label small text-muted mb-1">
        <i class="fas fa-database me-1"></i>Database Connection
    </label>
    <select wire:model.live="selectedConnection" class="form-select form-select-lg">
        @foreach($availableConnections as $connName => $dbName)
            <option value="{{ $connName }}">{{ $dbName }}</option>
        @endforeach
    </select>
</div>
```

#### Stats Display with Current DB
```blade
<div class="alert alert-info mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            <strong>Total Tables:</strong> {{ number_format($total) }}
        </div>
        <div class="text-end small">
            <i class="fas fa-database text-muted me-1"></i>
            <strong>{{ $availableConnections[$selectedConnection] ?? $selectedConnection }}</strong>
        </div>
    </div>
</div>
```

## Usage

### For Users

1. **Navigate to Docs-Tables page**: http://127.0.0.1:8001/docs-tables

2. **Select Database**: 
   - Gunakan dropdown di pojok kanan atas
   - Pilih database yang ingin dilihat:
     - RCM_DEV_HGS_SB (Development)
     - RCM_PROD_HGS_SB (Production)
     - ACC (Accounting)

3. **Browse Tables**:
   - List tabel akan auto-update sesuai database yang dipilih
   - Jumlah tabel ditampilkan di stats
   - Nama database aktif ditampilkan di pojok kanan stats

4. **View Schema**:
   - Klik tombol "View Schema" pada tabel
   - Schema ditampilkan dari database yang dipilih
   - Extended properties dibaca dari database terpilih

5. **URL Sharing**:
   - URL otomatis update dengan parameter `?selectedConnection=xxx`
   - Share URL untuk langsung membuka database tertentu
   - Contoh: `/docs-tables?selectedConnection=sqlsrv_acc`

### For Developers

#### Add New Database Connection

1. Edit `config/database.php`:
```php
'sqlsrv_newdb' => [
    'driver' => 'sqlsrv',
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'database' => 'NEW_DATABASE_NAME',
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'encrypt' => env('DB_ENCRYPT', 'yes'),
    'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', true),
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => true,
    ],
],
```

2. Dropdown akan otomatis mendeteksi dan menampilkan connection baru
3. Pastikan credentials dan server sama dengan connection lain

#### Use Connection in Other Components

```php
// Get available SQL Server connections
$connections = collect(config('database.connections'))
    ->filter(fn($config) => $config['driver'] === 'sqlsrv')
    ->keys();

// Use specific connection
DB::connection('sqlsrv_acc')->table('some_table')->get();

// Dynamic connection
$conn = $this->selectedConnection;
DB::connection($conn)->select("SELECT * FROM table");
```

## Important Notes

### 1. IT Documentation Storage
- Notes ALWAYS disimpan ke IT Docs di **default connection** (sqlsrv)
- Ini memastikan dokumentasi terpusat di satu tempat
- Extended Properties disimpan ke database yang sedang dipilih

### 2. Extended Properties
- Setiap database memiliki Extended Properties sendiri
- MS_Description tersimpan langsung di sys.extended_properties database terpilih
- Extended Properties portable jika database di-backup/restore

### 3. Security & Access Control
- Semua database menggunakan credentials yang sama (dari .env)
- **Auto-validation**: Hanya database yang accessible yang ditampilkan
- Database yang gagal login akan di-skip dengan warning log
- Pastikan user memiliki akses read ke database yang ingin ditampilkan
- Extended Property write memerlukan ALTER permission

### 4. Performance
- Connection pool dikelola Laravel secara otomatis
- Multiple connections tidak membebani memory
- Lazy loading - connection dibuat saat digunakan
- **Connection test**: Setiap mount melakukan lightweight `SELECT 1` test

### 5. Error Handling
- Jika connection gagal, fallback ke default
- Error message jelas menunjukkan database mana yang bermasalah
- Modal auto-close untuk menghindari stale data
- **Graceful degradation**: App tetap berfungsi meski beberapa database down
- Log semua connection failures ke `storage/logs/laravel.log`

## Testing

### Test Connection Switching
```bash
# Test all databases accessible
php artisan tinker

# In tinker:
DB::connection('sqlsrv')->select('SELECT DB_NAME()');
DB::connection('sqlsrv_rcm_dev')->select('SELECT DB_NAME()');
DB::connection('sqlsrv_rcm_prod')->select('SELECT DB_NAME()');
DB::connection('sqlsrv_acc')->select('SELECT DB_NAME()');
```

### Verify Extended Properties
```sql
-- Check Extended Properties in selected database
SELECT 
    OBJECT_NAME(major_id) as TableName,
    CAST(value AS NVARCHAR(MAX)) as Description
FROM sys.extended_properties
WHERE name = 'MS_Description'
AND minor_id = 0
ORDER BY TableName;
```

## Future Enhancements

### Potential Features
1. **Connection Health Check**: Indicator showing if database is online
2. **Recent Connections**: Remember last used database per user
3. **Database Comparison**: Side-by-side table comparison
4. **Cross-Database Search**: Search tables across all databases
5. **Connection Groups**: Group connections by environment (Dev/Prod)
6. **Custom Connections**: User-defined connections (dengan encryption)

### Architecture Improvements
1. **Connection Caching**: Cache available connections
2. **Async Loading**: Load table counts asynchronously
3. **Connection Pooling**: Better connection pool management
4. **Permission Check**: Verify user has access before showing connection

## Troubleshooting

### Problem: Database tidak muncul di dropdown
**Solution**: 
- Pastikan connection ada di `config/database.php`
- Pastikan driver adalah 'sqlsrv'
- Check typo di database name
- **Verify credentials**: Database mungkin memerlukan username/password berbeda
- **Check logs**: Lihat `storage/logs/laravel.log` untuk error detail
- **Test manually**: Coba connect via SSMS dengan credentials yang sama

### Problem: Error saat switch connection
**Solution**:
- Verify database exists di SQL Server
- Check user permissions (minimal SELECT permission)
- Verify server/port accessible
- Check firewall settings
- **Connection timeout**: Increase timeout di config database
- **Network issue**: Ping server untuk verify connectivity

### Problem: "Login failed for user 'sa'" Error
**Solution**:
- Database memerlukan credentials berbeda dari default
- Buat connection dengan username/password specific untuk database tersebut:
```php
'sqlsrv_specific' => [
    'driver' => 'sqlsrv',
    'host' => 'different-server.com',
    'database' => 'SPECIFIC_DB',
    'username' => 'specific_user',  // Different user
    'password' => 'specific_pass',   // Different password
    // ... other config
],
```
- Atau comment out database yang tidak accessible untuk menghindari error
- App akan otomatis skip database yang gagal connect dan log ke file

### Problem: Extended Property tidak tersimpan
**Solution**:
- User needs ALTER permission on table
- Check connection masih aktif
- Verify table exists di database terpilih

### Problem: Stale data setelah switch
**Solution**:
- Modal auto-close seharusnya handle ini
- Clear browser cache
- Force refresh dengan Ctrl+F5

## Related Documentation
- [DATABASE_TABLES_DOCUMENTATION.md](DATABASE_TABLES_DOCUMENTATION.md)
- [COA_STRUCTURE_DOCUMENTATION.md](COA_STRUCTURE_DOCUMENTATION.md)
- Laravel Database Documentation: https://laravel.com/docs/database
- SQL Server Extended Properties: https://docs.microsoft.com/en-us/sql/relational-databases/system-stored-procedures/sp-addextendedproperty-transact-sql
