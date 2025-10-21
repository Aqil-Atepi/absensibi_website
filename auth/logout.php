<?php
    require_once '../conn.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestauth = $_POST['requestauth'];

        if ($requestauth === 'logout') {
            session_unset();
            session_destroy();
            header('Location: ../');
            exit;
        }
        else {
            header('Location: ../')
        }
    }
    else
    {
        header('Location: ../');
    }