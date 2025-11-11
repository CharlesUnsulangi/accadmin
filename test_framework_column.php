<?php
/**
 * Test Framework Column in Application Management
 */

try {
    $conn = new PDO(
        'sqlsrv:Server=66.96.240.131,26402;Database=RCM_DEV_HGS_SB',
        'sa',
        'pfind@sqlserver'
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== TEST FRAMEWORK COLUMN ===\n\n";

    // Test 1: Check if framework column exists
    echo "1. Checking framework column...\n";
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'ms_admin_it_aplikasi' 
            AND COLUMN_NAME = 'framework'";
    $stmt = $conn->query($sql);
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "   ✓ Column exists: {$column['COLUMN_NAME']} ({$column['DATA_TYPE']}, max {$column['CHARACTER_MAXIMUM_LENGTH']})\n\n";
    } else {
        echo "   ✗ Column NOT found!\n\n";
        exit;
    }

    // Test 2: Update existing AccAdmin app with framework
    echo "2. Updating AccAdmin app with framework info...\n";
    $sql = "UPDATE ms_admin_it_aplikasi 
            SET framework = 'Laravel 11'
            WHERE CAST(apps_desc AS VARCHAR(MAX)) LIKE '%AccAdmin%'";
    $affected = $conn->exec($sql);
    echo "   ✓ Updated {$affected} row(s)\n\n";

    // Test 3: Display all applications with framework
    echo "3. Current applications with framework:\n";
    echo str_repeat("-", 100) . "\n";
    echo sprintf("%-40s | %-20s | %-30s\n", "Application", "Framework", "Status");
    echo str_repeat("-", 100) . "\n";

    $sql = "SELECT 
                apps_desc,
                framework,
                cek_non_aktif
            FROM ms_admin_it_aplikasi
            ORDER BY apps_desc";
    $stmt = $conn->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = ($row['cek_non_aktif'] == 0 || $row['cek_non_aktif'] === null) ? 'Active' : 'Inactive';
        echo sprintf("%-40s | %-20s | %-30s\n",
            substr($row['apps_desc'], 0, 40),
            $row['framework'] ?? '-',
            $status
        );
    }

    echo str_repeat("-", 100) . "\n";
    echo "\n✓ All tests completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
