<?php
require_once "../conn.php";

session_start();

if (isset($_SESSION["username"]) && isset($_SESSION["role"])) {
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
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'guru';
      header("Location: login.php");
    } elseif ($admin) {
      $_SESSION['username'] = $username;
      $_SESSION['role'] = "admin";
      header("Location: login.php");
    } else {
      $error = "Username atau Password salah! ⛔";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login ABSENBI</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100%;
    }

    .left {
      flex: 1;
      background: url("../assets/images/bg-login.png") no-repeat center;
      background-size: cover;
    }

    .right {
      flex: 1;
      background: #6a1b9a;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .right h2 {
      margin-bottom: 20px;
      font-size: 32px;
    }

    form {
      width: 80%;
      max-width: 400px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      width: 100%;
      padding: 14px;
      margin-top: 10px;
      background: #fff;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      font-size: 16px;
      color: #6a1b9a;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #e1bee7;
    }

    .error {
      color: #ffcccb;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="left"></div>
    <div class="right">
      <img src="../assets/images/logo_bi.png" style="width: 100px; height: 100px; top: 25px; right: 25px; position: absolute;">
      <h2>ABSENBI</h2>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="text" name="username" placeholder="Masukkan Username">
        <input type="password" name="password" placeholder="Masukkan Password">
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>

</html>