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

    // Criar conteúdo
    if ($acao === "criar") {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descricao = $conn->real_escape_string($_POST['descricao']);
        $user = $_SESSION['id'];

        if ($tipo === "atualizacao") {
            $conn->query("
                INSERT INTO atualizacoes (titulo, descricao, data_post, fk_id_user)
                VALUES ('$titulo', '$descricao', NOW(), $user)
            ");
        }

        if ($tipo === "mecanica") {
            $conn->query("
                INSERT INTO mecanicas (titulo, descricao, fk_id_user)
                VALUES ('$titulo', '$descricao', $user)
            ");
        }

        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }

    // Excluir vídeo
    if ($acao === "delete" && $tipo === "video") {
        $conn->query("DELETE FROM videos WHERE pk_id_video = $id");
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }

    // Excluir comentário
    if ($acao === "delete_comentario" && $tipo === "comentario_video") {
        $conn->query("DELETE FROM feedback_video WHERE pk_id_feedback_video = $id");
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }
}

// =======================
// BUSCAR VÍDEOS DO BANCO
// =======================
$feed = [];
$sql = "SELECT pk_id_video AS id, caminho, 'video' AS tipo FROM videos ORDER BY pk_id_video DESC";
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

        <!-- VÍDEO -->
        <?php if ($item['tipo'] === 'video'): ?>
            <video width="250" controls>
                <source src="../<?= htmlspecialchars($item['caminho']) ?>" type="video/mp4">
                Seu navegador não suporta a reprodução de vídeos.
            </video>
        <?php endif; ?>

        <!-- BOTÃO EXCLUIR -->
        <form method="POST">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($item['tipo']) ?>">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <button type="submit" name="acao" value="delete">Excluir</button>
        </form>

        <!-- BOTÃO COMENTÁRIOS -->
        <form method="POST">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($item['tipo']) ?>">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <button type="submit" name="acao" value="abrir">Comentários</button>
        </form>

        <!-- ÁREA DE COMENTÁRIOS -->
        <?php if ($show == $item['id'] && $tipo_show == $item['tipo']): ?>
            <div class="comentarios">
                <form method="GET">
                    <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtro) ?>">
                    <button type="submit">Fechar</button>
                </form>

                <h4>Comentários:</h4>

                <?php
                $idVideo = (int)$item['id'];
                $comentarios = $conn->query("
                    SELECT * FROM feedback_video 
                    WHERE fk_id_video = $idVideo
                ");

                if ($comentarios && $comentarios->num_rows > 0):
                    while ($c = $comentarios->fetch_assoc()):
                ?>
                        <p><?= htmlspecialchars($c['comentario']) ?></p>
                        <form method="POST">
                            <input type="hidden" name="tipo" value="comentario_video">
                            <input type="hidden" name="id" value="<?= (int)$c['pk_id_feedback_video'] ?>">
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