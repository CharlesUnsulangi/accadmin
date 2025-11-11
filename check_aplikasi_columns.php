<?php
/**
 * Check ms_admin_it_aplikasi table columns
 */

try {
    $conn = new PDO(
        'sqlsrv:Server=66.96.240.131,26402;Database=RCM_DEV_HGS_SB',
        'sa',
        'pfind@sqlserver'
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CURRENT TABLE STRUCTURE: ms_admin_it_aplikasi ===\n\n";

    $sql = "SELECT 
                COLUMN_NAME, 
                DATA_TYPE, 
                CHARACTER_MAXIMUM_LENGTH, 
                IS_NULLABLE,
                COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'ms_admin_it_aplikasi' 
            ORDER BY ORDINAL_POSITION";

    $stmt = $conn->query($sql);

    echo sprintf("%-30s | %-15s | %-10s | %-10s | %s\n", 
        "Column", "Type", "Length", "Nullable", "Default");
    echo str_repeat("-", 100) . "\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-30s | %-15s | %-10s | %-10s | %s\n",
            $row['COLUMN_NAME'],
            $row['DATA_TYPE'],
            $row['CHARACTER_MAXIMUM_LENGTH'] ?? 'N/A',
            $row['IS_NULLABLE'],
            $row['COLUMN_DEFAULT'] ?? 'NULL'
        );
    }

    echo "\n=== END ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
