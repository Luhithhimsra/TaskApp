// PDO connection — configured for Laragon local environment
<?php

$sName   = "localhost";
$uName   = "root";
$pass    = "";          // ← change to your MySQL password if needed
$db_name = "task_management_db";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name;charset=utf8mb4", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
