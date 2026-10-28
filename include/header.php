<?php
    $paginaAtual = basename($_SERVER['SCRIPT_NAME'], '.php');
    $paginaClasse = preg_replace('/[^a-z0-9_-]/', '-', strtolower($paginaAtual));

    $paginasComunidade = [
        'tudo',
        'feedback',
        'capturas',
        'discussoes',
        'videos',
        'mecanicas',
        'atualizacoes',
        'tudo_ADM',
        'feedback_ADM',
        'capturas_ADM',
        'discussoes_ADM',
        'videos_ADM',
        'mecanicas_ADM',
        'atualizacoes_ADM',
        'feedback_edit',
        'feedback_capturas_edit',
        'feedback_videos_edit',
        'capturas_edit',
        'videos_edit'
    ];

    $cssPagina = null;

    if ($paginaAtual === 'index') {
        $cssPagina = app_url('/css/style_home.css');
    } elseif ($paginaAtual === 'sobre') {
        $cssPagina = app_url('/css/style_sobre.css');
    } elseif ($paginaAtual === 'login' || $paginaAtual === 'cadastro') {
        $cssPagina = app_url('/css/style_login.css');
    } elseif (in_array($paginaAtual, $paginasComunidade, true)) {
        $cssPagina = app_url('/css/style_comunidade.css');
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(app_url('/css/style.css')) ?>?v=<?= (int) filemtime(dirname(__DIR__) . '/css/style.css') ?>">
    <?php if ($cssPagina) { ?>
        <link rel="stylesheet" href="<?= e($cssPagina) ?>?v=<?= (int) filemtime(dirname(__DIR__) . '/css/' . basename($cssPagina)) ?>">
    <?php } ?>
    <?php if ($cssPagina !== app_url('/css/style_login.css')) { ?>
        <link rel="stylesheet" href="<?= e(app_url('/css/style_login.css')) ?>?v=<?= (int) filemtime(dirname(__DIR__) . '/css/style_login.css') ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Lendas do Vale</title>
</head>
<body class="page-<?= htmlspecialchars($paginaClasse) ?>">
    <div id="container">
        <header class="main-header">
            <nav class="main-nav">
                <ul class="main-menu">
                    <li><a href="<?= e(app_url('/index.php')) ?>">Home</a></li>
                    <li><a href="<?= e(app_url('/pages/tudo.php')) ?>">Comunidade</a></li>
                    <li><a href="<?= e(app_url('/pages/sobre.php')) ?>">Sobre</a></li>
                    <?php if($logado){?>
                        <li><a href="<?= e(app_url('/pages/download.php')) ?>">Download</a></li>
                    <?php } ?>
                    <?php if ($adm) { ?>
                        <li><a href="<?= e(app_url('/pages/tudo_ADM.php')) ?>">Area ADM</a></li>
                    <?php } ?>
                    <?php if($logado){?>
                        <li>
                            <button type="button" id="openAccount" class="account-button" aria-controls="accountPanel" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <span>Conta</span>
                            </button>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a class="login-link" href="<?= e(app_url('/pages/login.php')) ?>">
                                <span>Login</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </header>
