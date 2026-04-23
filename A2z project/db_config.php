<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'a2z';
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    exit;
}
$mysqli->set_charset('utf8mb4');
return $mysqli;
