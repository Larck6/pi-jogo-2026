<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $host = "localhost";
    $user = "root";
    $pass = "";
    $name = "game_db";

    $conn = new mysqli($host, $user, $pass, $name);
?>