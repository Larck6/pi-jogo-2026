<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";

    $user_row = null;
    $quant_feed = 0;

    if ($idUser) {
        $user_result = $conn->query("SELECT * FROM user WHERE pk_id_user = '$idUser'");
        
        if ($user_result && $user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $quant_feed = $user_row['quant_feedback'] ?? 0;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $action = $_POST["btn"] ?? '';

        if ($action == "create" && $logado && $idUser) {

            if ($quant_feed > 0) {
                echo "<script>alert('Você já enviou um feedback.');</script>";
            } else {
                $feedback = $_POST['feedback'] ?? '';
                $nota = $_POST['nota'] ?? '';

                $conn->query("INSERT INTO feedback (feedback, nota, fk_id_user) VALUES ('$feedback', '$nota', '$idUser')");
                $conn->query("UPDATE user SET quant_feedback = 1 WHERE pk_id_user = '$idUser'");

                header("Location: /pi/pages/feedback.php");
                exit;
            }
        }

        if ($action == "delete" && $logado && $idUser) {

            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $conn->query("DELETE FROM feedback WHERE pk_id_feedback = '$idFeedback' AND fk_id_user = '$idUser'");
                $conn->query("UPDATE user SET quant_feedback = 0 WHERE pk_id_user = '$idUser'");
            }

            header("Location: /pi/pages/feedback.php");
            exit;
        }

        if ($action == "edit" && $logado && $idUser) {

            $idFeedback = $_POST['idf'] ?? null;

            if ($idFeedback) {
                $_SESSION["id_feedback"] = $idFeedback;
                header("Location: /pi/pages/feedback_edit.php");
                exit;
            }
        }
    }

    $select = $conn->query("SELECT feedback.pk_id_feedback, feedback.feedback, feedback.nota, user.name_user, feedback.fk_id_user FROM feedback JOIN user ON feedback.fk_id_user = user.pk_id_user");

    $userlogado = null;
    if ($idUser) {
        $userlogado = $conn->query("SELECT feedback.pk_id_feedback, feedback.feedback, feedback.nota, user.name_user, feedback.fk_id_user FROM feedback JOIN user ON feedback.fk_id_user = user.pk_id_user WHERE fk_id_user = '$idUser'");
    }

    $user_feedback = ($userlogado && $userlogado->num_rows > 0) ? $userlogado->fetch_assoc() : null;


    $ordem = $_GET['ordem'] ?? '';

    $orderSQL = "";

    if ($ordem == "maior") {
        $orderSQL = "ORDER BY feedback.nota DESC";
    } elseif ($ordem == "menor") {
        $orderSQL = "ORDER BY feedback.nota ASC";
    }

    $select = $conn->query("SELECT feedback.pk_id_feedback, feedback.feedback, feedback.nota, user.name_user, feedback.fk_id_user FROM feedback JOIN user ON feedback.fk_id_user = user.pk_id_user $orderSQL");
?>

<?php
    include "../include/header.php";
    include "../include/comunidade_menu.php";
    include "../include/login.php";
?>

<?php if ($logado) { ?>
<section>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="text" name="feedback" placeholder="Feedback" required>
        <div class="estrelas">
            <input type="radio" id="nota1" name="nota" value="5" />
            <label for="nota1"><i class="fa"></i></label>

            <input type="radio" id="nota2" name="nota" value="4" />
            <label for="nota2"><i class="fa"></i></label>

            <input type="radio" id="nota3" name="nota" value="3" />
            <label for="nota3"><i class="fa"></i></label>

            <input type="radio" id="nota4" name="nota" value="2" />
            <label for="nota4"><i class="fa"></i></label>

            <input type="radio" id="nota5" name="nota" value="1" />
            <label for="nota5"><i class="fa"></i></label>
        </div>
        <button type="submit" name="btn" value="create">Enviar</button>
    </form>
</section>
<?php } else { ?>
    <p>Você precisa estar logado para enviar um feedback.</p>
<?php } ?>

<form method="GET" style="margin-bottom: 15px;">
    <label for="ordem">Ordenar por nota:</label>
    <select name="ordem" id="ordem" onchange="this.form.submit()">
        <option value="">Padrão</option>
        <option value="maior" <?= (isset($_GET['ordem']) && $_GET['ordem'] == 'maior') ? 'selected' : '' ?>>
            Maior → Menor
        </option>
        <option value="menor" <?= (isset($_GET['ordem']) && $_GET['ordem'] == 'menor') ? 'selected' : '' ?>>
            Menor → Maior
        </option>
    </select>
</form>

<div>
    <?php if ($logado && $user_feedback) { ?>
        <div>
            <div><?= $user_feedback["feedback"] ?></div>
            <div class="nota_estrela">
                <?php for ($i = 1; $i <= 5; $i++){ ?>
                    <i class="fa fa-star" id="<?= ($user_feedback["nota"] >= $i) ? "nota_checked" : ""; ?>"></i>
                <?php } ?>
            </div>
            <div><?= $user_feedback["name_user"] ?></div>
            <div>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <input type="hidden" name="idf" value="<?= $user_feedback["pk_id_feedback"] ?>">
                    <button type="submit" name="btn" value="edit">Editar</button>
                </form>
            </div>
            <div>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <input type="hidden" name="idf" value="<?= $user_feedback["pk_id_feedback"] ?>">
                    <button type="submit" name="btn" value="delete">Excluir</button>
                </form>
            </div>
        </div>
    <?php } ?>

    <?php while ($row = $select->fetch_assoc()) { ?>
        <?php if (!($logado && $row["fk_id_user"] == $idUser)) { ?>
            <div>
                <div><?= $row['feedback'] ?></div>
                <div class="nota_estrela">
                    <?php for ($i = 1; $i <= 5; $i++){ ?>
                        <i class="fa fa-star" id="<?= ($row["nota"] >= $i) ? "nota_checked" : ""; ?>"></i>
                    <?php } ?>
                </div>
                <div><?= $row["name_user"] ?></div>
            </div>
        <?php } ?>
    <?php } ?>

<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>