<?php
    require_once '../conn.php';

    session_start();

    if (isset($_SESSION["id"])) {
        echo"" . $_SESSION['role'];
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') {
            header('Location: ../pages/guru/');
        }
        elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            header('Location: ../pages/admin/');
        }
        else {
            header('Location: ../');
        }
    }
    else {
        echo "Incomplete ⛔";
    }
?>