<?php
require_once "../conn.php";

session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"])) {
    header("Location: login.php");
}

function redirect($targeturl)
{
    header("Location: " . $targeturl);
    exit;
}

function cekuser($conn, $target, $username, $password)
{
    $stmt = $conn->prepare("SELECT * FROM $target WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return false;
    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Tolong isi semua field yang tersedia ⚠️";
    } else {
        $guru = cekuser($conn, "guru", $username, $password);
        $admin = cekuser($conn, "administratif", $username, $password);

        if ($guru) {
            $_SESSION['id'] = $guru['nik'];
            $_SESSION['role'] = 'guru';
            header("Location: login.php");
        } elseif ($admin) {
            $_SESSION['id'] = $admin['id'];
            $_SESSION['role'] = "admin";
            header("Location: login.php");
        } else {
            $error = "Username atau Password salah! ⛔";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/main.css" rel="stylesheet">
    <link href="../assets/images/logo-bi.png" rel="icon">
    <title>Login</title>
    <style>
        .containerLogin {
            width: 100vw;
            height: 100vh;

            display: flex;
        }

        .background-doodles {
            flex: 1;
            background: url("../assets/images/doodle.png") no-repeat center;
            background-size: cover;
        }

        .content {
            flex: 1;
            background-color: var(--color1);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content img {
            width: 100px;
            height: 100px;

            position: absolute;
            top: 0;
            right: 0;

            margin: 20px;
        }

        .content form {
            width: 500px;
            height: 500px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .content form h1 {
            margin-bottom: 25px;

            color: var(--color3);
            font-size: 50px;
            font-weight: bold;
        }

        .content form input {
            width: 80%;
            height: auto;

            margin-top: 15px;
            padding: 10px 15px;

            border: 0px hidden;
            border-radius: 10px;

            background-color: var(--color2);

            font-size: 15px;
        }

        .content form button {
            width: 50%;
            height: auto;

            margin-top: 15px;
            padding: 10px;

            border: 0px hidden;
            border-radius: 10px;

            background-color: var(--color2);

            font-weight: bold;
        }

        .error {
            width: 80%;
            height: auto;

            margin-top: 15px;
            padding: 10px 15px;

            border: 1px dashed var(--color3);
            border-radius: 10px;

            color: var(--color3);

            text-align: center;
        }
    </style>
</head>

<body>
    <div class="containerLogin">
        <div class="background-doodles"></div>
        <div class="content">
            <img src="../assets/images/logo-bi.png" alt="SMK Bina Informatika">
            <form method="POST">
                <h1>AbsenBI</h1>
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif;?>
                <input type="text" name="username" placeholder="Masukkan Username">
                <input type="password" name="password" placeholder="Masukkan Password">
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>

</html>