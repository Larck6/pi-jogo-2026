<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    // PROTEÇÃO ADM
    if (!isset($_SESSION['adm']) || $_SESSION['adm'] !== true) {
        die("Acesso negado");
    }

    // DIRETÓRIO DE UPLOAD
    $diretorio = "../uploads/atualizacoes/";
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
                $caminhoBanco = "uploads/atualizacoes/" . $nomeImagem;
            } else {
                die("Erro ao enviar a imagem.");
            }
        }

        // CRIAR ATUALIZAÇÃO
        if ($acao === "criar") {
            $sql = "INSERT INTO atualizacoes (texto, imagem) 
                    VALUES ('$texto', '$caminhoBanco')";
            if (!$conn->query($sql)) {
                die("Erro ao inserir: " . $conn->error);
            }
        }

        // EDITAR ATUALIZAÇÃO
        if ($acao === "editar") {
            $id = (int)$id;

            if (!empty($caminhoBanco)) {
                $sql = "UPDATE atualizacoes 
                        SET texto='$texto', imagem='$caminhoBanco'
                        WHERE pk_id_atualizacoes=$id";
            } else {
                $sql = "UPDATE atualizacoes 
                        SET texto='$texto'
                        WHERE pk_id_atualizacoes=$id";
            }

            if (!$conn->query($sql)) {
                die("Erro ao atualizar: " . $conn->error);
            }
        }

        // EXCLUIR ATUALIZAÇÃO
        if ($acao === "delete") {
            $id = (int)$id;

            // Excluir imagem do servidor
            $result = $conn->query("SELECT imagem FROM atualizacoes WHERE pk_id_atualizacoes=$id");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $arquivo = "../" . $row['imagem'];
                if (!empty($row['imagem']) && file_exists($arquivo)) {
                    unlink($arquivo);
                }
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
    include "../include/menu_ADM.php";
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

            <textarea name="texto" required><?= $item['texto'] ?></textarea>

            <?php if (!empty($item['imagem'])): ?>
                <img src="../<?= $item['imagem'] ?>" width="200"><br><br>
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