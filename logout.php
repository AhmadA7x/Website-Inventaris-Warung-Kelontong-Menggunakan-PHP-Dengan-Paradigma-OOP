<?php
// ============================================================
// FILE: logout.php
// Skrip untuk menghancurkan session dan mengarahkan ke halaman login
// ============================================================

session_start();

// Hapus semua data session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan sukses
header('Location: login.php?pesan=logout_sukses');
exit;
