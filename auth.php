<?php
// auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getLoggedInUser() {
    return $_SESSION['user'] ?? null;
}

function requireAuth() {
    if (!getLoggedInUser()) {
        header("Location: login.php");
        exit;
    }
}