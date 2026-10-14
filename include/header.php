<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/pi/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><!--Nome Jogo--></title>
</head>
<body>
    <div id="container">
        <header>
            <nav>
                <ul>
                    <li><a href="/pi/index.php">Home</a></li>
                    <li><a href="/pi/pages/tudo.php">Comunidade</a></li>
                    <li><a href="/pi/pages/sobre.php">Sobre</a></li>
                    <?php if($logado){?>
                        <li><a href="/pi/pages/download.php">Download</a></li>
                    <?php } ?>
                    <?php if ($adm) { ?>
                        <li><a href="/pi/pages/tudo_ADM.php">Area ADM</a></li>
                    <?php } ?>
                    <li><button id="openLogin">Login</button></li>
                </ul>
            </nav>
        </header>