<?php
session_start();

// Nome do arquivo
$arquivo = "mito22.jpg";

// Caminho absoluto do arquivo de forma dinâmica
$caminho = dirname(__DIR__) . "/jogo/" . $arquivo;

// Verifica se o arquivo existe
if (!file_exists($caminho)) {
    die("Erro: Arquivo não encontrado.");
}

// Limpa o buffer de saída
if (ob_get_level()) {
    ob_end_clean();
}

// Define os cabeçalhos para download
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($caminho) . "\"");
header("Content-Length: " . filesize($caminho));
header("Content-Transfer-Encoding: binary");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Expires: 0");

// Envia o arquivo
readfile($caminho);
exit;
?>