<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TABLE STRUCTURE: tr_admin_it_aplikasi_table ===\n\n";

$columns = DB::select("
    SELECT 
        COLUMN_NAME, 
        DATA_TYPE, 
        CHARACTER_MAXIMUM_LENGTH,
        IS_NULLABLE,
        COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'tr_admin_it_aplikasi_table' 
    ORDER BY ORDINAL_POSITION
");

foreach($columns as $col) {
    $length = $col->CHARACTER_MAXIMUM_LENGTH ? "({$col->CHARACTER_MAXIMUM_LENGTH})" : '';
    $nullable = $col->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
    echo sprintf("%-30s %-20s %s\n", 
        $col->COLUMN_NAME, 
        $col->DATA_TYPE . $length,
        $nullable
    );
}

echo "\n=== CHECKING FOR PRIMARY KEY ===\n\n";

$pk = DB::select("
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE OBJECTPROPERTY(OBJECT_ID(CONSTRAINT_SCHEMA + '.' + CONSTRAINT_NAME), 'IsPrimaryKey') = 1
    AND TABLE_NAME = 'tr_admin_it_aplikasi_table'
");

if (!empty($pk)) {
    echo "Primary Key: " . $pk[0]->COLUMN_NAME . "\n";
} else {
    echo "No primary key found\n";
}

echo "\n=== CHECKING FOR UNIQUE CONSTRAINTS ===\n\n";

$unique = DB::select("
    SELECT 
        c.COLUMN_NAME,
        tc.CONSTRAINT_NAME
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE c
        ON tc.CONSTRAINT_NAME = c.CONSTRAINT_NAME
    WHERE tc.TABLE_NAME = 'tr_admin_it_aplikasi_table'
    AND tc.CONSTRAINT_TYPE = 'UNIQUE'
");

if (!empty($unique)) {
    foreach($unique as $u) {
        echo "Unique: {$u->COLUMN_NAME} ({$u->CONSTRAINT_NAME})\n";
    }
} else {
    echo "No unique constraints found\n";
}

echo "\n=== SAMPLE DATA (First 5 records) ===\n\n";

$samples = DB::table('tr_admin_it_aplikasi_table')
    ->select('tr_aplikasi_table_id', 'ms_aplikasi_id', 'id', 'table_name')
    ->limit(5)
    ->get();

foreach($samples as $sample) {
    echo "tr_aplikasi_table_id: {$sample->tr_aplikasi_table_id}\n";
    echo "ms_aplikasi_id: " . ($sample->ms_aplikasi_id ?? 'NULL') . "\n";
    echo "id: {$sample->id}\n";
    echo "table_name: {$sample->table_name}\n";
    echo "---\n";
}

echo "\n=== CHECKING ID PATTERNS ===\n\n";

$idPatterns = DB::select("
    SELECT 
        COUNT(*) as total,
        COUNT(DISTINCT CAST(tr_aplikasi_table_id AS VARCHAR(MAX))) as distinct_tr_aplikasi_table_id,
        COUNT(DISTINCT CAST(id AS VARCHAR(MAX))) as distinct_id,
        COUNT(DISTINCT CAST(table_name AS VARCHAR(MAX))) as distinct_table_name,
        COUNT(ms_aplikasi_id) as ms_aplikasi_id_not_null
    FROM tr_admin_it_aplikasi_table
");

$pattern = $idPatterns[0];
echo "Total records: {$pattern->total}\n";
echo "Distinct tr_aplikasi_table_id: {$pattern->distinct_tr_aplikasi_table_id}\n";
echo "Distinct id: {$pattern->distinct_id}\n";
echo "Distinct table_name: {$pattern->distinct_table_name}\n";
echo "ms_aplikasi_id NOT NULL: {$pattern->ms_aplikasi_id_not_null}\n";

echo "\n=== RECOMMENDATION ===\n\n";

if ($pattern->distinct_table_name == $pattern->total) {
    echo "✓ table_name is UNIQUE - Can be used as natural key\n";
} else {
    echo "✗ table_name has duplicates - NOT unique\n";
}

if ($pattern->distinct_tr_aplikasi_table_id == $pattern->total) {
    echo "✓ tr_aplikasi_table_id is UNIQUE - Can be used as primary key\n";
} else {
    echo "✗ tr_aplikasi_table_id has duplicates\n";
}

if ($pattern->ms_aplikasi_id_not_null == 0) {
    echo "⚠ ms_aplikasi_id is always NULL - Not being used\n";
} else {
    echo "✓ ms_aplikasi_id has values in some records\n";
}

echo "\nDone!\n";
