<?php
require_once __DIR__ . '/../include/conexao.php';
require_login();

// Nome do arquivo
$arquivo = env_value('GAME_DOWNLOAD_FILE', 'jogo.zip');

// Caminho absoluto do arquivo
$nomeSeguro = basename((string) $arquivo);
$caminho = APP_ROOT . DIRECTORY_SEPARATOR . 'jogo' . DIRECTORY_SEPARATOR . $nomeSeguro;

// Verifica se o arquivo existe
if (!file_exists($caminho)) {
    http_response_code(404);
    exit("O arquivo do jogo ainda não está disponível.");
}

// Limpa o buffer de saída
if (ob_get_level()) {
    ob_end_clean();
}

// Define os cabeçalhos para download
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $nomeSeguro . "\"");
header("Content-Length: " . filesize($caminho));
header("Content-Transfer-Encoding: binary");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Expires: 0");

// Envia o arquivo
readfile($caminho);
exit;
?>
