<?php
// config/config.php

// Mulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database
require_once __DIR__ . '/database.php';

// Base URL
define('BASE_URL', 'http://localhost/absensi_siswa/');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Fungsi Check Login
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

// Fungsi Check Role
function check_role($required_role) {
    check_login();
    if ($_SESSION['role'] !== $required_role) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

// Fungsi Generate CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi Validasi CSRF Token
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("Invalid CSRF token");
    }
}

// Fungsi Clean Input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>