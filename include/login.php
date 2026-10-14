<section class="login" id="loginBox">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="text" name="email" placeholder="E-mail">
        <input type="text" name="senha" placeholder="Senha">
        <button type="submit">Enviar</button>
        <a href="/pi/pages/cadastro.php">Fazer cadastro</a>
    </form>

    <div class="logout<?= $show ? " show" : '' ?>">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <button type="submit" name="logout" value="true">Logout</button>
        </form>
    </div>
</section>