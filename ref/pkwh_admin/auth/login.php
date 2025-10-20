<?php
// auth/login.php
session_start();
require_once __DIR__ . '/../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // cek ke tabel admin
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :u AND password = :p LIMIT 1");
    $stmt->execute(['u' => $username, 'p' => $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'role' => 'admin',
            'nama' => $admin['nama'],
            'username' => $admin['username']
        ];
        header('Location: /pkwh_admin/dashboard.php');
        exit;
    } else {
        $error = "User / Password salah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login ABSENBI</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0; font-family: Arial, sans-serif;
    }
    .container {
      display: flex;
      height: 100vh;  /* full tinggi layar */
      width: 100%;    /* full lebar layar */
    }
    .left {
      flex: 1;
      background: url('../assets/bg-login.png') no-repeat center;
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
    .error { color: #ffcccb; margin-bottom: 15px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="left"></div>
    <div class="right">
      <h2>ABSENBI</h2>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="text" name="username" placeholder="User" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
