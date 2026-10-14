<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $idVideo = $_SESSION["id_video"] ?? null;

    $sql = $conn->query("SELECT * FROM videos WHERE pk_id_video = '$idVideo'");
    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $video = $_POST['nome'] ?? '';

        $arquivo = $_FILES['video'];
        $nomeArquivo = $arquivo['name'];
        $tmp = $arquivo['tmp_name'];

        $pastaFisico = "C:/wamp64/www/pi/videos/";
        $caminhoFisico = $pastaFisico . $nomeArquivo;

        $pastaURL = "/pi/videos/";
        $caminhoURL = $pastaURL . $nomeArquivo;

        // Só move se enviou arquivo
        if (!empty($nomeArquivo)) {
            move_uploaded_file($tmp, $caminhoFisico);
        }

        // Se não alterou o nome, mantém o antigo
        if ($video == '') {
            $video = $dados['nome_video'];
        }

        // Se não enviou novo vídeo, mantém o antigo
        if (empty($nomeArquivo)) {
            $caminhoURL = $dados['caminho'];
        }

        $update = "UPDATE videos SET nome_video = '$video', caminho = '$caminhoURL' WHERE pk_id_video = '$idVideo' AND fk_id_user = '$idUser'";

        if ($conn->query($update)) {
            header("Location: /pi/pages/videos.php");
            exit;
        }
    }

    //SELECT VIDEO
    $vid = $conn->query("SELECT * FROM videos WHERE pk_id_video = '$idVideo' AND fk_id_user = '$idUser'");
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome do vídeo">
    <input type="file" name="video">
    <button type="submit">Atualizar</button>
</form>

<?php
    while($row = $vid->fetch_assoc()){
?>
    <video class="img" controls>
        <source src="<?= $row['caminho'] ?>" type="video/mp4">
    </video>

    <p><b><?= $row['nome_video'] ?></b></p>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>