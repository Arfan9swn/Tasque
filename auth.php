<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ./login.php');
        exit;
    }
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_username(): ?string {
    return isset($_SESSION['username']) ? (string)$_SESSION['username'] : null;
}

function current_name(): ?string {
    return isset($_SESSION['name']) ? (string)$_SESSION['name'] : null;
}