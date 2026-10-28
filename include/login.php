<?php
    $contaUsuario = null;

    if ($logado && $idUser) {
        $stmtConta = $conn->prepare("SELECT name_user, email_user, foto_user FROM user WHERE pk_id_user = ? LIMIT 1");
        $stmtConta->bind_param("i", $idUser);
        $stmtConta->execute();
        $contaUsuario = $stmtConta->get_result()->fetch_assoc();
    }
?>

<?php if ($logado && $contaUsuario) { ?>
    <div class="account-backdrop" id="accountBackdrop"></div>

    <aside class="account-panel" id="accountPanel" aria-hidden="true">
        <button type="button" class="account-close" id="closeAccount" aria-label="Fechar menu da conta">
            <i class="fa fa-times"></i>
        </button>

        <div class="account-profile">
            <?php if (!empty($contaUsuario['foto_user'])) { ?>
                <img class="account-photo" src="<?= htmlspecialchars($contaUsuario['foto_user']) ?>" alt="Foto de perfil">
            <?php } else { ?>
                <div class="account-photo account-photo-empty">
                    <i class="fa fa-user"></i>
                </div>
            <?php } ?>

            <strong><?= htmlspecialchars($contaUsuario['name_user']) ?></strong>
            <span><?= htmlspecialchars($contaUsuario['email_user']) ?></span>
        </div>

        <div class="account-actions">
            <form action="<?= e(app_url('/index.php')) ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" name="switch_account" value="true">
                    <i class="fa fa-exchange"></i>
                    Mudar de Conta
                </button>
            </form>

            <form action="<?= e(app_url('/index.php')) ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" name="logout" value="true">
                    <i class="fa fa-sign-out"></i>
                    Sair da Conta
                </button>
            </form>

            <form action="<?= e(app_url('/index.php')) ?>" method="post" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Essa acao nao pode ser desfeita.');">
                <?= csrf_field() ?>
                <button type="submit" name="delete_account" value="true" class="danger">
                    <i class="fa fa-trash"></i>
                    Excluir Conta
                </button>
            </form>
        </div>
    </aside>
<?php } ?>
