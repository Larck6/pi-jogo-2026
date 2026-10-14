<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $idFeedback = $_SESSION["id_feedback_imagem"] ?? null;

    $sql = $conn->query("SELECT feedback_imagem.comentario, feedback_imagem.nota, user.name_user, feedback_imagem.fk_id_user FROM feedback_imagem JOIN user ON feedback_imagem.fk_id_user = user.pk_id_user WHERE feedback_imagem.pk_id_feedback_imagem = '$idFeedback'");

    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $feedback = $_POST['feedback'] ?? '';
        $nota = $_POST['nota'] ?? '';

        if ($feedback == '') {
            $feedback = $dados['feedback'];
        }

        if ($nota == '') {
            $nota = $dados['nota'];
        }

        $update = "UPDATE feedback_imagem SET comentario = '$feedback', nota = '$nota' WHERE pk_id_feedback_imagem = '$idFeedback' AND fk_id_user = '$idUser'";

        if ($conn->query($update)) {
            header("Location: /pi/pages/capturas.php");
            exit;
        }
    }
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<section class="edit">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="text" name="feedback" placeholder="Feedback" value="<?= $dados['comentario'] ?>">
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
    <div><?= $dados["comentario"] ?></div>
    <div><?= $dados["nota"] ?></div>
    <div><?= $dados["name_user"] ?></div>
</section>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>