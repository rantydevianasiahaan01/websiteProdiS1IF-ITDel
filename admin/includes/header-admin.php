<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$page_title = $page_title ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - HIMASTI IT Del</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/web-himasti/admin/assets/css/admin.css">
</head>
<body>