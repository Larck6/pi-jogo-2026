<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
    require_login();

    $idFeedback = (int) ($_SESSION["id_feedback_video"] ?? 0);

    $sql = db_stmt($conn, "
        SELECT feedback_video.comentario, feedback_video.nota, user.name_user, feedback_video.fk_id_user 
        FROM feedback_video 
        JOIN user ON feedback_video.fk_id_user = user.pk_id_user 
        WHERE feedback_video.pk_id_feedback_video = ? AND feedback_video.fk_id_user = ?
    ", 'ii', $idFeedback, $idUser)->get_result();

    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $feedback = $_POST['feedback'] ?? '';
        $nota = (int) ($_POST['nota'] ?? $dados['nota']);

        // Mantém valores antigos se não alterar
        if ($feedback == '') {
            $feedback = $dados['comentario'];
        }

        if ($nota == '') {
            $nota = $dados['nota'];
        }

        if ($nota < 1 || $nota > 5) exit('Nota inválida.');
        db_stmt($conn, 'UPDATE feedback_video SET comentario = ?, nota = ? WHERE pk_id_feedback_video = ? AND fk_id_user = ?', 'siii', $feedback, $nota, $idFeedback, $idUser);
        redirect_to('/pages/videos.php');
    }
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<section class="edit">
    <form action="<?= e(app_url('/pages/feedback_videos_edit.php')) ?>" method="post">
        <input type="text" name="feedback" placeholder="Feedback" value="<?= e($dados['comentario']) ?>">

        <div class="estrelas">
            <input type="radio" id="nota1" name="nota" value="5" <?= ($dados['nota'] == 5) ? 'checked' : '' ?> />
            <label for="nota1"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota2" name="nota" value="4" <?= ($dados['nota'] == 4) ? 'checked' : '' ?> />
            <label for="nota2"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota3" name="nota" value="3" <?= ($dados['nota'] == 3) ? 'checked' : '' ?> />
            <label for="nota3"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota4" name="nota" value="2" <?= ($dados['nota'] == 2) ? 'checked' : '' ?> />
            <label for="nota4"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota5" name="nota" value="1" <?= ($dados['nota'] == 1) ? 'checked' : '' ?> />
            <label for="nota5"><i class="fa fa-star"></i></label>
        </div>

        <button type="submit">Atualizar</button>
    </form>
</section>

<section>
    <div><?= e($dados["comentario"]) ?></div>
    <div><?= $dados["nota"] ?></div>
    <div><?= e($dados["name_user"]) ?></div>
</section>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
