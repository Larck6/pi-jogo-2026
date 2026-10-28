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
            $caminhoBanco = store_upload($_FILES['imagem'], 'atualizacoes', 'image');
        }

        // CRIAR ATUALIZAÇÃO
        if ($acao === "criar") {
            db_stmt($conn, 'INSERT INTO atualizacoes (texto, imagem) VALUES (?, ?)', 'ss', $texto, $caminhoBanco);
        }

        // EDITAR ATUALIZAÇÃO
        if ($acao === "editar") {
            $id = (int)$id;

            if (!empty($caminhoBanco)) {
                db_stmt($conn, 'UPDATE atualizacoes SET texto=?, imagem=? WHERE pk_id_atualizacoes=?', 'ssi', $texto, $caminhoBanco, $id);
            } else {
                db_stmt($conn, 'UPDATE atualizacoes SET texto=? WHERE pk_id_atualizacoes=?', 'si', $texto, $id);
            }
        }

        // EXCLUIR ATUALIZAÇÃO
        if ($acao === "delete") {
            $id = (int)$id;

            // Excluir imagem do servidor
            $result = $conn->query("SELECT imagem FROM atualizacoes WHERE pk_id_atualizacoes=$id");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                delete_uploaded_file($row['imagem'] ?? null);
            }

            $conn->query("DELETE FROM atualizacoes WHERE pk_id_atualizacoes=$id");
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // =======================
    // BUSCAR ATUALIZAÇÕES
    // =======================
    $atualizacoes = [];
    $sql = "SELECT * FROM atualizacoes ORDER BY pk_id_atualizacoes DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $atualizacoes[] = $row;
    }
?>

<?php
    include "../include/header.php";
    include "../include/menu_adm.php";
    include "../include/login.php";
?>

<!-- FORMULÁRIO DE CADASTRO -->
<div class="card">
    <form method="POST" enctype="multipart/form-data">
        <textarea name="texto" placeholder="Descreva a atualização..." required></textarea>
        <input type="file" name="imagem" accept="image/*">
        <button type="submit" name="acao" value="criar">Publicar</button>
    </form>
</div>

<!-- LISTAGEM DAS ATUALIZAÇÕES -->
<?php foreach ($atualizacoes as $item): ?>
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $item['pk_id_atualizacoes'] ?>">

            <textarea name="texto" required><?= e($item['texto']) ?></textarea>

            <?php if (!empty($item['imagem'])): ?>
                <img src="<?= e($item['imagem']) ?>" width="200" alt="Imagem da atualização"><br><br>
            <?php endif; ?>

            <input type="file" name="imagem" accept="image/*">

            <button type="submit" name="acao" value="editar">Salvar Alterações</button>
            <button type="submit" name="acao" value="delete" onclick="return confirm('Deseja excluir esta atualização?')">
                Excluir
            </button>
        </form>
    </div>
<?php endforeach; ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php"; ?>
