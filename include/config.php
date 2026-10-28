<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

if (!defined('APP_ENV')) {
    define('APP_ENV', env_value('APP_ENV', 'development'));
}
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', rtrim((string) env_value('APP_BASE_PATH', '/pi'), '/'));
}
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
if (!defined('APP_UPLOAD_ROOT')) {
    define('APP_UPLOAD_ROOT', APP_ROOT . DIRECTORY_SEPARATOR . 'uploads');
}
if (!defined('APP_MAX_IMAGE_BYTES')) {
    define('APP_MAX_IMAGE_BYTES', 5 * 1024 * 1024);
}
if (!defined('APP_MAX_VIDEO_BYTES')) {
    define('APP_MAX_VIDEO_BYTES', 50 * 1024 * 1024);
}

if (!defined('DB_HOST')) {
    define('DB_HOST', defined('DB_HOST_LOCAL') ? DB_HOST_LOCAL : env_value('DB_HOST', 'localhost'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', (int) env_value('DB_PORT', '3306'));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', defined('DB_NAME_LOCAL') ? DB_NAME_LOCAL : env_value('DB_NAME', 'game_db'));
}
if (!defined('DB_USER')) {
    define('DB_USER', defined('DB_USER_LOCAL') ? DB_USER_LOCAL : env_value('DB_USER', 'root'));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', defined('DB_PASS_LOCAL') ? DB_PASS_LOCAL : (string) env_value('DB_PASS', ''));
}
