<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!defined('ROOT_DIR')) {
        define('ROOT_DIR', dirname(__DIR__));
    }

    if (!defined('BASE_URL')) {
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $pagesPos = strpos($scriptPath, '/pages/');
        if ($pagesPos !== false) {
            $base = substr($scriptPath, 0, $pagesPos);
        } else {
            $base = dirname($scriptPath);
        }
        $base = rtrim(str_replace('\\', '/', $base), '/');
        define('BASE_URL', ($base === '.' || $base === '/') ? '' : $base);
    }

    $host = "localhost";
    $user = "root";
    $pass = "";
    $name = "game_db";

    $conn = new mysqli($host, $user, $pass, $name);
?>