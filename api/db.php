<?php
// api/db.php
error_reporting(0); //in prod 
// error_reporting(E_ALL); //in dev
// ini_set('display_errors', 1); // in dev
// The CORS blocks should be placed individually in API endpoint files.

$host = '127.0.0.1'; //or localhost will work too 
$db_name = 'apms_db';
$username = 'root'; // default xampp
$password = ''; // default xampp

try {
    $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //this will make sure if error occurs it gets display.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //fetch mode, the request will come more cleaner.
        PDO::ATTR_EMULATE_PREPARES   => false, //Prepared statements (security) Protects against SQL injection
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    // $pdo is now your database handle

} catch (PDOException $e) { //if error occurs 
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => 'Database connection failed'
    ]);
    exit;
}


// we are connecting db with pdo
// PDO = PHP Data Objects

// 👉 It’s just a tool (library) in PHP that lets you talk to databases safely and cleanly.