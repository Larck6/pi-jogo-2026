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

define('APP_ENV', env_value('APP_ENV', 'development'));
define('APP_BASE_PATH', rtrim((string) env_value('APP_BASE_PATH', '/pi'), '/'));
define('APP_ROOT', dirname(__DIR__));
define('APP_UPLOAD_ROOT', APP_ROOT . DIRECTORY_SEPARATOR . 'uploads');
define('APP_MAX_IMAGE_BYTES', 5 * 1024 * 1024);
define('APP_MAX_VIDEO_BYTES', 50 * 1024 * 1024);

define('DB_HOST', defined('DB_HOST_LOCAL') ? DB_HOST_LOCAL : env_value('DB_HOST', 'localhost'));
define('DB_PORT', (int) env_value('DB_PORT', '3306'));
define('DB_NAME', defined('DB_NAME_LOCAL') ? DB_NAME_LOCAL : env_value('DB_NAME', 'game_db'));
define('DB_USER', defined('DB_USER_LOCAL') ? DB_USER_LOCAL : env_value('DB_USER', 'root'));
define('DB_PASS', defined('DB_PASS_LOCAL') ? DB_PASS_LOCAL : (string) env_value('DB_PASS', ''));
