<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $feed = [];

    //BUSCAR FEEDBACKS
    $feedbacks = $conn->query("SELECT feedback.feedback, feedback.nota, user.name_user FROM feedback JOIN user ON feedback.fk_id_user = user.pk_id_user");

    while ($f = $feedbacks->fetch_assoc()) {
        $feed[] = [
            "tipo" => "review",
            "usuario" => $f["name_user"],
            "conteudo" => $f["feedback"],
            "nota" => $f["nota"]
        ];
    }

    //BUSCAR IMAGENS
    $imagens = $conn->query("SELECT imagens.*, user.name_user FROM imagens JOIN user ON imagens.fk_id_user = user.pk_id_user");

    while ($img = $imagens->fetch_assoc()) {

        $media = $conn->query("SELECT AVG(nota) as media FROM feedback_imagem WHERE fk_id_imagem = {$img['pk_id_imagem']}")->fetch_assoc()["media"];

        $feed[] = [
            "tipo" => "imagem",
            "usuario" => $img["name_user"],
            "titulo" => $img["nome_imagem"],
            "caminho" => $img["caminho"],
            "nota" => round($media ?? 0)
        ];
    }

    //BUSCAR VIDEOS
    $videos = $conn->query("SELECT videos.*, user.name_user FROM videos JOIN user ON videos.fk_id_user = user.pk_id_user");

    while ($vid = $videos->fetch_assoc()) {

        $media = $conn->query("SELECT AVG(nota) as media FROM feedback_video WHERE fk_id_video = {$vid['pk_id_video']}")->fetch_assoc()["media"];

        $feed[] = [
            "tipo" => "video",
            "usuario" => $vid["name_user"],
            "titulo" => $vid["nome_video"],
            "caminho" => $vid["caminho"],
            "nota" => round($media ?? 0)
        ];
    }

    //BUSCAR DISCUSSÕES
    $foruns = $conn->query("SELECT forum.*, user.name_user FROM forum JOIN user ON forum.fk_id_user = user.pk_id_user");

    while ($forum = $foruns->fetch_assoc()) {

        $comentarios = $conn->query("SELECT COUNT(*) as total FROM forum_comentario WHERE fk_id_forum = {$forum['pk_id_forum']}")->fetch_assoc()["total"];

        $feed[] = [
            "tipo" => "forum",
            "usuario" => $forum["name_user"],
            "titulo" => $forum["nome"],
            "descricao" => $forum["description"],
            "comentarios" => $comentarios
        ];
    }

    $tipoFiltro = $_GET['tipo'] ?? '';
    $notaFiltro = $_GET['nota'] ?? '';

    $feed = array_filter($feed, function($item) use ($tipoFiltro, $notaFiltro) {

        // FILTRO POR TIPO
        if ($tipoFiltro && $item["tipo"] != $tipoFiltro) {
            return false;
        }

        // FILTRO POR NOTA (apenas se existir nota)
        if ($notaFiltro && isset($item["nota"])) {
            if ($item["nota"] < $notaFiltro) {
                return false;
            }
        }

        return true;
    });

    // DEIXAR ALEATÓRIO
    shuffle($feed);
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<form method="GET" style="text-align:center; margin-bottom:20px;">

    <!-- FILTRO POR TIPO -->
    <select name="tipo">
        <option value="">Tudo</option>
        <option value="review" <?= ($_GET['tipo'] ?? '') == 'review' ? 'selected' : '' ?>>Análises</option>
        <option value="imagem" <?= ($_GET['tipo'] ?? '') == 'imagem' ? 'selected' : '' ?>>Imagens</option>
        <option value="video" <?= ($_GET['tipo'] ?? '') == 'video' ? 'selected' : '' ?>>Vídeos</option>
        <option value="forum" <?= ($_GET['tipo'] ?? '') == 'forum' ? 'selected' : '' ?>>Discussões</option>
    </select>

    <!-- FILTRO POR NOTA -->
    <select name="nota">
        <option value="">Qualquer nota</option>
        <option value="5">5 estrelas</option>
        <option value="4">4+ estrelas</option>
        <option value="3">3+ estrelas</option>
    </select>

    <button type="submit">Filtrar</button>

</form>

<?php foreach ($feed as $item) { ?>
    <div class="card">
        <?php if ($item["tipo"] == "review") { ?>

            <h3>Análise</h3>
            <p><b><?= e($item["usuario"]) ?></b></p>
            <p><?= e($item["conteudo"]) ?></p>

            <div class="nota">
                <?php for ($i=1;$i<=5;$i++) { ?>
                    <i class="fa fa-star" <?= ($item["nota"] >= $i) ? 'style="color:gold"' : '' ?>></i>
                <?php } ?>
            </div>

        <?php } ?>

        <?php if ($item["tipo"] == "imagem") { ?>

            <h3>Captura</h3>
            <img class="img" src="<?= e($item["caminho"]) ?>" alt="<?= e($item["titulo"]) ?>">
            <p><b><?= e($item["titulo"]) ?></b></p>
            <p>Por: <?= e($item["usuario"]) ?></p>

        <?php } ?>

        <?php if ($item["tipo"] == "video") { ?>

            <h3>Vídeo</h3>
            <video class="img" controls>
                <source src="<?= e($item["caminho"]) ?>">
            </video>
            <p><b><?= e($item["titulo"]) ?></b></p>
            <p>Por: <?= e($item["usuario"]) ?></p>

        <?php } ?>

        <?php if ($item["tipo"] == "forum") { ?>

            <h3>Discussão</h3>
            <p><b><?= e($item["titulo"]) ?></b></p>
            <p><?= e($item["descricao"]) ?></p>
            <p>Por: <?= e($item["usuario"]) ?></p>
            <small><?= $item["comentarios"] ?> comentários</small>

        <?php } ?>
    </div>
<?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
