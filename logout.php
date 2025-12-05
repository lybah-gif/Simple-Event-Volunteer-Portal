<?php
// logout.php
require_once __DIR__ . '/config/db.php';
session_unset();
session_destroy();
header('Location: /simple-event-portal/index.php');
exit;
