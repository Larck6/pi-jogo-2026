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

    // Carrega o arquivo de configuração seguro se existir na máquina (não vai para o Git)
    $configFile = __DIR__ . '/config.local.php';
    if (file_exists($configFile)) {
        require_once $configFile;
    }

    // Conexão Híbrida: Tenta MySQL Local (Wamp); se não estiver disponível, usa a Nuvem
    $conn = null;
    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']) || php_sapi_name() === 'cli';

    if ($isLocal) {
        $hostLocal = defined('DB_HOST_LOCAL') ? DB_HOST_LOCAL : 'localhost';
        $userLocal = defined('DB_USER_LOCAL') ? DB_USER_LOCAL : 'root';
        $passLocal = defined('DB_PASS_LOCAL') ? DB_PASS_LOCAL : '';
        $nameLocal = defined('DB_NAME_LOCAL') ? DB_NAME_LOCAL : 'game_db';
        $conn = @new mysqli($hostLocal, $userLocal, $passLocal, $nameLocal);
        if ($conn->connect_error) {
            $conn = null;
        }
    }

    // Se estiver na nuvem ou o MySQL local estiver desligado, conecta no Alwaysdata
    if (!$conn) {
        $hostCloud = defined('DB_HOST_CLOUD') ? DB_HOST_CLOUD : 'mysql-pijogo2026.alwaysdata.net';
        $userCloud = defined('DB_USER_CLOUD') ? DB_USER_CLOUD : 'pijogo2026';
        $passCloud = defined('DB_PASS_CLOUD') ? DB_PASS_CLOUD : '';
        $nameCloud = defined('DB_NAME_CLOUD') ? DB_NAME_CLOUD : 'pijogo2026_game_db';
        $conn = @new mysqli($hostCloud, $userCloud, $passCloud, $nameCloud);
    }

    if (!$conn || $conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . ($conn ? $conn->connect_error : 'Sem conexão disponível'));
    }
?>