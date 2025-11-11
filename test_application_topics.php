<?php
/**
 * Test Application Topics Functionality
 */

try {
    $conn = new PDO(
        'sqlsrv:Server=66.96.240.131,26402;Database=RCM_DEV_HGS_SB',
        'sa',
        'pfind@sqlserver'
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== TEST APPLICATION TOPICS ===\n\n";

    // Get AccAdmin app ID
    echo "1. Getting AccAdmin application ID...\n";
    $sql = "SELECT ms_admin_it_aplikasi_id, apps_desc 
            FROM ms_admin_it_aplikasi 
            WHERE CAST(apps_desc AS VARCHAR(MAX)) LIKE '%AccAdmin%'";
    $stmt = $conn->query($sql);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        echo "   ✗ AccAdmin application not found!\n";
        exit;
    }

    $appId = $app['ms_admin_it_aplikasi_id'];
    echo "   ✓ Found: {$app['apps_desc']} (ID: {$appId})\n\n";

    // Get next topic ID
    echo "2. Getting next topic ID...\n";
    $sql = "SELECT ISNULL(MAX(ms_admin_it_topic), 0) + 1 as next_id FROM ms_admin_it_aplikasi_topic";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextId = $result['next_id'];
    echo "   ✓ Next topic ID: {$nextId}\n\n";

    // Sample topics to insert
    $topics = [
        ['topic_desc' => 'Chart of Accounts Management', 'value_priority' => 1],
        ['topic_desc' => 'Transaction Entry & Recording', 'value_priority' => 2],
        ['topic_desc' => 'Monthly/Yearly Closing Process', 'value_priority' => 3],
        ['topic_desc' => 'Balance Sheet Reporting', 'value_priority' => 4],
        ['topic_desc' => 'Master Data Management', 'value_priority' => 5],
        ['topic_desc' => 'Database Table Administration', 'value_priority' => 6],
        ['topic_desc' => 'Stored Procedures & Functions', 'value_priority' => 7],
        ['topic_desc' => 'User Access Control', 'value_priority' => 8],
    ];

    echo "3. Inserting sample topics...\n";
    $conn->beginTransaction();

    foreach ($topics as $topic) {
        $sql = "INSERT INTO ms_admin_it_aplikasi_topic 
                (topic_desc, value_priority, ms_admin_it_aplikasi_id)
                VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $topic['topic_desc'],
            $topic['value_priority'],
            $appId
        ]);

        $insertedId = $conn->lastInsertId();
        echo "   ✓ [{$insertedId}] {$topic['topic_desc']} (Priority: {$topic['value_priority']})\n";
    }

    $conn->commit();
    echo "\n   Total topics inserted: " . count($topics) . "\n\n";

    // Display all topics for AccAdmin
    echo "4. Topics for AccAdmin:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-5s | %-45s | %-10s\n", "ID", "Topic Description", "Priority");
    echo str_repeat("-", 80) . "\n";

    $sql = "SELECT ms_admin_it_topic, topic_desc, value_priority 
            FROM ms_admin_it_aplikasi_topic 
            WHERE CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?
            ORDER BY value_priority ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$appId]);

    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-5s | %-45s | %-10s\n",
            $row['ms_admin_it_topic'],
            substr($row['topic_desc'], 0, 45),
            $row['value_priority'] ?? '-'
        );
        $count++;
    }

    echo str_repeat("-", 80) . "\n";
    echo "Total topics: {$count}\n";

    echo "\n✓ All tests completed successfully!\n";
    echo "\nNow you can:\n";
    echo "1. Go to /applications page\n";
    echo "2. Click 'View Topics' button (list icon) on AccAdmin row\n";
    echo "3. See all {$count} topics in the modal\n";
    echo "4. Add, edit, or delete topics\n";

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
