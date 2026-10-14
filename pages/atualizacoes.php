<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $sql = "SELECT * FROM atualizacoes ORDER BY pk_id_atualizacoes DESC";
    $result = $conn->query($sql);
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>
    <h2>Atualizações do Jogo</h2>

    <?php while ($item = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if (!empty($item['imagem'])): ?>
                <img src="<?= $item['imagem'] ?>" class="img-atualizacao">
            <?php endif; ?>

            <p><?= nl2br($item['texto']) ?></p>
        </div>
    <?php endwhile; ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>