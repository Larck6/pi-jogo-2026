<?php
    $show = $login;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: /pi/index.php");
            exit;
        }

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $select = $conn->query("SELECT * FROM user WHERE email_user = '$email' LIMIT 1");
        $user = $select->fetch_assoc();

        if ($user && $senha == $user['password_user'] && $user["adm"] == '1') {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $user['pk_id_user'];
            $_SESSION['adm'] = true;

            header("Location: /pi/index.php");
            exit;
        }
        if ($user && $senha == $user['password_user'] && $user["adm"] == '0') {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $user['pk_id_user'];
            $_SESSION['adm'] = false;

            header("Location: /pi/index.php");
            exit;
        } else {
            $_SESSION['login'] = false;
            $_SESSION['adm'] = false;
        }
    }
?>