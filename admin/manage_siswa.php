<?php
// manage_siswa.php
session_start();
if (!isset($_SESSION['user'])) header('Location: /auth/login.php');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $password = password_hash($_POST['password'] ?: '123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO siswa (nis, nama, password) VALUES (:nis, :nama, :pw)");
    $stmt->execute(['nis'=>$nis,'nama'=>$nama,'pw'=>$password]);
    header('Location: /manage_siswa.php'); exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM siswa WHERE id = :id")->execute(['id'=> (int)$_GET['delete']]);
    header('Location: /manage_siswa.php'); exit;
}

$siswa = $pdo->query("SELECT * FROM siswa ORDER BY id DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Kelola Siswa</title></head><body>
  <h2>Kelola Siswa</h2>
  <a href="/dashboard.php">← Dashboard</a>

  <h3>Tambah Siswa</h3>
  <form method="post">
    <input type="hidden" name="action" value="add">
    <label>NIS</label><input name="nis" required><br>
    <label>Nama</label><input name="nama" required><br>
    <label>Password</label><input name="password"><br>
    <button>Tambah</button>
  </form>

  <h3>Daftar</h3>
  <table border="1" cellpadding="6">
    <tr><th>ID</th><th>NIS</th><th>Nama</th><th>Aksi</th></tr>
    <?php foreach($siswa as $s): ?>
      <tr>
        <td><?=$s['id']?></td>
        <td><?=htmlspecialchars($s['nis'])?></td>
        <td><?=htmlspecialchars($s['nama'])?></td>
        <td><a href="?delete=<?=$s['id']?>" onclick="return confirm('Hapus?')">Hapus</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body></html>
