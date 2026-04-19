<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

echo "<h2>Database Connection Test</h2>";

if (!$pdo) {
    die("<h3 style='color:red;'>❌ DB connection failed!</h3>");
} else {
    echo "<h3 style='color:green;'>✅ DB Connected Successfully</h3>";
}

echo "<hr>";
echo "<h3>Testing 'users' table:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM users");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($data)) {
        echo "<p style='color:orange;'>⚠️ Table 'users' exists but is completely empty.</p>";
    } else {
        echo "<p style='color:green;'>✅ 'users' table contains " . count($data) . " rows.</p>";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Query Failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Testing 'pollution_data' table:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM pollution_data");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($data)) {
        echo "<p style='color:orange;'>⚠️ Table 'pollution_data' exists but is completely empty.</p>";
    } else {
        echo "<p style='color:green;'>✅ 'pollution_data' table contains " . count($data) . " rows.</p>";
    }

} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Query Failed: " . $e->getMessage() . "</p>";
}

?>
