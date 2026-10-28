<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $erroLogin = $_SESSION['login_error'] ?? null;
    $sucessoCadastro = $_SESSION['cadastro_success'] ?? null;

    unset($_SESSION['login_error'], $_SESSION['cadastro_success']);
?>

<?php
    include "../include/header.php";
    include "../include/login.php";
?>

<main class="auth-shell">
    <section class="auth-card">
        <div class="auth-heading">
            <span class="auth-icon"><i class="fa fa-user"></i></span>
            <h1>Entrar na conta</h1>
            <p>Acesse sua conta para publicar, comentar e baixar o jogo.</p>
        </div>

        <?php if ($erroLogin) { ?>
            <p class="auth-message error"><?= htmlspecialchars($erroLogin) ?></p>
        <?php } ?>

        <?php if ($sucessoCadastro) { ?>
            <p class="auth-message success"><?= htmlspecialchars($sucessoCadastro) ?></p>
        <?php } ?>

        <form action="<?= e(app_url('/pages/login.php')) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="auth_action" value="login">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>

            <label for="senha">Senha</label>
            <div class="password-field">
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                <button type="button" class="password-toggle" data-toggle-password="#senha" aria-label="Mostrar senha">
                    <i class="fa fa-eye"></i>
                </button>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p class="auth-link">Não possui uma conta? <a href="<?= e(app_url('/pages/cadastro.php')) ?>">Cadastre-se!</a></p>
    </section>
</main>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
