<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["btn"] ?? '';
    $idForum = $_POST['idf'] ?? null;

    // CRIAR FÓRUM
    if ($action == "create" && $logado && $idUser) {
        $nome_forum = $_POST['forum'] ?? '';
        $description = $_POST['description'] ?? '';

        $conn->query("INSERT INTO forum (nome, description, fk_id_user) 
                      VALUES ('$nome_forum', '$description', '$idUser')");

        header("Location: /pi/pages/discussoes.php");
        exit;
    }

    // EXCLUIR FÓRUM
    if ($action == "delete" && $logado && $idUser) {
        if ($idForum) {
            $conn->query("DELETE FROM forum_comentario WHERE fk_id_forum = '$idForum'");
            $conn->query("DELETE FROM forum WHERE pk_id_forum = '$idForum' AND fk_id_user = '$idUser'");
        }

        header("Location: /pi/pages/discussoes.php?show=$idForum#forum$idForum");
        exit;
    }

    // COMENTAR
    if ($action == "comment" && $logado && $idUser) {
        $comentario = $_POST['comentario'] ?? '';

        if (!empty($comentario) && $idForum) {
            $conn->query("INSERT INTO forum_comentario (comentario, fk_id_user, fk_id_forum) 
                          VALUES ('$comentario', '$idUser', '$idForum')");
        }

        header("Location: /pi/pages/discussoes.php?show=$idForum#forum$idForum");
        exit;
    }

    // EXCLUIR COMENTÁRIO
    if ($action == "delete_comment" && $logado && $idUser) {
        $idComentario = $_POST['idc'] ?? null;

        if ($idComentario) {
            $conn->query("
                DELETE FROM forum_comentario 
                WHERE pk_id_forum_comentario = '$idComentario' 
                AND fk_id_user = '$idUser'
            ");
        }

        header("Location: /pi/pages/discussoes.php?show=$idForum#forum$idForum");
        exit;
    }

        // MOSTRAR COMENTÁRIOS
    if ($action == "see_comments") {
        $idForum = $_POST['idf'];
        header("Location: /pi/pages/discussoes.php?show=$idForum#forum$idForum");
        exit;
    }

    // FECHAR COMENTÁRIOS
    if ($action == "close_comments") {
        $show = null;
        header("Location: /pi/pages/discussoes.php");
        exit;
    }
}

// BUSCAR FÓRUNS
$select_forum = $conn->query("
    SELECT forum.*, user.name_user 
    FROM forum
    JOIN user ON forum.fk_id_user = user.pk_id_user
");
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<!-- CRIAR FÓRUM -->
<?php if ($logado) { ?>
<section>
    <form method="post">
        <input type="text" name="forum" placeholder="Inicie uma conversa" required>
        <input type="text" name="description" placeholder="Descrição" required>
        <button type="submit" name="btn" value="create">Criar</button>
    </form>
</section>
<?php } else { ?>
    <p>Você precisa estar logado</p>
<?php } ?>

<!-- LISTA DE FÓRUNS -->
<?php while ($row = $select_forum->fetch_assoc()) { ?>
    <div class="forum"  id="forum<?= $row["pk_id_forum"] ?>">
    <h3><?= $row["nome"] ?></h3>
    <p><?= $row["description"] ?></p>
    <small>Por: <?= $row["name_user"] ?></small>

    <!-- EXCLUIR FÓRUM -->
    <?php if ($logado && $idUser == $row["fk_id_user"]) { ?>
        <form method="POST">
            <input type="hidden" name="idf" value="<?= $row["pk_id_forum"] ?>">
            <button type="submit" name="btn" value="delete">Excluir</button>
        </form>
    <?php } ?>

    <!-- BOTÃO VER COMENTÁRIOS -->
    <form method="POST">
        <input type="hidden" name="idf" value="<?= $row["pk_id_forum"] ?>">
        <button type="submit" name="btn" value="see_comments">Comentários</button>
    </form>

    <?php
        //SELECT COMENTARIOS FORUM
        $idForumAtual = $row["pk_id_forum"];
        $comentarios = $conn->query("SELECT forum_comentario.*, user.name_user FROM forum_comentario JOIN user ON forum_comentario.fk_id_user = user.pk_id_user WHERE fk_id_forum = '$idForumAtual'");
    ?>

    <!-- SEÇÃO ESCONDIDA -->
    <section style="<?= ($show == $idForumAtual) ? "display:block;" : "display:none;" ?>">

        <!-- FECHAR -->
        <form method="POST">
            <input type="hidden" name="idf" value="<?= $idForumAtual ?>">
            <button type="submit" name="btn" value="close_comments">Fechar</button>
        </form>

        <h4>Comentários:</h4>

        <?php while ($c = $comentarios->fetch_assoc()) { ?>
            <div class="comentario">
                <strong><?= $c["name_user"] ?>:</strong>
                <?= $c["comentario"] ?>

                <!-- EXCLUIR COMENTÁRIO -->
                <?php if ($logado && $idUser == $c["fk_id_user"]) { ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="idc" value="<?= $c["pk_id_forum_comentario"] ?>">
                        <button type="submit" name="btn" value="delete_comment">🗑️</button>
                    </form>
                <?php } ?>
            </div>
        <?php } ?>

        <!-- FORM COMENTAR -->
        <?php if ($logado) { ?>
            <form method="POST">
                <input type="hidden" name="idf" value="<?= $idForumAtual ?>">
                <input type="text" name="comentario" placeholder="Comentar..." required>
                <button type="submit" name="btn" value="comment">Enviar</button>
            </form>
        <?php } ?>

    </section>

</div>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>