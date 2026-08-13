<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_url($url) {
    $sep = (strpos($url, '?') === false) ? '?' : '&';
    return $url . $sep . 'csrf_token=' . urlencode(csrf_token());
}

function csrf_verify() {
    $sent = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? '');
    if (empty($_SESSION['csrf_token']) || $sent === '' || !hash_equals($_SESSION['csrf_token'], $sent)) {
        die('Permintaan tidak valid (CSRF token tidak cocok).');
    }
}
