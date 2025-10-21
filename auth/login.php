<?php
    require_once '../conn.php';
    session_start();

    function redirect($targeturl) {
        header('Location: ' . $targeturl);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('../');
    }
    
    $requestauth = $_POST['requestauth'];

    if ($requestauth !== 'login') {
        redirect('../');
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    function cekguru($username, $password) {

    }

    $stmt = $conn->prepare('SELECT * FROM guru WHERE username="" AND ')