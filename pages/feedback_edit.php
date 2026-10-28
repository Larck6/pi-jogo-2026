<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
    require_login();

    $idFeedback = (int) ($_SESSION["id_feedback"] ?? 0);

    $sql = db_stmt($conn, 'SELECT feedback.feedback, feedback.nota, user.name_user, feedback.fk_id_user FROM feedback JOIN user ON feedback.fk_id_user = user.pk_id_user WHERE feedback.pk_id_feedback = ? AND feedback.fk_id_user = ?', 'ii', $idFeedback, $idUser)->get_result();

    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $feedback = $_POST['feedback'] ?? '';
        $nota = (int) ($_POST['nota'] ?? $dados['nota']);

        if ($feedback == '') {
            $feedback = $dados['feedback'];
        }

        if ($nota == '') {
            $nota = $dados['nota'];
        }

        if ($nota < 1 || $nota > 5) exit('Nota inválida.');
        db_stmt($conn, 'UPDATE feedback SET feedback = ?, nota = ? WHERE pk_id_feedback = ? AND fk_id_user = ?', 'siii', $feedback, $nota, $idFeedback, $idUser);
        redirect_to('/pages/feedback.php');
    }
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<section class="edit">
    <form action="<?= e(app_url('/pages/feedback_edit.php')) ?>" method="post">
        <input type="text" name="feedback" placeholder="Feedback" value="<?= e($dados['feedback']) ?>">
        <div class="estrelas">
            <input type="radio" id="nota1" name="nota" value="5" />
            <label for="nota1"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota2" name="nota" value="4" />
            <label for="nota2"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota3" name="nota" value="3" />
            <label for="nota3"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota4" name="nota" value="2" />
            <label for="nota4"><i class="fa fa-star"></i></label>

            <input type="radio" id="nota5" name="nota" value="1" />
            <label for="nota5"><i class="fa fa-star"></i></label>
        </div>
        <button type="submit">Atualizar</button>
    </form>
</section>

<section>
    <div><?= e($dados["feedback"]) ?></div>
    <div><?= $dados["nota"] ?></div>
    <div><?= e($dados["name_user"]) ?></div>
</section>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
