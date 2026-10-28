<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => APP_BASE_PATH === '' ? '/' : APP_BASE_PATH . '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
}

ob_start(static function (string $html): string {
    return preg_replace_callback('/<form\b([^>]*)>/i', static function (array $match): string {
        if (!preg_match('/\bmethod\s*=\s*(["\'])?post\1/i', $match[1])) {
            return $match[0];
        }
        return $match[0] . csrf_field();
    }, $html) ?? $html;
});

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = null;

// Tenta conexão principal (Localhost)
try {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (Throwable $exception) {
    // Se falhar e as credenciais da nuvem estiverem disponíveis no config.local.php, conecta na nuvem
    if (defined('DB_HOST_CLOUD') && defined('DB_USER_CLOUD') && defined('DB_PASS_CLOUD') && defined('DB_NAME_CLOUD')) {
        try {
            $conn = new mysqli(DB_HOST_CLOUD, DB_USER_CLOUD, DB_PASS_CLOUD, DB_NAME_CLOUD, 3306);
            $conn->set_charset('utf8mb4');
        } catch (Throwable $cloudException) {
            error_log($cloudException->getMessage());
            http_response_code(503);
            exit(APP_ENV === 'production'
                ? 'Serviço temporariamente indisponível.'
                : 'Falha ao conectar ao banco de dados: ' . e($cloudException->getMessage()));
        }
    } else {
        error_log($exception->getMessage());
        http_response_code(503);
        exit(APP_ENV === 'production'
            ? 'Serviço temporariamente indisponível.'
            : 'Falha ao conectar ao banco de dados: ' . e($exception->getMessage()));
    }
}
