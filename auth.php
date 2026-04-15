<?php
$validUser = 'admin';
$validPass = 'password123';

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="POS Simulation Login"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required.';
    exit;
}

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

if ($user !== $validUser || $pass !== $validPass) {
    header('WWW-Authenticate: Basic realm="POS Simulation Login"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Invalid username or password.';
    exit;
}
?>