<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $sql = $conn->query("SELECT * FROM imagens WHERE pk_id_imagem = '$idImagem'");

    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $imagem = $_POST['nome'] ?? '';

        $arquivo = $_FILES['imagem'];
        $nomeArquivo = $arquivo['name'];
        $tmp = $arquivo['tmp_name'];

        $pastaFisico = "C:/wamp64/www/pi/img/";
        $caminhoFisico = $pastaFisico . $nomeArquivo;

        $pastaURL = "/pi/img/";
        $caminhoURL = $pastaURL . $nomeArquivo;

        move_uploaded_file($tmp, $caminhoFisico);

        if ($feedback == '') {
            $feedback = $dados['nome_imagem'];
        }

        if ($caminhoURL == '') {
            $caminhoURL = $dados['caminho'];
        }

        $update = "UPDATE imagens SET nome_imagem = '$imagem', caminho = '$caminhoURL' WHERE pk_id_imagem = '$idImagem' AND fk_id_user = '$idUser'";

        if ($conn->query($update)) {
            header("Location: /pi/pages/capturas.php");
            exit;
        }
    }
    // SELECT IMAGENS
    $img = $conn->query("SELECT * FROM imagens WHERE pk_id_imagem = '$idImagem' AND fk_id_user = '$idUser'");
?>

<?php 
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome da imagem" required>
    <input type="file" name="imagem" required>
    <button type="submit" name="btn" value="upload_image">Enviar</button>
</form>

<?php 
    while($row = $img->fetch_assoc()){
?>
    <img class="img" src="<?= $row['caminho'] ?>">

    <p><b><?= $row['nome_imagem'] ?></b></p>
<?php
    }
?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>