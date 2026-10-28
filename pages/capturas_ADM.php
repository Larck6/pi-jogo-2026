<?php
include "../include/conexao.php";
include "../include/id.php";
include "../include/login_php.php";

// PROTEÇÃO ADM
require_admin();

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

    // Excluir imagem
    if ($acao === "delete" && $tipo === "imagem") {
        $conn->query("DELETE FROM imagens WHERE pk_id_imagem = $id");
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }

    // Excluir comentário
    if ($acao === "delete_comentario" && $tipo === "comentario_imagem") {
        $conn->query("DELETE FROM feedback_imagem WHERE pk_id_feedback_imagem = $id");
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }
}

// =======================
// BUSCAR IMAGENS DO BANCO
// =======================
$imagens = $conn->query("
    SELECT 
        imagens.pk_id_imagem AS id,
        imagens.nome_imagem,
        imagens.caminho,
        user.name_user,
        'imagem' AS tipo
    FROM imagens
    JOIN user ON imagens.fk_id_user = user.pk_id_user
    ORDER BY imagens.pk_id_imagem DESC
");
?>

<?php 
    include "../include/header.php";
    include "../include/menu_adm.php";
    include "../include/login.php";
?>

<div class="feed-container">
<?php if ($imagens && $imagens->num_rows > 0): ?>
    <?php while ($item = $imagens->fetch_assoc()): ?>
        <div class="card">

            <!-- IMAGEM -->
            <img src="<?= e($item['caminho']) ?>"
                 alt="<?= e($item['nome_imagem']) ?>"
                 width="250">

            <h4><?= e($item['nome_imagem']) ?></h4>
            <p><strong>Autor:</strong> <?= e($item['name_user']) ?></p>

            <!-- BOTÃO EXCLUIR -->
            <form method="POST">
                <input type="hidden" name="tipo" value="imagem">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <button type="submit" name="acao" value="delete">Excluir</button>
            </form>

            <!-- BOTÃO COMENTÁRIOS -->
            <form method="POST">
                <input type="hidden" name="tipo" value="imagem">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <button type="submit" name="acao" value="abrir">Comentários</button>
            </form>

            <!-- ÁREA DE COMENTÁRIOS -->
            <?php if ($show == $item['id'] && $tipo_show == 'imagem'): ?>
                <div class="comentarios">
                    <form method="GET">
                        <input type="hidden" name="filtro" value="<?= $filtro ?>">
                        <button type="submit">Fechar</button>
                    </form>

                    <h4>Comentários:</h4>

                    <?php
                    $idImagem = (int)$item['id'];
                    $comentarios = $conn->query("
                        SELECT * FROM feedback_imagem 
                        WHERE fk_id_imagem = $idImagem
                    ");

                    if ($comentarios && $comentarios->num_rows > 0):
                        while ($c = $comentarios->fetch_assoc()):
                    ?>
                            <p><?= e($c['comentario']) ?></p>
                            <form method="POST">
                                <input type="hidden" name="tipo" value="comentario_imagem">
                                <input type="hidden" name="id" value="<?= (int)$c['pk_id_feedback_imagem'] ?>">
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
    <?php endwhile; ?>
<?php endif; ?>
</div>

<script src="../js/login.js"></script>
<?php include "../include/footer.php"; ?>
