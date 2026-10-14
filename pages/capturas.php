<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $user_row = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $action = $_POST["btn"] ?? null;

        // UPLOAD IMAGEM
        if ($action == "upload_image" && $logado && $idUser) {
            $nome = $_POST['nome'];

            $arquivo = $_FILES['imagem'];
            $nomeArquivo = $arquivo['name'];
            $tmp = $arquivo['tmp_name'];

            $pastaFisico = dirname(__DIR__) . "/img/";
            if (!is_dir($pastaFisico)) {
                mkdir($pastaFisico, 0755, true);
            }
            $caminhoFisico = $pastaFisico . $nomeArquivo;

            $pastaURL = "../img/";
            $caminhoURL = $pastaURL . $nomeArquivo;

            move_uploaded_file($tmp, $caminhoFisico);

            $conn->query("INSERT INTO imagens (nome_imagem, caminho, fk_id_user) 
            VALUES ('$nome', '$caminhoURL', '$idUser')");

            header("Location: /pi/pages/capturas.php");
            exit;
        }

        // COMENTAR
        if ($action == "comentar_image" && $logado && $idUser) {
            if (!isset($_POST['nota'])) {
                echo "<script>alert('Selecione uma nota');</script>";
                return;
            }

            $comentario = $_POST['comentario'];
            $nota = $_POST['nota'];
            $idImagem = $_POST['id_imagem'];

            // VERIFICA SE JÁ EXISTE COMENTÁRIO NESSA IMAGEM
            $check = $conn->query("SELECT * FROM feedback_imagem WHERE fk_id_user = '$idUser' AND fk_id_imagem = '$idImagem'");

            if ($check->num_rows == 0) {

                $conn->query("INSERT INTO feedback_imagem (comentario, nota, fk_id_imagem, fk_id_user) VALUES ('$comentario', '$nota', '$idImagem', '$idUser')");

                header("Location: /pi/pages/capturas.php");
                exit;

            } else {
                echo "<script>alert('Você já comentou nessa imagem.');</script>";
            }
        }

        // MOSTRAR COMENTÁRIOS
        if ($action == "see_feedback") {
            $show = $_POST['id_imagem'];
        }

        if ($action == "close_feedback") {
            $show = null;
        }

        // DELETE IMAGEM
        if ($action == "delete_imagem" && $logado && $idUser) {
            $idImagem = $_POST['idf'] ?? null;

            if ($idImagem) {
                $conn->query("DELETE FROM imagens WHERE pk_id_imagem = '$idImagem' AND fk_id_user = '$idUser'");
            }

            header("Location: /pi/pages/capturas.php");
            exit;
        }

        // EDIT IMAGEM
        if ($action == "edit_imagem" && $logado && $idUser) {
            $idImagem = $_POST['idf'] ?? null;

            if ($idImagem) {
                $_SESSION["id_imagem"] = $idImagem;
                header("Location: /pi/pages/capturas_edit.php");
                exit;
            }
        }

        // DELETE
        if ($action == "delete" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $conn->query("DELETE FROM feedback_imagem WHERE pk_id_feedback_imagem = '$idFeedback' AND fk_id_user = '$idUser'");
            }

            header("Location: /pi/pages/capturas.php");
            exit;
        }

        // EDIT
        if ($action == "edit" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $_SESSION["id_feedback_imagem"] = $idFeedback;
                header("Location: /pi/pages/feedback_capturas_edit.php");
                exit;
            }
        }
    }

    $userlogado = null;
    if ($idUser) {
        $userlogado = $conn->query("SELECT feedback_imagem.pk_id_feedback_imagem, feedback_imagem.comentario, feedback_imagem.nota, user.name_user, feedback_imagem.fk_id_user FROM feedback_imagem JOIN user ON feedback_imagem.fk_id_user = user.pk_id_user WHERE fk_id_user = '$idUser'");
    }
    $user_feedback = ($userlogado && $userlogado->num_rows > 0) ? $userlogado->fetch_assoc() : null;

    // BUSCAR IMAGENS
    $imagens = $conn->query("SELECT imagens.*, user.name_user FROM imagens JOIN user ON imagens.fk_id_user = user.pk_id_user ORDER BY (imagens.fk_id_user = '$idUser') DESC");
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<!-- FORM UPLOAD -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome da imagem" required>
    <input type="file" name="imagem" required>
    <button type="submit" name="btn" value="upload_image">Enviar</button>
