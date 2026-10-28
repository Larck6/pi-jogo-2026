<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $erroCadastro = null;

    if($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST['auth_action'] ?? '') === 'cadastro')
    {
        verify_csrf();
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $fotoURL = null;

        if ($nome === '' || $email === '' || $senha === '') {
            $erroCadastro = 'Preencha todos os campos obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erroCadastro = 'Informe um e-mail válido.';
        } elseif (mb_strlen($nome) > 60) {
            $erroCadastro = 'O nome deve ter no máximo 60 caracteres.';
        } elseif (strlen($senha) < 8) {
            $erroCadastro = 'A senha deve ter pelo menos 8 caracteres.';
        } else {
            $stmtCheck = $conn->prepare("SELECT pk_id_user FROM user WHERE email_user = ? LIMIT 1");
            $stmtCheck->bind_param("s", $email);
            $stmtCheck->execute();
            $emailExiste = $stmtCheck->get_result()->num_rows > 0;

            if ($emailExiste) {
                $erroCadastro = 'Já existe uma conta com esse e-mail.';
            }
        }

        if (!$erroCadastro && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            try {
                $fotoURL = store_upload($_FILES['foto'], 'perfis', 'image');
            } catch (RuntimeException $exception) {
                $erroCadastro = $exception->getMessage();
            }
        }

        if (!$erroCadastro) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmtInsert = $conn->prepare("INSERT INTO user (name_user, email_user, password_user, foto_user, quant_feedback, adm) VALUES (?, ?, ?, ?, 0, 0)");
            $stmtInsert->bind_param("ssss", $nome, $email, $senhaHash, $fotoURL);

            if ($stmtInsert->execute()) {
                $novoUsuarioId = (int) $conn->insert_id;

                session_regenerate_id(true);
                $_SESSION['login'] = true;
                $_SESSION['id'] = $novoUsuarioId;
                $_SESSION['adm'] = false;
                unset($_SESSION['login_attempts'], $_SESSION['login_error'], $_SESSION['cadastro_success']);

                redirect_to('/index.php');
            }

            delete_uploaded_file($fotoURL);
            $erroCadastro = 'Não foi possível criar a conta. Tente novamente.';
        }
    }
?>

<?php
    include "../include/header.php";
    include "../include/login.php";
?>

<main class="auth-shell">
    <section class="auth-card">
        <div class="auth-heading">
            <span class="auth-icon"><i class="fa fa-user-plus"></i></span>
            <h1>Criar conta</h1>
            <p>Cadastre seu usuário para participar da comunidade.</p>
        </div>

        <?php if ($erroCadastro) { ?>
            <p class="auth-message error"><?= htmlspecialchars($erroCadastro) ?></p>
        <?php } ?>

        <form action="<?= e(app_url('/pages/cadastro.php')) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="auth_action" value="cadastro">

            <label for="nome">Nome de usuário</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome no site" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>

            <label for="senha">Senha</label>
            <div class="password-field">
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>
                <button type="button" class="password-toggle" data-toggle-password="#senha" aria-label="Mostrar senha">
                    <i class="fa fa-eye"></i>
                </button>
            </div>

            <label for="foto">Foto de perfil</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Cadastrar</button>
        </form>

        <p class="auth-link">Já possui uma conta? <a href="<?= e(app_url('/pages/login.php')) ?>">Fazer login</a></p>
    </section>
</main>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
