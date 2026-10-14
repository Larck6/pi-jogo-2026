<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
    

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $conn->query("INSERT INTO user (name_user, email_user, password_user) VALUE ('$nome', '$email', '$senha')");
        header("Location: /pi/pages/login.php");
    }
?>

<?php
    include "../include/header.php";
    include "../include/login_php.php";
?>

<section>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="text" name="nome" placeholder="Nome">
        <input type="text" name="email" placeholder="E-mail">
        <input type="text" name="senha" placeholder="Senha">
        <button type="submit">Enviar</button>
        <a href="/pi/pages/login.php">Fazer login</a>
    </form>
</section>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>