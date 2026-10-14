<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $action = $_POST["btn"] ?? null;

        // UPLOAD VIDEO
        if ($action == "upload_video" && $logado && $idUser) {
            $nome = $_POST['nome'];

            $arquivo = $_FILES['video'];
            $nomeArquivo = $arquivo['name'];
            $tmp = $arquivo['tmp_name'];

            $pastaFisico = "C:/wamp64/www/pi/videos/";
            $caminhoFisico = $pastaFisico . $nomeArquivo;

            $pastaURL = "/pi/videos/";
            $caminhoURL = $pastaURL . $nomeArquivo;

            move_uploaded_file($tmp, $caminhoFisico);

            $conn->query("INSERT INTO videos (nome_video, caminho, fk_id_user) 
            VALUES ('$nome', '$caminhoURL', '$idUser')");

            header("Location: /pi/pages/videos.php");
            exit;
        }

        // COMENTAR
        if ($action == "comentar_video" && $logado && $idUser) {

            if (!isset($_POST['nota'])) {
                echo "<script>alert('Selecione uma nota');</script>";
                return;
            }

            $comentario = $_POST['comentario'];
            $nota = $_POST['nota'];
            $idVideo = $_POST['id_video'];

            // VERIFICA SE JÁ EXISTE COMENTÁRIO
            $check = $conn->query("SELECT * FROM feedback_video WHERE fk_id_user = '$idUser' AND fk_id_video = '$idVideo'");

            if ($check->num_rows == 0) {

                $conn->query("INSERT INTO feedback_video (comentario, nota, fk_id_video, fk_id_user) 
                VALUES ('$comentario', '$nota', '$idVideo', '$idUser')");

                header("Location: /pi/pages/videos.php");
                exit;

            } else {
                echo "<script>alert('Você já comentou nesse vídeo.');</script>";
            }
        }

        // MOSTRAR COMENTÁRIOS
        if ($action == "see_feedback") {
            $show = $_POST['id_video'];
        }

        if ($action == "close_feedback") {
            $show = null;
        }

        // DELETE VIDEO
        if ($action == "delete_video" && $logado && $idUser) {
            $idVideo = $_POST['idf'] ?? null;

            if ($idVideo) {
                $conn->query("DELETE FROM videos WHERE pk_id_video = '$idVideo' AND fk_id_user = '$idUser'");
            }

            header("Location: /pi/pages/videos.php");
            exit;
        }

        // EDIT VIDEO
        if ($action == "edit_video" && $logado && $idUser) {
            $idVideo = $_POST['idf'] ?? null;

            if ($idVideo) {
                $_SESSION["id_video"] = $idVideo;
                header("Location: /pi/pages/videos_edit.php");
                exit;
            }
        }

        // DELETE FEEDBACK
        if ($action == "delete" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $conn->query("DELETE FROM feedback_video WHERE pk_id_feedback_video = '$idFeedback' AND fk_id_user = '$idUser'");
            }

            header("Location: /pi/pages/videos.php");
            exit;
        }

        // EDIT FEEDBACK
        if ($action == "edit" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $_SESSION["id_feedback_video"] = $idFeedback;
                header("Location: /pi/pages/feedback_videos_edit.php");
                exit;
            }
        }
    }

    // BUSCAR VIDEOS (USUÁRIO LOGADO PRIMEIRO)
    $videos = $conn->query("SELECT videos.*, user.name_user FROM videos JOIN user ON videos.fk_id_user = user.pk_id_user ORDER BY (videos.fk_id_user = '$idUser') DESC, videos.pk_id_video DESC");
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<!-- FORM UPLOAD -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome do vídeo" required>
    <input type="file" name="video" required>
    <button type="submit" name="btn" value="upload_video">Enviar</button>
</form>

