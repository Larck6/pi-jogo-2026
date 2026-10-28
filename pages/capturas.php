<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $user_row = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        verify_csrf();

        $action = $_POST["btn"] ?? null;

        // UPLOAD IMAGEM
        if ($action == "upload_image" && $logado && $idUser) {
            $nome = trim($_POST['nome'] ?? '');
            if ($nome === '' || mb_strlen($nome) > 255) exit('Nome da imagem inválido.');
            try {
                $caminhoURL = store_upload($_FILES['imagem'] ?? [], 'capturas', 'image');
                db_stmt($conn, 'INSERT INTO imagens (nome_imagem, caminho, fk_id_user) VALUES (?, ?, ?)', 'ssi', $nome, $caminhoURL, $idUser);
                redirect_to('/pages/capturas.php');
            } catch (RuntimeException $exception) {
                $erroUpload = $exception->getMessage();
            }
        }

        // COMENTAR
        if ($action == "comentar_image" && $logado && $idUser) {
            if (!isset($_POST['nota'])) {
                echo "<script>alert('Selecione uma nota');</script>";
                return;
            }

            $comentario = trim($_POST['comentario'] ?? '');
            $nota = (int) ($_POST['nota'] ?? 0);
            $idImagem = (int) ($_POST['id_imagem'] ?? 0);
            if ($comentario === '' || $nota < 1 || $nota > 5 || $idImagem < 1) exit('Comentário ou nota inválidos.');

            // VERIFICA SE JÁ EXISTE COMENTÁRIO NESSA IMAGEM
            $check = db_stmt($conn, 'SELECT 1 FROM feedback_imagem WHERE fk_id_user = ? AND fk_id_imagem = ?', 'ii', $idUser, $idImagem)->get_result();

            if ($check->num_rows == 0) {

                db_stmt($conn, 'INSERT INTO feedback_imagem (comentario, nota, fk_id_imagem, fk_id_user) VALUES (?, ?, ?, ?)', 'siii', $comentario, $nota, $idImagem, $idUser);
                redirect_to('/pages/capturas.php');

            } else {
                echo "<script>alert('Você já comentou nessa imagem.');</script>";
            }
        }

        // MOSTRAR COMENTÁRIOS
        if ($action == "see_feedback") {
            $show = (int) ($_POST['id_imagem'] ?? 0);
        }

        if ($action == "close_feedback") {
            $show = null;
        }

        // DELETE IMAGEM
        if ($action == "delete_imagem" && $logado && $idUser) {
            $idImagem = (int) ($_POST['idf'] ?? 0);

            if ($idImagem) {
                $result = db_stmt($conn, 'SELECT caminho FROM imagens WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ii', $idImagem, $idUser)->get_result()->fetch_assoc();
                db_stmt($conn, 'DELETE FROM imagens WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ii', $idImagem, $idUser);
                delete_uploaded_file($result['caminho'] ?? null);
            }

            redirect_to('/pages/capturas.php');
        }

        // EDIT IMAGEM
        if ($action == "edit_imagem" && $logado && $idUser) {
            $idImagem = (int) ($_POST['idf'] ?? 0);

            if ($idImagem) {
                $owns = db_stmt($conn, 'SELECT 1 FROM imagens WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ii', $idImagem, $idUser)->get_result()->num_rows;
                if ($owns) {
                    $_SESSION["id_imagem"] = $idImagem;
                    redirect_to('/pages/capturas_edit.php');
                }
            }
        }

        // DELETE
        if ($action == "delete" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                db_stmt($conn, 'DELETE FROM feedback_imagem WHERE pk_id_feedback_imagem = ? AND fk_id_user = ?', 'ii', $idFeedback, $idUser);
            }

            redirect_to('/pages/capturas.php');
        }

        // EDIT
        if ($action == "edit" && $logado && $idUser) {
            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $_SESSION["id_feedback_imagem"] = $idFeedback;
                redirect_to('/pages/feedback_capturas_edit.php');
            }
        }
    }

    $userlogado = null;
    if ($idUser) {
        $userlogado = db_stmt($conn, 'SELECT feedback_imagem.pk_id_feedback_imagem, feedback_imagem.comentario, feedback_imagem.nota, user.name_user, feedback_imagem.fk_id_user FROM feedback_imagem JOIN user ON feedback_imagem.fk_id_user = user.pk_id_user WHERE feedback_imagem.fk_id_user = ?', 'i', $idUser)->get_result();
    }
    $user_feedback = ($userlogado && $userlogado->num_rows > 0) ? $userlogado->fetch_assoc() : null;

    // BUSCAR IMAGENS
    $imagens = db_stmt($conn, 'SELECT imagens.*, user.name_user FROM imagens LEFT JOIN user ON imagens.fk_id_user = user.pk_id_user ORDER BY (imagens.fk_id_user = ?) DESC, imagens.pk_id_imagem DESC', 'i', (int) $idUser)->get_result();
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<!-- FORM UPLOAD -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome da imagem" required>
    <input type="file" name="imagem" accept="image/jpeg,image/png,image/gif,image/webp" required>
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

        <img class="img" src="<?= e($img['caminho']) ?>" alt="<?= e($img['nome_imagem']) ?>">

        <p><b><?= e($img['nome_imagem']) ?></b></p>
        <p>Por: <?= e($img['name_user'] ?? 'Usuário removido') ?></p>

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
                <p><b><?= e($f['name_user']) ?>:</b> <?= e($f['comentario']) ?></p>

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
