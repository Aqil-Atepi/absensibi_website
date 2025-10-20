<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /pkwh_admin/auth/login.php');
    exit;
}
require_once 'db.php';

// Hapus guru jika ada request delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM guru WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: manage_guru.php");
    exit;
}

// Ambil semua guru
$stmt = $pdo->query("SELECT id, nama, nik, role FROM guru ORDER BY nama ASC");
$gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manage Guru</title>
  <style>
    body {margin:0;font-family:Arial,sans-serif;background:#f3f3f3;}
    .container {display:flex;min-height:100vh;}

    /* Sidebar */
    .sidebar {
      width: 80px;
      background: #fff;
      color: #6a1b9a;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      position: fixed;
      left: 0; top: 0; bottom: 0;
    }
    .sidebar a {
      display:block;
      padding:15px;
      margin:15px 0;
      border-radius:12px;
    }
    .sidebar a.active {background:#6a1b9a;}
    .sidebar a.active img {filter: brightness(0) invert(1);}

    .main {flex:1;padding:20px;margin-left:80px;}

    h2 {margin-top:0;display:flex;justify-content:space-between;align-items:center;}
    .btn-add {
      background:#6a1b9a;
      color:#fff;
      padding:10px 18px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-weight:bold;
      text-decoration:none;
    }
    .btn-add:hover {background:#4a148c;}

    table {
      width:100%;
      border-collapse:collapse;
      background:#fff;
      box-shadow:0 2px 6px rgba(0,0,0,0.1);
      border-radius:8px;
      overflow:hidden;
    }
    thead {background:#6a1b9a;color:#fff;}
    thead th {padding:12px;text-align:left;}
    tbody td {padding:12px;border-bottom:1px solid #eee;}
    tbody tr:last-child td {border-bottom:none;}
    .btn {
      padding:6px 14px;
      border:none;border-radius:6px;
      font-size:13px;
      cursor:pointer;
      color:#fff;
      margin-right:5px;
      text-decoration:none;
      display:inline-block;
    }
    .btn-edit {background:#43a047;}
    .btn-delete {background:#e53935;}
  </style>
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <a href="/pkwh_admin/dashboard.php"><img src="/pkwh_admin/assets/icons/dashboard.png" width="28"></a>
    <a href="/pkwh_admin/manage_jadwal.php"><img src="/pkwh_admin/assets/icons/jadwal.png" width="28"></a>
    <a href="/pkwh_admin/manage_guru.php" class="active"><img src="/pkwh_admin/assets/icons/users.png" width="28"></a>
  </div>

  <!-- Main -->
  <div class="main">
    <h2>
      Daftar Guru
      <a href="add_guru.php" class="btn-add">+ Tambah Guru</a>
    </h2>

    <table>
      <thead>
        <tr>
          <th>Nama Guru</th>
          <th>NIK</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($gurus): ?>
          <?php foreach($gurus as $g): ?>
            <tr>
              <td><?=htmlspecialchars($g['nama'])?></td>
              <td><?=htmlspecialchars($g['nik'])?></td>
              <td><?=htmlspecialchars($g['role'])?></td>
              <td>
                <a href="edit_guru.php?id=<?=$g['id']?>" class="btn btn-edit">Edit</a>
                <a href="manage_guru.php?delete=<?=$g['id']?>" class="btn btn-delete" onclick="return confirm('Yakin hapus guru ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" style="text-align:center;padding:20px;">Belum ada data guru</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
