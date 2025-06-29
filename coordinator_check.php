<?php
session_start();

if (isset($_SESSION['coordinator_id'])) {
    // Coordinator is logged in
    $redirect = $_GET['redirect'] ?? 'dashboard.php';
    header("Location: $redirect");
    exit();
} else {
    // Not logged in - redirect to login page
    $redirect = $_GET['redirect'] ?? 'lab_schedule_create.php';
    header("Location: coordinator_login.php?redirect=" . urlencode($redirect));
    exit();
}
?>
