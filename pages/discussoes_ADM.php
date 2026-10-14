<?php
session_start();
include "../include/conexao.php";
include "../include/id.php";
include "../include/login_php.php";

// PROTEÇÃO ADM
if (!isset($_SESSION['adm']) || $_SESSION['adm'] !== true) {
    die("Acesso negado");
}

// FILTRO
$filtro = $_GET['filtro'] ?? 'todos';

// CONTROLE DE COMENTÁRIOS
$show = $_GET['show'] ?? null;
$tipo_show = $_GET['tipo'] ?? null;

// =======================
// AÇÕES DO ADMINISTRADOR
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tipo = $_POST['tipo'] ?? null;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $acao = $_POST['acao'] ?? null;

    // Abrir comentários
    if ($acao === "abrir") {
        header("Location: {$_SERVER['PHP_SELF']}?show=$id&tipo=$tipo&filtro=$filtro");
        exit;
    }

    // Excluir tópico do fórum
    if ($acao === "delete" && $tipo === "forum") {
        $conn->query("DELETE FROM forum WHERE pk_id_forum = $id");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Excluir comentário do fórum
    if ($acao === "delete_comentario" && $tipo === "comentario_forum") {
        $conn->query("DELETE FROM forum_comentario WHERE pk_id_forum_comentario = $id");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// =======================
// BUSCAR DADOS DO FÓRUM
// =======================
$feed = [];

$sql = "
    SELECT 
        f.pk_id_forum AS id,
        f.nome,
        f.description,
        u.name_user,
        'forum' AS tipo
    FROM forum f
    JOIN user u ON f.fk_id_user = u.pk_id_user
    ORDER BY f.pk_id_forum DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feed[] = $row;
    }
}
?>

<?php 
    include "../include/header.php";
    include "../include/menu_ADM.php";
    include "../include/login.php";
?>

<div class="feed-container">
<?php foreach ($feed as $item): ?>
    <div class="card">

        <h3><?= $item['nome'] ?></h3>
        <p><strong>Autor:</strong> <?= $item['name_user'] ?></p>
        <p><?= $item['description'] ?></p>

        <!-- BOTÃO EXCLUIR TÓPICO -->
        <form method="POST">
            <input type="hidden" name="tipo" value="forum">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <button type="submit" name="acao" value="delete">Excluir Tópico</button>
        </form>

        <!-- BOTÃO COMENTÁRIOS -->
        <form method="POST">
            <input type="hidden" name="tipo" value="forum">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <button type="submit" name="acao" value="abrir">Comentários</button>
        </form>

        <!-- ÁREA DE COMENTÁRIOS -->
        <?php if ($show == $item['id'] && $tipo_show == 'forum'): ?>
            <div class="comentarios">
                <form method="GET">
                    <input type="hidden" name="filtro" value="<?= $filtro ?>">
                    <button type="submit">Fechar</button>
                </form>

                <h4>Comentários:</h4>

                <?php
                $idForum = (int)$item['id'];
                $comentarios = $conn->query("
                    SELECT 
                        fc.pk_id_forum_comentario,
                        fc.comentario,
                        u.name_user
                    FROM forum_comentario fc
                    JOIN user u ON fc.fk_id_user = u.pk_id_user
                    WHERE fc.fk_id_forum = $idForum
                    ORDER BY fc.pk_id_forum_comentario DESC
                ");

                if ($comentarios && $comentarios->num_rows > 0):
                    while ($c = $comentarios->fetch_assoc()):
                ?>
                        <p><strong><?= $c['name_user'] ?>:</strong> <?= $c['comentario'] ?></p>

                        <form method="POST">
                            <input type="hidden" name="tipo" value="comentario_forum">
                            <input type="hidden" name="id" value="<?= (int)$c['pk_id_forum_comentario'] ?>">
                            <button type="submit" name="acao" value="delete_comentario">
                                Excluir
                            </button>
                        </form>
                        <hr>
                <?php
                    endwhile;
                else:
                    echo "<p>Nenhum comentário encontrado.</p>";
                endif;
                ?>
            </div>
        <?php endif; ?>

    </div>
<?php endforeach; ?>
</div>

<script src="../js/login.js"></script>
<?php include "../include/footer.php"; ?>