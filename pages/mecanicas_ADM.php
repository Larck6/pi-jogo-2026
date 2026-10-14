<?php
include "../include/conexao.php";
include "../include/id.php";
include "../include/login_php.php";

// PROTEÇÃO ADM
if (!isset($_SESSION['adm']) || $_SESSION['adm'] !== true) {
    die("Acesso negado");
}

// DIRETÓRIO DE UPLOAD
$diretorio = "../uploads/mecanicas/";
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0755, true);
}

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

    $texto = $conn->real_escape_string($texto);
    $caminhoBanco = "";

    // Upload da imagem
    if (!empty($_FILES['imagem']['name'])) {
        $nomeImagem = time() . "_" . basename($_FILES['imagem']['name']);
        $caminhoCompleto = $diretorio . $nomeImagem;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            $caminhoBanco = "uploads/mecanicas/" . $nomeImagem;
        } else {
            die("Erro ao enviar a imagem.");
        }
    }

    // CRIAR
    if ($acao === "criar") {
        $sql = "INSERT INTO mecanicas (texto, imagem) 
                VALUES ('$texto', '$caminhoBanco')";
        if (!$conn->query($sql)) {
            die("Erro ao inserir: " . $conn->error);
        }
    }

    // EDITAR
    if ($acao === "editar") {
        $id = (int)$id;

        if (!empty($caminhoBanco)) {
            $sql = "UPDATE mecanicas 
                    SET texto='$texto', imagem='$caminhoBanco'
                    WHERE pk_id_mecanicas=$id";
        } else {
            $sql = "UPDATE mecanicas 
                    SET texto='$texto'
                    WHERE pk_id_mecanicas=$id";
        }

        if (!$conn->query($sql)) {
            die("Erro ao atualizar: " . $conn->error);
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
    include "../include/menu_ADM.php";
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

                <textarea name="texto" required><?= $item['texto'] ?></textarea>

                <?php if (!empty($item['imagem'])): ?>
                    <img src="../<?= $item['imagem'] ?>" width="200"><br><br>
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