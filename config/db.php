<?php
// config/db.php
if (session_status() === PHP_SESSION_NONE) session_start();

define('BASE_URL', 'http://localhost/simple-event-portal');

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'simple_event_portal';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    die('DB Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

function esc($v) {
    global $mysqli;
    return htmlspecialchars($mysqli->real_escape_string(trim($v)));
}
?>