<?php while ($vid = $videos->fetch_assoc()) { ?>

    <?php
        //MEDIA NOTA FEEDBACK
        $media = $conn->query("SELECT AVG(nota) AS media FROM feedback_video WHERE fk_id_video = {$vid['pk_id_video']}")->fetch_assoc()['media'];
        $media = round($media ?? 0);

        //SELECT FEEDBACKS VIDEO
        $feedbacks = $conn->query("SELECT feedback_video.*, user.name_user FROM feedback_video JOIN user ON feedback_video.fk_id_user = user.pk_id_user WHERE fk_id_video = {$vid['pk_id_video']}");
    ?>

    <div class="card">

        <video class="img" controls>
            <source src="<?= $vid['caminho'] ?>" type="video/mp4">
        </video>

        <p><b><?= $vid['nome_video'] ?></b></p>
        <p>Por: <?= $vid['name_user'] ?></p>

        <div class="nota_estrela">
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <i class="fa fa-star" <?= ($media >= $i) ? 'id="nota_checked"' : '' ?>></i>
            <?php } ?>
        </div>

        <!-- EDITAR / EXCLUIR -->
        <div>
            <?php if ($logado && $vid['fk_id_user'] == $idUser) { ?>
                <form method="POST">
                    <input type="hidden" name="idf" value="<?= $vid['pk_id_video'] ?>">
                    <button name="btn" value="edit_video">Editar</button>
                    <button name="btn" value="delete_video">Excluir</button>
                </form>
            <?php } ?>
        </div>

        <!-- VER COMENTÁRIOS -->
        <form method="POST">
            <input type="hidden" name="id_video" value="<?= $vid['pk_id_video'] ?>">
            <button type="submit" name="btn" value="see_feedback">Comentários</button>
        </form>>

        <section style="<?= ($show == $vid['pk_id_video']) ? "display:block;" : "display:none;" ?>">

            <form method="POST">
                <input type="hidden" name="id_video" value="<?= $vid['pk_id_video'] ?>">
                <button type="submit" name="btn" value="close_feedback">Fechar</button>
            </form>

            <h4>Comentários:</h4>

            <?php while ($f = $feedbacks->fetch_assoc()) { ?>
                <p><b><?= $f['name_user'] ?>:</b> <?= $f['comentario'] ?></p>

                <div class="nota_estrela">
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <i class="fa fa-star" <?= ($f['nota'] >= $i) ? 'id="nota_checked"' : '' ?>></i>
                    <?php } ?>
                </div>

                <?php if ($logado && $f['fk_id_user'] == $idUser) { ?>
                    <form method="POST">
                        <input type="hidden" name="idf" value="<?= $f['pk_id_feedback_video'] ?>">
                        <button name="btn" value="edit">Editar</button>
                        <button name="btn" value="delete">Excluir</button>
                    </form>
                <?php } ?>

                <hr>
            <?php } ?>
        </section>

        <!-- COMENTAR -->
        <?php if ($logado) { ?>
            <form method="POST">
                <input type="hidden" name="id_video" value="<?= $vid['pk_id_video'] ?>">

                <input type="text" name="comentario" placeholder="Comentário" required>

                <div class="estrelas">
                    <input type="radio" id="nota1_<?= $vid['pk_id_video'] ?>" name="nota" value="5" required/>
                    <label for="nota1_<?= $vid['pk_id_video'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota2_<?= $vid['pk_id_video'] ?>" name="nota" value="4" required/>
                    <label for="nota2_<?= $vid['pk_id_video'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota3_<?= $vid['pk_id_video'] ?>" name="nota" value="3" required/>
                    <label for="nota3_<?= $vid['pk_id_video'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota4_<?= $vid['pk_id_video'] ?>" name="nota" value="2" required/>
                    <label for="nota4_<?= $vid['pk_id_video'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota5_<?= $vid['pk_id_video'] ?>" name="nota" value="1" required/>
                    <label for="nota5_<?= $vid['pk_id_video'] ?>"><i class="fa"></i></label>
                </div>

                <button type="submit" name="btn" value="comentar_video">Comentar</button>
            </form>
        <?php } ?>
    </div>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>