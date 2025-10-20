<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ./auth/login.php');
    exit;
}
require_once 'db.php';

$today = date('Y-m-d');

// Statistik
$totStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totGuru     = $pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();

$telatStudents = $pdo->query("SELECT COUNT(*) FROM attendance WHERE student_id IS NOT NULL AND tanggal='$today' AND status='Telat'")->fetchColumn();
$telatGuru     = $pdo->query("SELECT COUNT(*) FROM attendance WHERE guru_id IS NOT NULL AND tanggal='$today' AND status='Telat'")->fetchColumn();

// Ambil absensi siswa hari ini
$stmt = $pdo->prepare("
  SELECT a.jam_masuk, a.status, s.nama, s.nis, s.kelas
  FROM attendance a
  LEFT JOIN students s ON s.id = a.student_id
  WHERE a.tanggal = :t AND a.student_id IS NOT NULL
  ORDER BY a.jam_masuk ASC
");
$stmt->execute(['t' => $today]);
$studentsAttendance = $stmt->fetchAll();

// Ambil absensi guru hari ini
$stmt = $pdo->prepare("
  SELECT a.jam_masuk, a.status, g.nama 
  FROM attendance a
  LEFT JOIN guru g ON g.id = a.guru_id
  WHERE a.tanggal = :t AND a.guru_id IS NOT NULL
  ORDER BY a.jam_masuk ASC
");
$stmt->execute(['t' => $today]);
$guruAttendance = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <style>
    body {margin:0;font-family:Arial, sans-serif;background:#f3f3f3;}
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
      display:block;padding:15px;margin:15px 0;border-radius:12px;
    }
    .sidebar a.active {background:#6a1b9a;}
    .sidebar a.active img {filter: brightness(0) invert(1);}

    .main {flex:1;padding:20px;margin-left:80px;}

    h1 {margin:0 0 20px;}

    .grid {display:grid;grid-template-columns:1fr 2fr;gap:20px;}

    /* Cards kotak statistik */
    .stats {display:grid;grid-template-columns:repeat(2,1fr);gap:15px;}
    .stat {
      background:white;
      border-radius:12px;
      padding:20px;
      box-shadow:0 4px 10px rgba(0,0,0,0.1);
      text-align:center;
    }
    .stat h2 {margin:0;font-size:24px;}
    .stat p {margin:5px 0 0;color:#555;}

    /* Card tabel */
    .card {
      background:white;
      border-radius:12px;
      margin-bottom:20px;
      box-shadow:0 3px 6px rgba(0,0,0,0.1);
      overflow:hidden;
    }
    .card-header {
      background:#6a1b9a;
      color:white;
      font-weight:bold;
      padding:10px;
    }
    table {width:100%;border-collapse:collapse;}
    th, td {
      padding:10px;
      border-bottom:1px solid #eee;
      text-align:left;
      font-size:14px;
    }
    th {background:#fafafa;font-weight:bold;}
    td small {color:#666;font-size:12px;}
    .badge {
      padding:4px 10px;
      border-radius:8px;
      color:white;
      font-weight:bold;
      font-size:12px;
    }
    .Hadir { background:green; }
    .Telat { background:orange; }
    .Alpha { background:red; }
    .Izin { background:gray; }

    /* Camera toggle (frontend only) */
    .camera-toggle {
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 16px;
      background:#6a1b9a;
      color:#fff;
      border:none;
      border-radius:8px;
      cursor:pointer;
      font-weight:bold;
      transition:0.2s;
      float:right;
      margin-top:-60px;
    }
    .camera-toggle.off {
      background:#aaa;
    }
    .camera-icon {
  filter: brightness(0) invert(1);
}
  </style>
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <a href="./dashboard.php" class="<?=basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''?>">
      <img src="./assets/icons/dashboard.png" alt="Dashboard" width="28">
    </a>
    <a href="./manage_jadwal.php" class="<?=basename($_SERVER['PHP_SELF'])=='manage_jadwal.php'?'active':''?>">
      <img src="./assets/icons/jadwal.png" alt="Jadwal" width="28">
    </a>
    <a href="./manage_guru.php" class="<?=basename($_SERVER['PHP_SELF'])=='manage_guru.php'?'active':''?>">
      <img src="./assets/icons/users.png" alt="Guru" width="28">
    </a>
  </div>

  <!-- Main -->
  <div class="main">
    <h1>Halo, <?=htmlspecialchars($_SESSION['user']['nama'])?>! <br> Home / Dashboard</h1>

    <!-- FRONTEND-ONLY Camera Toggle Button -->
    <button class="camera-toggle off" id="cameraSwitch">
      <img src="./assets/icons/camera.png" alt="Camera" width="28" class="camera-icon">
      <span>Camera: Off</span>
    </button>

    <div class="grid">
      <!-- Kotak statistik kiri -->
      <div class="stats">
        <div class="stat">
          <h2><?=$totStudents?> <small>Siswa</small></h2>
          <p>Total Siswa</p>
        </div>
        <div class="stat">
          <h2><?=$totGuru?> <small>Guru</small></h2>
          <p>Total Guru</p>
        </div>
        <div class="stat">
          <h2><?=$telatStudents?> <small>Siswa</small></h2>
          <p>Telat Waktu</p>
        </div>
        <div class="stat">
          <h2><?=$telatGuru?> <small>Guru</small></h2>
          <p>Telat Waktu</p>
        </div>
      </div>

      <!-- Tabel kanan -->
      <div>
        <div class="card">
          <div class="card-header">Nama Siswa</div>
          <table>
            <tr><th>Nama</th><th>Jam Masuk</th><th>Status Hadiran</th></tr>
            <?php foreach($studentsAttendance as $s): ?>
            <tr>
              <td><?=htmlspecialchars($s['nama'])?></td>
              <td><?=$s['jam_masuk']?></td>
              <td><span class="badge <?=$s['status']?>"><?=$s['status']?></span></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>

        <div class="card">
          <div class="card-header">Nama Guru</div>
          <table>
            <tr><th>Nama</th><th>Jam Masuk</th><th>Status Hadiran</th></tr>
            <?php foreach($guruAttendance as $g): ?>
            <tr>
              <td><?=htmlspecialchars($g['nama'])?></td>
              <td><?=$g['jam_masuk']?></td>
              <td><span class="badge <?=$g['status']?>"><?=$g['status']?></span></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- simple UI-only toggle -->
<script>
  const camBtn = document.getElementById('cameraSwitch');
  camBtn.addEventListener('click', () => {
    camBtn.classList.toggle('off');
    const status = camBtn.classList.contains('off') ? 'Off' : 'On';
    camBtn.querySelector('span').textContent = `Camera: ${status}`;
  });
</script>
</body>
</html>
