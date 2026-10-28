<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        verify_csrf();
        $action = $_POST["btn"] ?? null;

        if ($action == "upload_video" && $logado && $idUser) {
            $nome = trim($_POST['nome'] ?? '');
            if ($nome === '' || mb_strlen($nome) > 255) exit('Nome do vídeo inválido.');
            try {
                $caminhoURL = store_upload($_FILES['video'] ?? [], 'videos', 'video');
                db_stmt($conn, 'INSERT INTO videos (nome_video, caminho, fk_id_user) VALUES (?, ?, ?)', 'ssi', $nome, $caminhoURL, $idUser);
                redirect_to('/pages/videos.php');
            } catch (RuntimeException $exception) {
                $erroUpload = $exception->getMessage();
            }
        }

        // COMENTAR
        if ($action == "comentar_video" && $logado && $idUser) {

            if (!isset($_POST['nota'])) {
                echo "<script>alert('Selecione uma nota');</script>";
                return;
            }

            $comentario = trim($_POST['comentario'] ?? '');
            $nota = (int) ($_POST['nota'] ?? 0);
            $idVideo = (int) ($_POST['id_video'] ?? 0);
            if ($comentario === '' || $nota < 1 || $nota > 5 || $idVideo < 1) exit('Comentário ou nota inválidos.');

            // VERIFICA SE JÁ EXISTE COMENTÁRIO
            $check = db_stmt($conn, 'SELECT 1 FROM feedback_video WHERE fk_id_user = ? AND fk_id_video = ?', 'ii', $idUser, $idVideo)->get_result();

            if ($check->num_rows == 0) {

                db_stmt($conn, 'INSERT INTO feedback_video (comentario, nota, fk_id_video, fk_id_user) VALUES (?, ?, ?, ?)', 'siii', $comentario, $nota, $idVideo, $idUser);
                redirect_to('/pages/videos.php');

            } else {
                echo "<script>alert('Você já comentou nesse vídeo.');</script>";
            }
        }

        // MOSTRAR COMENTÁRIOS
        if ($action == "see_feedback") {
            $show = (int) ($_POST['id_video'] ?? 0);
        }

        if ($action == "close_feedback") {
            $show = null;
        }

        // DELETE VIDEO
        if ($action == "delete_video" && $logado && $idUser) {
            $idVideo = (int) ($_POST['idf'] ?? 0);

            if ($idVideo) {
                $result = db_stmt($conn, 'SELECT caminho FROM videos WHERE pk_id_video = ? AND fk_id_user = ?', 'ii', $idVideo, $idUser)->get_result()->fetch_assoc();
                db_stmt($conn, 'DELETE FROM videos WHERE pk_id_video = ? AND fk_id_user = ?', 'ii', $idVideo, $idUser);
                delete_uploaded_file($result['caminho'] ?? null);
            }

            redirect_to('/pages/videos.php');
        }

        // EDIT VIDEO
        if ($action == "edit_video" && $logado && $idUser) {
            $idVideo = $_POST['idf'] ?? null;

            if ($idVideo) {
                $_SESSION["id_video"] = $idVideo;
                redirect_to('/pages/videos_edit.php');
            }
        }

        // DELETE FEEDBACK
        if ($action == "delete" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                db_stmt($conn, 'DELETE FROM feedback_video WHERE pk_id_feedback_video = ? AND fk_id_user = ?', 'ii', $idFeedback, $idUser);
            }

            redirect_to('/pages/videos.php');
        }

        // EDIT FEEDBACK
        if ($action == "edit" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $_SESSION["id_feedback_video"] = $idFeedback;
                redirect_to('/pages/feedback_videos_edit.php');
            }
        }
    }

    // BUSCAR VIDEOS (USUÁRIO LOGADO PRIMEIRO)
    $videos = db_stmt($conn, 'SELECT videos.*, user.name_user FROM videos JOIN user ON videos.fk_id_user = user.pk_id_user ORDER BY (videos.fk_id_user = ?) DESC, videos.pk_id_video DESC', 'i', (int) $idUser)->get_result();
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<!-- FORM UPLOAD -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome do vídeo" required>
    <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" required>
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
            <source src="<?= e($vid['caminho']) ?>">
        </video>

        <p><b><?= e($vid['nome_video']) ?></b></p>
        <p>Por: <?= e($vid['name_user']) ?></p>

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
        </form>

        <section style="<?= ($show == $vid['pk_id_video']) ? "display:block;" : "display:none;" ?>">

            <form method="POST">
                <input type="hidden" name="id_video" value="<?= $vid['pk_id_video'] ?>">
                <button type="submit" name="btn" value="close_feedback">Fechar</button>
            </form>

            <h4>Comentários:</h4>

            <?php while ($f = $feedbacks->fetch_assoc()) { ?>
                <p><b><?= e($f['name_user']) ?>:</b> <?= e($f['comentario']) ?></p>

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
