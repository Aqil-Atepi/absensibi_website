<?php
    require_once '../conn.php';
    session_start();

    function redirect($targeturl) {
        header('Location: ' . $targeturl);
        exit;
    }

    function cekuser($target, $username, $password) {
        $stmt = $conn->prepare('SELECT * FROM ? WHERE username=?');
        $stmt->bind_param("ss", $target, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        else {
            return false;
        }
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('../');
    }
    
    $requestauth = $_POST['requestauth'];

    if ($requestauth !== 'login') {
        redirect('../');
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Tolong isi semua field yang tersedia ⚠️';
    }

    $guru = cekuser('guru', $username);
    $admin = cekuser('administratif', $username);

    if ($guru) {
        $_SESSION['username'] = $username;
    }

    if ($admin) {
        $_SESSION['username'] = $username;
    }

    $error = 'Username atau Password salah! ⛔';