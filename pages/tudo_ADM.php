<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    // PROTEÇÃO ADM
    if (!isset($_SESSION['adm']) || $_SESSION['adm'] !== true) {
        die("Acesso negado");
    }

    // FILTRO
    $filtro = $_GET['filtro'] ?? 'todos';

    // =======================
    // AÇÕES ADM
    // =======================
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $tipo = $_POST['tipo'] ?? null;
        $id = $_POST['id'] ?? null;
        $acao = $_POST['acao'] ?? null;

        $id = (int)$id; // segurança básica

        if (isset($_POST['acao']) && $_POST['acao'] == "abrir") {
            header("Location: ".$_SERVER['PHP_SELF']."?show=$id&tipo=$tipo&filtro=$filtro");
            exit;
        }

        if (isset($_POST['acao']) && $_POST['acao'] == "fechar") {
            $show = null;
            $tipo_show = null;
        }

        if ($acao == "criar") {

            $titulo = $_POST['titulo'];
            $descricao = $_POST['descricao'];
            $user = $_SESSION['id'];

            if ($tipo == "atualizacao") {
                $conn->query("
                    INSERT INTO atualizacoes (titulo, descricao, data_post, fk_id_user)
                    VALUES ('$titulo', '$descricao', NOW(), $user)
                ");
            }

            if ($tipo == "mecanica") {
                $conn->query("
                    INSERT INTO mecanicas (titulo, descricao, fk_id_user)
                    VALUES ('$titulo', '$descricao', $user)
                ");
            }

            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

        if ($acao == "delete") {
            if ($tipo == "atualizacao") {
                $conn->query("DELETE FROM atualizacoes WHERE pk_id_atualizacao = $id");
            }

            if ($tipo == "mecanica") {
                $conn->query("DELETE FROM mecanicas WHERE pk_id_mecanica = $id");
            }

            if ($tipo == "review") {
                $conn->query("DELETE FROM feedback WHERE pk_id_feedback = $id");
            }

            if ($tipo == "imagem") {
                $conn->query("DELETE FROM imagens WHERE pk_id_imagem = $id");
            }

            if ($tipo == "video") {
                $conn->query("DELETE FROM videos WHERE pk_id_video = $id");
            }

            if ($tipo == "forum") {
                $conn->query("DELETE FROM forum WHERE pk_id_forum = $id");
            }

            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

        if ($acao == "delete_comentario") {

            if ($tipo == "comentario_video") {
                $conn->query("DELETE FROM feedback_video WHERE pk_id_feedback_video = $id");
            }

            if ($tipo == "comentario_imagem") {
                $conn->query("DELETE FROM feedback_imagem WHERE pk_id_feedback_imagem = $id");
            }

            if ($tipo == "comentario_forum") {
                $conn->query("DELETE FROM forum_comentario WHERE pk_id_forum_comentario = $id");
            }

            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    }

    // =======================
    // CONTROLE COMENTÁRIOS
    // =======================
    $show = $_GET['show'] ?? null;
    $tipo_show = $_GET['tipo'] ?? null;

    // =======================
    // FEED FILTRADO
    // =======================
    $feed = [];

    // ATUALIZAÇÕES
    if ($filtro == "todos" || $filtro == "atualizacao") {
        $atualizacoes = $conn->query("SELECT * FROM atualizacoes");

        while ($a = $atualizacoes->fetch_assoc()) {
            $feed[] = [
                "tipo" => "atualizacao",
                "id" => $a["pk_id_atualizacoes"],
                "texto" => $a["texto"],
                "imagem" => $a["imagem"]
            ];
        }
    }

    // MECÂNICAS
    if ($filtro == "todos" || $filtro == "mecanica") {
        $mecanicas = $conn->query("SELECT * FROM mecanicas");

        while ($m = $mecanicas->fetch_assoc()) {
            $feed[] = [
                "tipo" => "mecanica",
                "id" => $m["pk_id_mecanicas"],
                "texto" => $m["texto"],
                "imagem" => $m["imagem"]
            ];
        }
    }

    // REVIEW
    if ($filtro == "todos" || $filtro == "review") {
        $feedbacks = $conn->query("
            SELECT feedback.pk_id_feedback, feedback.feedback, feedback.nota, user.name_user
            FROM feedback
            JOIN user ON feedback.fk_id_user = user.pk_id_user
        ");

        while ($f = $feedbacks->fetch_assoc()) {
            $feed[] = [
                "tipo" => "review",
                "id" => $f["pk_id_feedback"],
                "usuario" => $f["name_user"],
                "conteudo" => $f["feedback"],
                "nota" => $f["nota"]
            ];
        }
    }

    // IMAGENS
    if ($filtro == "todos" || $filtro == "imagem") {
        $imagens = $conn->query("
            SELECT imagens.*, user.name_user
            FROM imagens
            JOIN user ON imagens.fk_id_user = user.pk_id_user
        ");

        while ($img = $imagens->fetch_assoc()) {
            $feed[] = [
                "tipo" => "imagem",
                "id" => $img["pk_id_imagem"],
                "usuario" => $img["name_user"],
                "titulo" => $img["nome_imagem"],
                "caminho" => $img["caminho"]
            ];
        }
    }

    // VIDEOS
    if ($filtro == "todos" || $filtro == "video") {
        $videos = $conn->query("
            SELECT videos.*, user.name_user
            FROM videos
            JOIN user ON videos.fk_id_user = user.pk_id_user
        ");

        while ($vid = $videos->fetch_assoc()) {
            $feed[] = [
                "tipo" => "video",
                "id" => $vid["pk_id_video"],
                "usuario" => $vid["name_user"],
                "titulo" => $vid["nome_video"],
                "caminho" => $vid["caminho"]
            ];
        }
    }

    // FORUM
    if ($filtro == "todos" || $filtro == "forum") {
        $foruns = $conn->query("
            SELECT forum.*, user.name_user
            FROM forum
            JOIN user ON forum.fk_id_user = user.pk_id_user
        ");

        while ($forum = $foruns->fetch_assoc()) {
            $feed[] = [
                "tipo" => "forum",
                "id" => $forum["pk_id_forum"],
                "usuario" => $forum["name_user"],
                "titulo" => $forum["nome"],
                "descricao" => $forum["description"]
            ];
        }
    }
?>

<?php
    include "../include/header.php";
    include "../include/menu_ADM.php";
    include "../include/login.php";
?>

<!-- FILTRO -->
<form method="GET">
<select name="filtro">
    <option value="todos" <?= $filtro=="todos"?"selected":"" ?>>Todos</option>
    <option value="atualizacao" <?= $filtro=="atualizacao"?"selected":"" ?>>Atualizações</option>
    <option value="mecanica" <?= $filtro=="mecanica"?"selected":"" ?>>Mecânicas</option>
    <option value="review" <?= $filtro=="review"?"selected":"" ?>>Reviews</option>
    <option value="imagem" <?= $filtro=="imagem"?"selected":"" ?>>Imagens</option>
    <option value="video" <?= $filtro=="video"?"selected":"" ?>>Vídeos</option>
    <option value="forum" <?= $filtro=="forum"?"selected":"" ?>>Fórum</option>
</select>
<button type="submit">Filtrar</button>
</form>

<?php foreach ($feed as $item): ?>

    <div class="card">
        <!-- CONTEÚDO -->
        <?php if ($item["tipo"] == "imagem") { ?>
        <img src="<?= $item["caminho"] ?>" width="200">
        <?php } ?>

        <?php if ($item["tipo"] == "review") { ?>
        <p><b><?= $item["usuario"] ?></b></p>
        <p><b><?= $item["conteudo"] ?></b></p>
        <p><?= $item["nota"] ?></p>
        <?php } ?>

        <?php if ($item["tipo"] == "video") { ?>
        <video width="250" controls>
        <source src="<?= $item["caminho"] ?>">
        </video>
        <?php } ?>

        <?php if ($item["tipo"] == "forum") { ?>
        <p><b><?= $item["titulo"] ?></b></p>
        <p><?= $item["descricao"] ?></p>
        <?php } ?>

        <?php if ($item["tipo"] == "mecanica") { ?>
        <p><?= $item["texto"] ?></p>
        <?php } ?>

        <?php if ($item["tipo"] == "atualizacao") { ?>
        <p><?= $item["texto"] ?></p>
        <?php } ?>

        <!-- EXCLUIR POST -->
        <form method="POST">
        <input type="hidden" name="tipo" value="<?= $item["tipo"] ?>">
        <input type="hidden" name="id" value="<?= $item["id"] ?>">
        <button name="acao" value="delete">Excluir</button>
        </form>

        <!-- ABRIR COMENTÁRIOS -->
        <?php if ($item["tipo"] != "review" && $item["tipo"] != "mecanica" && $item["tipo"] != "atualizacao"): ?>
            <form method="POST">
                <input type="hidden" name="tipo" value="<?= $item["tipo"] ?>">
                <input type="hidden" name="id" value="<?= $item["id"] ?>">
                <button name="acao" value="abrir">Comentários</button>
            </form>
        <?php endif; ?>

        <!-- ÁREA DE COMENTÁRIOS -->
        <div style="<?php echo ($show == $item['id'] && $tipo_show == $item['tipo']) ? 'display:block;' : 'display:none;'; ?>">
            <form method="GET">
                <input type="hidden" name="filtro" value="<?= $filtro ?>">
                <button>Fechar</button>
            </form>

            <h4>Comentários:</h4>

            <?php
            if ($item["tipo"] == "video") {
                $comentarios = $conn->query("
                    SELECT * FROM feedback_video WHERE fk_id_video = {$item["id"]}
                ");

                while($c = $comentarios->fetch_assoc()) {
                    ?>
                    <p><?= $c['comentario'] ?></p>

                    <form method="POST">
                        <input type="hidden" name="tipo" value="comentario_video">
                        <input type="hidden" name="id" value="<?= $c['pk_id_feedback_video'] ?>">
                        <button name="acao" value="delete_comentario">Excluir</button>
                    </form>
                    <hr>
                    <?php
                }
            }

            if ($item["tipo"] == "imagem") {
                $comentarios = $conn->query("
                    SELECT * FROM feedback_imagem WHERE fk_id_imagem = {$item["id"]}
                ");

                while($c = $comentarios->fetch_assoc()) {
                    ?>
                    <p><?= $c['comentario'] ?></p>

                    <form method="POST">
                        <input type="hidden" name="tipo" value="comentario_imagem">
                        <input type="hidden" name="id" value="<?= $c['pk_id_feedback_imagem'] ?>">
                        <button name="acao" value="delete_comentario">Excluir</button>
                    </form>
                    <hr>
                    <?php
                }
            }

            if ($item["tipo"] == "forum") {
                $comentarios = $conn->query("
                    SELECT * FROM forum_comentario WHERE fk_id_forum = {$item["id"]}
                ");

                while($c = $comentarios->fetch_assoc()) {
                    ?>
                    <p><?= $c['comentario'] ?></p>

                    <form method="POST">
                        <input type="hidden" name="tipo" value="comentario_forum">
                        <input type="hidden" name="id" value="<?= $c['pk_id_forum_comentario'] ?>">
                        <button name="acao" value="delete_comentario">Excluir</button>
                    </form>
                    <hr>
                    <?php
                }
            }
            ?>
        </div>
    </div>
<?php endforeach; ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>