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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Guru</title>
    <style>
        body {font-family: Arial, sans-serif; background: #f4f4f4; text-align: center;}
        .login-box {width: 350px; margin: 100px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.2);}
        input {width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;}
        button {width: 95%; padding: 10px; background: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer;}
        button:hover {background: #0056b3;}
        .error {color: red; margin-top: 10px;}
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Guru</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Masukkan Username" required><br>
            <input type="password" name="password" placeholder="Masukkan Password" required><br>
            <button type="submit">Login</button>
        </form>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
    </div>
</body>
</html>
<?php