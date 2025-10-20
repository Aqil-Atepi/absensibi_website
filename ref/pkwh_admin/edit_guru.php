<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /pkwh_admin/auth/login.php');
    exit;
}
require_once 'db.php';

// ambil guru berdasarkan id
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM guru WHERE id = :id");
$stmt->execute(['id' => $id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    die("Guru tidak ditemukan!");
}

// update guru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE guru SET nama=:n, nik=:nik, role=:r WHERE id=:id");
        $stmt->execute([
            'n' => $_POST['nama'],
            'nik' => $_POST['nik'],
            'r' => $_POST['role'],
            'id' => $id
        ]);
        header("Location: manage_guru.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM guru WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        header("Location: manage_guru.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Guru</title>
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
    .actions {margin-top:20px;display:flex;justify-content:space-between;}
    .btn {
      padding:12px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:bold;color:#fff;
    }
    .btn-update {background:#43a047;}
    .btn-delete {background:#e53935;}
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
      <h2>Edit Guru</h2>
      <form method="post">
        <label>Nama Guru</label>
        <input type="text" name="nama" value="<?=$guru['nama']?>" required>

        <label>NIK</label>
        <input type="text" name="nik" value="<?=$guru['nik']?>" required>

        <label>Role</label>
        <select name="role">
          <option value="Guru" <?=$guru['role']=='Guru'?'selected':''?>>Guru</option>
          <option value="Staff" <?=$guru['role']=='Staff'?'selected':''?>>Staff</option>
          <option value="WaliKelas" <?=$guru['role']=='Staff'?'selected':''?>>WaliKelas</option>
        </select>

        <div class="actions">
          <button type="submit" name="update" class="btn btn-update">Update</button>
          <button type="submit" name="delete" class="btn btn-delete" onclick="return confirm('Yakin hapus guru ini?')">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
