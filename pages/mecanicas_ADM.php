<?php
include "../include/conexao.php";
include "../include/id.php";
include "../include/login_php.php";

// PROTEÇÃO ADM
require_admin();

// DIRETÓRIO DE UPLOAD
// =======================
// AÇÕES DO ADMINISTRADOR
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $acao = $_POST['acao'] ?? null;
    $id = $_POST['id'] ?? null;
    $texto = trim($_POST['texto'] ?? '');

    if (empty($texto)) {
        die("O texto não pode estar vazio.");
    }

    $caminhoBanco = "";

    // Upload da imagem
    if (!empty($_FILES['imagem']['name'])) {
        $caminhoBanco = store_upload($_FILES['imagem'], 'mecanicas', 'image');
    }

    // CRIAR
    if ($acao === "criar") {
        db_stmt($conn, 'INSERT INTO mecanicas (texto, imagem) VALUES (?, ?)', 'ss', $texto, $caminhoBanco);
    }

    // EDITAR
    if ($acao === "editar") {
        $id = (int)$id;

        if (!empty($caminhoBanco)) {
            db_stmt($conn, 'UPDATE mecanicas SET texto=?, imagem=? WHERE pk_id_mecanicas=?', 'ssi', $texto, $caminhoBanco, $id);
        } else {
            db_stmt($conn, 'UPDATE mecanicas SET texto=? WHERE pk_id_mecanicas=?', 'si', $texto, $id);
        }
    }

    // EXCLUIR
    if ($acao === "delete") {
        $id = (int)$id;
        $conn->query("DELETE FROM mecanicas WHERE pk_id_mecanicas=$id");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// BUSCAR MECÂNICAS
$mecanicas = [];
$sql = "SELECT * FROM mecanicas ORDER BY pk_id_mecanicas DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $mecanicas[] = $row;
}
?>

<?php
    include "../include/header.php";
    include "../include/menu_adm.php";
    include "../include/login.php";
?>

<div class="container">
    <h2>Painel de Mecânicas</h2>

    <!-- FORMULÁRIO DE CADASTRO -->
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <textarea name="texto" placeholder="Descreva a mecânica do jogo..." required></textarea>
            <input type="file" name="imagem" accept="image/*">
            <button type="submit" name="acao" value="criar">Publicar</button>
        </form>
    </div>

    <!-- LISTAGEM -->
    <?php foreach ($mecanicas as $item): ?>
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $item['pk_id_mecanicas'] ?>">

            <textarea name="texto" required><?= e($item['texto']) ?></textarea>

                <?php if (!empty($item['imagem'])): ?>
                    <img src="<?= e($item['imagem']) ?>" width="200" alt="Imagem da mecânica"><br><br>
                <?php endif; ?>

                <input type="file" name="imagem" accept="image/*">

                <button type="submit" name="acao" value="editar">Salvar Alterações</button>
                <button type="submit" name="acao" value="delete"
                        onclick="return confirm('Deseja excluir esta mecânica?')">
                    Excluir
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<script src="../js/login.js"></script>
<?php include "../include/footer.php"; ?>