</form>

<?php while ($img = $imagens->fetch_assoc()) { ?>
    <?php
        //NOTA MEDIA DOS FEEDBACKS
        $media = $conn->query("SELECT AVG(nota) AS media FROM feedback_imagem WHERE fk_id_imagem = {$img['pk_id_imagem']}")->fetch_assoc()['media'];
        $media = round($media ?? 0);

        //FEEDBACKS DAS IMAGENS
        $feedbacks = $conn->query("SELECT feedback_imagem.*, user.name_user FROM feedback_imagem JOIN user ON feedback_imagem.fk_id_user = user.pk_id_user WHERE fk_id_imagem = {$img['pk_id_imagem']}");
    ?>

    <div class="card">

        <img class="img" src="<?= $img['caminho'] ?>">

        <p><b><?= $img['nome_imagem'] ?></b></p>
        <p>Por: <?= $img['name_user'] ?></p>

        <div class="nota_estrela">
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <i class="fa fa-star" <?= ($media >= $i) ? 'id="nota_checked"' : '' ?>></i>
            <?php } ?>
        </div>

        <!-- BOTÃO EDITAR/EXCLUIR -->
        <div>
            <?php if ($logado && $img['fk_id_user'] == $idUser) { ?>
                <form method="POST">
                    <input type="hidden" name="idf" value="<?= $img['pk_id_imagem'] ?>">
                    <button name="btn" value="edit_imagem">Editar</button>
                    <button name="btn" value="delete_imagem">Excluir</button>
                </form>
            <?php } ?>
        </div>

        <!-- BOTÃO VER COMENTÁRIOS -->
        <form method="POST">
            <input type="hidden" name="id_imagem" value="<?= $img['pk_id_imagem'] ?>">
            <button type="submit" name="btn" value="see_feedback">Comentários</button>
        </form>

        <!-- COMENTÁRIOS -->
        <section style="<?= ($show == $img['pk_id_imagem']) ? "display:block;" : "display:none;" ?>">
            <form method="POST">
                <input type="hidden" name="id_imagem" value="<?= $img['pk_id_imagem'] ?>">
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
                        <input type="hidden" name="idf" value="<?= $f['pk_id_feedback_imagem'] ?>">
                        <button name="btn" value="edit">Editar</button>
                        <button name="btn" value="delete">Excluir</button>
                    </form>
                <?php } ?>

                <hr>
            <?php } ?>
        </section>

        <!-- FORM COMENTAR -->
        <?php if ($logado) { ?>
            <form method="POST">
                <input type="hidden" name="id_imagem" value="<?= $img['pk_id_imagem'] ?>">
                <input type="text" name="comentario" placeholder="Comentário" required>
                <div class="estrelas">
                    <input type="radio" id="nota1_<?= $img['pk_id_imagem'] ?>" name="nota" value="5" required/>
                    <label for="nota1_<?= $img['pk_id_imagem'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota2_<?= $img['pk_id_imagem'] ?>" name="nota" value="4" required/>
                    <label for="nota2_<?= $img['pk_id_imagem'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota3_<?= $img['pk_id_imagem'] ?>" name="nota" value="3"required />
                    <label for="nota3_<?= $img['pk_id_imagem'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota4_<?= $img['pk_id_imagem'] ?>" name="nota" value="2" required/>
                    <label for="nota4_<?= $img['pk_id_imagem'] ?>"><i class="fa"></i></label>

                    <input type="radio" id="nota5_<?= $img['pk_id_imagem'] ?>" name="nota" value="1" required/>
                    <label for="nota5_<?= $img['pk_id_imagem'] ?>"><i class="fa"></i></label>
                </div>
                <button type="submit" name="btn" value="comentar_image">Comentar</button>
            </form>
        <?php } ?>
    </div>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
