<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function app_url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return (APP_BASE_PATH === '' ? '' : APP_BASE_PATH) . ($path === '/' ? '/' : $path);
}

function redirect_to(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Sessão expirada. Atualize a página e tente novamente.');
    }
}

function require_login(): void
{
    if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
        $_SESSION['login_error'] = 'Faça login para continuar.';
        redirect_to('/pages/login.php');
    }
}

function require_admin(): void
{
    if (($_SESSION['adm'] ?? false) !== true) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function db_stmt(mysqli $conn, string $sql, string $types = '', mixed ...$values): mysqli_stmt
{
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    return $stmt;
}

function ensure_upload_directory(string $folder): string
{
    $directory = APP_UPLOAD_ROOT . DIRECTORY_SEPARATOR . trim($folder, '/\\');
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível preparar a pasta de arquivos.');
    }
    return $directory;
}

function store_upload(array $file, string $folder, string $kind): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('O envio do arquivo falhou.');
    }

    $size = (int) ($file['size'] ?? 0);
    $isImage = $kind === 'image';
    $limit = $isImage ? APP_MAX_IMAGE_BYTES : APP_MAX_VIDEO_BYTES;
    if ($size < 1 || $size > $limit) {
        throw new RuntimeException($isImage ? 'A imagem deve ter no máximo 5 MB.' : 'O vídeo deve ter no máximo 50 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = $isImage
        ? ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp']
        : ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogv'];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException($isImage ? 'Formato de imagem inválido.' : 'Formato de vídeo inválido.');
    }

    $directory = ensure_upload_directory($folder);
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destination = $directory . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Não foi possível salvar o arquivo.');
    }

    return app_url('/uploads/' . trim($folder, '/\\') . '/' . $filename);
}

function delete_uploaded_file(?string $url): void
{
    if (!$url) {
        return;
    }
    $prefix = app_url('/uploads/');
    if (!str_starts_with($url, $prefix)) {
        return;
    }
    $relative = substr($url, strlen($prefix));
    $candidate = realpath(APP_UPLOAD_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative));
    $root = realpath(APP_UPLOAD_ROOT);
    if ($candidate && $root && str_starts_with($candidate, $root . DIRECTORY_SEPARATOR) && is_file($candidate)) {
        unlink($candidate);
    }
}

