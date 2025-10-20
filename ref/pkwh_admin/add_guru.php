<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /pkwh_admin/auth/login.php");
    exit;
}
require_once "db.php";

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $nik = trim($_POST['nik']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if ($nama && $nik && $role && $password) {
        $stmt = $pdo->prepare("INSERT INTO guru (nama, nik, role, password, foto) VALUES (:n, :nik, :r, :p, NULL)");
        $stmt->execute([
            'n'   => $nama,
            'nik' => $nik,
            'r'   => $role,
            'p'   => $password, // tanpa hash, sesuai permintaan
        ]);
        header("Location: manage_guru.php");
        exit;
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Guru</title>
  <style>
    body {margin:0;font-family:Arial,sans-serif;background:#f3f3f3;}
    .container {display:flex;min-height:100vh;}

    .sidebar {
      width:80px;background:#fff;box-shadow:2px 0 10px rgba(0,0,0,0.1);
      display:flex;flex-direction:column;align-items:center;padding:20px 0;position:fixed;top:0;bottom:0;left:0;
    }
    .sidebar a {padding:15px;margin:15px 0;display:block;border-radius:12px;}
    .sidebar a.active {background:#6a1b9a;}
    .sidebar a.active img {filter:brightness(0) invert(1);}

    .main {flex:1;padding:40px;margin-left:80px;}
    .card {
      background:#fff;padding:30px;border-radius:12px;
      box-shadow:0 2px 6px rgba(0,0,0,0.1);max-width:500px;margin:auto;
    }
    h2 {margin-top:0;}
    label {display:block;margin-top:15px;font-weight:bold;}
    input, select {
      width:100%;padding:12px;margin-top:6px;border:1px solid #ccc;border-radius:8px;
    }
    .actions {margin-top:20px;text-align:right;}
    .btn {
      padding:12px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:bold;color:#fff;
    }
    .btn-save {background:#6a1b9a;}
    .btn-cancel {background:#9e9e9e;}
    .error {color:#e53935;margin-bottom:10px;}
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <a href="/pkwh_admin/dashboard.php"><img src="/pkwh_admin/assets/icons/dashboard.png" width="28"></a>
    <a href="/pkwh_admin/manage_jadwal.php"><img src="/pkwh_admin/assets/icons/jadwal.png" width="28"></a>
    <a href="/pkwh_admin/manage_guru.php" class="active"><img src="/pkwh_admin/assets/icons/users.png" width="28"></a>
  </div>

  <div class="main">
    <div class="card">
      <h2>Tambah Guru</h2>
      <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
      <form method="post">
        <label>Nama Guru</label>
        <input type="text" name="nama" required>

        <label>NIK</label>
        <input type="text" name="nik" required>

        <label>Role</label>
        <select name="role" required>
          <option value="Guru">Guru</option>
          <option value="Staff">Staff</option>
          <option value="Admin">Admin</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" required>

        <div class="actions">
          <a href="manage_guru.php" class="btn btn-cancel">Batal</a>
          <button type="submit" class="btn btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
