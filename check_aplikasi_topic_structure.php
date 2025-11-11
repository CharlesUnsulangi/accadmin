<?php
/**
 * Check tr_admin_aplikasi_topic table structure
 */

try {
    $conn = new PDO(
        'sqlsrv:Server=66.96.240.131,26402;Database=RCM_DEV_HGS_SB',
        'sa',
        'pfind@sqlserver'
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== TABLE STRUCTURE: ms_admin_it_aplikasi_topic ===\n\n";

    // Check if table exists
    $checkTable = "SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ms_admin_it_aplikasi_topic'";
    $stmt = $conn->query($checkTable);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exists['cnt'] == 0) {
        echo "Table ms_admin_it_aplikasi_topic NOT FOUND!\n";
        exit;
    }

    // Get columns
    $sql = "SELECT 
                COLUMN_NAME, 
                DATA_TYPE, 
                CHARACTER_MAXIMUM_LENGTH, 
                IS_NULLABLE,
                COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'ms_admin_it_aplikasi_topic' 
            ORDER BY ORDINAL_POSITION";

    $stmt = $conn->query($sql);

    echo sprintf("%-35s | %-15s | %-10s | %-10s\n", 
        "Column", "Type", "Length", "Nullable");
    echo str_repeat("-", 90) . "\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-35s | %-15s | %-10s | %-10s\n",
            $row['COLUMN_NAME'],
            $row['DATA_TYPE'],
            $row['CHARACTER_MAXIMUM_LENGTH'] ?? 'N/A',
            $row['IS_NULLABLE']
        );
    }

    // Sample data
    echo "\n=== SAMPLE DATA ===\n\n";
    $sql = "SELECT TOP 10 * FROM ms_admin_it_aplikasi_topic ORDER BY ms_admin_it_topic";
    $stmt = $conn->query($sql);
    
    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        echo "Record {$count}:\n";
        foreach ($row as $key => $value) {
            echo "  {$key}: " . ($value ?? 'NULL') . "\n";
        }
        echo "\n";
    }

    if ($count == 0) {
        echo "No data found in table.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
