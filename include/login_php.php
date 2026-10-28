<?php
    $show = $login;

    if (!function_exists('senhaConfere')) {
        function senhaConfere($senhaDigitada, $senhaSalva) {
            if (password_get_info((string) $senhaSalva)['algo'] !== null) {
                return password_verify($senhaDigitada, $senhaSalva);
            }
            return hash_equals((string) $senhaSalva, (string) $senhaDigitada);
        }
    }

    if (!function_exists('encerrarSessao')) {
        function encerrarSessao() {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }

            session_destroy();
        }
    }

    if (!function_exists('excluirContaUsuario')) {
        function excluirContaUsuario($conn, $idUser) {
            $idUser = (int) $idUser;

            if ($idUser <= 0) {
                return;
            }

            $fotoPerfil = null;
            $selectFoto = db_stmt($conn, "SELECT foto_user FROM user WHERE pk_id_user = ? LIMIT 1", 'i', $idUser)->get_result();

            if ($selectFoto && $selectFoto->num_rows > 0) {
                $fotoPerfil = $selectFoto->fetch_assoc()['foto_user'] ?? null;
            }

            $mediaFiles = [];
            foreach (['imagens' => 'caminho', 'videos' => 'caminho'] as $table => $column) {
                $result = db_stmt($conn, "SELECT $column FROM $table WHERE fk_id_user = ?", 'i', $idUser)->get_result();
                while ($row = $result->fetch_assoc()) {
                    $mediaFiles[] = $row[$column] ?? null;
                }
            }

            $conn->begin_transaction();
            try {
                db_stmt($conn, 'DELETE FROM feedback_video WHERE fk_id_user = ? OR fk_id_video IN (SELECT pk_id_video FROM videos WHERE fk_id_user = ?)', 'ii', $idUser, $idUser);
                db_stmt($conn, 'DELETE FROM feedback_imagem WHERE fk_id_user = ? OR fk_id_imagem IN (SELECT pk_id_imagem FROM imagens WHERE fk_id_user = ?)', 'ii', $idUser, $idUser);
                db_stmt($conn, 'DELETE FROM forum_comentario WHERE fk_id_user = ? OR fk_id_forum IN (SELECT pk_id_forum FROM forum WHERE fk_id_user = ?)', 'ii', $idUser, $idUser);
                db_stmt($conn, 'DELETE FROM feedback WHERE fk_id_user = ?', 'i', $idUser);
                db_stmt($conn, 'DELETE FROM videos WHERE fk_id_user = ?', 'i', $idUser);
                db_stmt($conn, 'DELETE FROM imagens WHERE fk_id_user = ?', 'i', $idUser);
                db_stmt($conn, 'DELETE FROM forum WHERE fk_id_user = ?', 'i', $idUser);
                db_stmt($conn, "DELETE FROM user WHERE pk_id_user = ?", 'i', $idUser);
                $conn->commit();
            } catch (Throwable $exception) {
                $conn->rollback();
                throw $exception;
            }
            delete_uploaded_file($fotoPerfil);
            foreach ($mediaFiles as $mediaFile) {
                delete_uploaded_file($mediaFile);
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        verify_csrf();

        if (isset($_POST['logout'])) {
            encerrarSessao();
            redirect_to('/index.php');
        }

        if (isset($_POST['switch_account'])) {
            encerrarSessao();
            redirect_to('/pages/login.php');
        }

        if (isset($_POST['delete_account']) && $idUser) {
            excluirContaUsuario($conn, $idUser);
            encerrarSessao();
            redirect_to('/pages/login.php');
        }

        if (($_POST['auth_action'] ?? '') === 'login') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'started_at' => time()];
            if (time() - (int) $attempts['started_at'] > 900) {
                $attempts = ['count' => 0, 'started_at' => time()];
            }
            if ((int) $attempts['count'] >= 5) {
                $_SESSION['login_error'] = 'Muitas tentativas. Aguarde 15 minutos e tente novamente.';
                redirect_to('/pages/login.php');
            }

            $stmt = $conn->prepare("SELECT * FROM user WHERE email_user = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $select = $stmt->get_result();
            $user = $select->fetch_assoc();

            if ($user && senhaConfere($senha, $user['password_user'])) {
                session_regenerate_id(true);
                $_SESSION['login'] = true;
                $_SESSION['id'] = $user['pk_id_user'];
                $_SESSION['adm'] = $user["adm"] == '1';
                unset($_SESSION['login_attempts']);

                if (password_get_info((string) $user['password_user'])['algo'] === null || password_needs_rehash($user['password_user'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($senha, PASSWORD_DEFAULT);
                    db_stmt($conn, 'UPDATE user SET password_user = ? WHERE pk_id_user = ?', 'si', $newHash, $user['pk_id_user']);
                }

                redirect_to('/index.php');
            }

            unset($_SESSION['id']);
            $attempts['count']++;
            $_SESSION['login_attempts'] = $attempts;
            $_SESSION['login'] = false;
            $_SESSION['adm'] = false;
            $_SESSION['login_error'] = 'E-mail ou senha incorretos.';

            redirect_to('/pages/login.php');
        }
    }
?>
