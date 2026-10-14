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

    // Conexão Híbrida: Tenta MySQL Local (Wamp); se não estiver disponível, usa a Nuvem
    $conn = null;
    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']) || php_sapi_name() === 'cli';

    if ($isLocal) {
        $conn = @new mysqli("localhost", "root", "", "game_db");
        if ($conn->connect_error) {
            $conn = null;
        }
    }

    // Se estiver na nuvem ou o MySQL local estiver desligado, conecta no Alwaysdata
    if (!$conn) {
        $host = "mysql-pijogo2026.alwaysdata.net";
        $user = "pijogo2026";
        $pass = "Rayne147585**";
        $name = "pijogo2026_game_db";
        $conn = new mysqli($host, $user, $pass, $name);
    }

    if ($conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }
?>