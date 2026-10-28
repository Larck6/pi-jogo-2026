<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
    require_login();

    $idVideo = (int) ($_SESSION["id_video"] ?? 0);

    $sql = db_stmt($conn, 'SELECT * FROM videos WHERE pk_id_video = ? AND fk_id_user = ?', 'ii', $idVideo, $idUser)->get_result();
    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $video = trim($_POST['nome'] ?? '') ?: $dados['nome_video'];
        $caminhoURL = $dados['caminho'];
        if (isset($_FILES['video']) && $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
            $novo = store_upload($_FILES['video'], 'videos', 'video');
            delete_uploaded_file($caminhoURL);
            $caminhoURL = $novo;
        }
        db_stmt($conn, 'UPDATE videos SET nome_video = ?, caminho = ? WHERE pk_id_video = ? AND fk_id_user = ?', 'ssii', $video, $caminhoURL, $idVideo, $idUser);
        redirect_to('/pages/videos.php');
    }

    //SELECT VIDEO
    $vid = db_stmt($conn, 'SELECT * FROM videos WHERE pk_id_video = ? AND fk_id_user = ?', 'ii', $idVideo, $idUser)->get_result();
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome do vídeo">
    <input type="file" name="video" accept="video/mp4,video/webm,video/ogg">
    <button type="submit">Atualizar</button>
</form>

<?php
    while($row = $vid->fetch_assoc()){
?>
    <video class="img" controls>
        <source src="<?= e($row['caminho']) ?>">
    </video>

    <p><b><?= e($row['nome_video']) ?></b></p>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
