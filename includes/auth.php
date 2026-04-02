<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    $user = current_user();
    return $user && ($user['role'] ?? '') === 'admin';
}

function require_login() {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!current_user() || !is_admin()) {
        header('Location: login.php');
        exit;
    }
}
