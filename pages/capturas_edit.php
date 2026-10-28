<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
    require_login();

    $idImagem = (int) $idImagem;
    $sql = db_stmt($conn, 'SELECT * FROM imagens WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ii', $idImagem, $idUser)->get_result();

    $dados = $sql->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $imagem = trim($_POST['nome'] ?? '') ?: $dados['nome_imagem'];
        $caminhoURL = $dados['caminho'];
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $novo = store_upload($_FILES['imagem'], 'capturas', 'image');
            delete_uploaded_file($caminhoURL);
            $caminhoURL = $novo;
        }
        db_stmt($conn, 'UPDATE imagens SET nome_imagem = ?, caminho = ? WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ssii', $imagem, $caminhoURL, $idImagem, $idUser);
        redirect_to('/pages/capturas.php');
    }
    // SELECT IMAGENS
    $img = db_stmt($conn, 'SELECT * FROM imagens WHERE pk_id_imagem = ? AND fk_id_user = ?', 'ii', $idImagem, $idUser)->get_result();
?>

<?php 
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome da imagem" required>
    <input type="file" name="imagem" accept="image/jpeg,image/png,image/gif,image/webp">
    <button type="submit" name="btn" value="upload_image">Enviar</button>
</form>

<?php 
    while($row = $img->fetch_assoc()){
?>
    <img class="img" src="<?= e($row['caminho']) ?>" alt="<?= e($row['nome_imagem']) ?>">

    <p><b><?= e($row['nome_imagem']) ?></b></p>
<?php
    }
?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
