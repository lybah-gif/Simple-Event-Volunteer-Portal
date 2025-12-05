<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Simple Event Volunteer Portal</title>
  <link rel="stylesheet" href="/simple-event-portal/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="container">
