<?php
require_once __DIR__ . '/includes/auth.php';

// Wipe the data, expire the cookie, destroy the server-side session.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// A fresh session just to carry the confirmation message.
session_start();
$_SESSION['flash'] = ['type' => 'success', 'text' => 'You have been logged out.'];
header('Location: login.php');
exit;
