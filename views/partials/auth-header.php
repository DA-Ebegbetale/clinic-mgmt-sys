<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-panel">
            <div class="auth-brand">
                <span>Clinic OS</span>
            </div>

            <?php require_once __DIR__ . '/flash.php'; ?>