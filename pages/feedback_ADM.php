<?php
include "../include/conexao.php";
include "../include/id.php";
include "../include/login_php.php";

// PROTEÇÃO ADM
if (!isset($_SESSION['adm']) || $_SESSION['adm'] !== true) {
    die("Acesso negado");
}

// =======================
// AÇÕES DO ADMINISTRADOR
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tipo = $_POST['tipo'] ?? null;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $acao = $_POST['acao'] ?? null;

    // Excluir feedback
    if ($acao === "delete" && $tipo === "feedback") {
        $conn->query("DELETE FROM feedback WHERE pk_id_feedback = $id");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// =======================
// BUSCAR FEEDBACKS DO BANCO
// =======================
$feed = [];

$sql = "
    SELECT 
        f.pk_id_feedback AS id,
        f.feedback,
        f.nota,
        u.name_user
    FROM feedback f
    JOIN user u ON f.fk_id_user = u.pk_id_user
    ORDER BY f.pk_id_feedback DESC
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

        <h3>Feedback de <?= $item['name_user'] ?></h3>

        <p><strong>Comentário:</strong></p>
        <p><?= $item['feedback'] ?></p>

        <p><strong>Nota:</strong> ⭐ <?= $item['nota'] ?>/5</p>

        <!-- BOTÃO EXCLUIR -->
        <form method="POST">
            <input type="hidden" name="tipo" value="feedback">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <button type="submit" name="acao" value="delete">Excluir</button>
        </form>

    </div>
<?php endforeach; ?>
</div>

<script src="../js/login.js"></script>
<?php include "../include/footer.php"; ?>