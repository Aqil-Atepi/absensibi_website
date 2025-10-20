<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /pkwh_admin/auth/login.php');
    exit;
}
require_once 'db.php';

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$monthName = date('F', mktime(0,0,0,$month,1,$year));

$stmt = $pdo->prepare("SELECT tanggal, status FROM attendance WHERE student_id IS NULL AND MONTH(tanggal)=:m AND YEAR(tanggal)=:y");
$stmt->execute(['m'=>$month,'y'=>$year]);
$jadwal = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date('w', strtotime("$year-$month-01"));
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Kelola Jadwal</title>
  <style>
    body {margin:0;font-family:Arial,sans-serif;background:#f3f3f3;}
    .container {display:flex;min-height:100vh;}
    .sidebar {width:80px;background:#fff;box-shadow:2px 0 10px rgba(0,0,0,0.1);position:fixed;top:0;bottom:0;left:0;display:flex;flex-direction:column;align-items:center;padding:20px 0;}
    .sidebar a {display:block;padding:15px;margin:15px 0;border-radius:12px;}
    .sidebar a.active {background:#6a1b9a;}
    .sidebar a.active img {filter:brightness(0) invert(1);}
    .main {flex:1;padding:20px;margin-left:80px;}
    .calendar {max-width:700px;margin:auto;}
    .calendar-header {text-align:center;font-size:20px;margin-bottom:10px;}
    .calendar-grid {display:grid;grid-template-columns:repeat(7,1fr);gap:5px;}
    .day-name {background:#eee;padding:10px;text-align:center;font-weight:bold;}
    .day {background:#ddd;padding:15px;text-align:center;border-radius:6px;cursor:pointer;}
    .day.masuk {background:#c8e6c9;}
    .day.libur {background:#ffcdd2;}
    .day.event {background:#ffe082;}
    /* Modal */
    .modal {display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;}
    .modal-content {background:#fff;padding:20px;border-radius:10px;width:600px;max-width:90%;}
    .form-row {display:flex;gap:20px;margin-bottom:15px;}
    .form-row div {flex:1;}
    input {width:100%;padding:10px;border-radius:6px;border:1px solid #ccc;}
    button {padding:10px 20px;border:none;border-radius:6px;cursor:pointer;}
    .submit-btn {background:#c62828;color:#fff;float:right;}
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <a href="/pkwh_admin/dashboard.php"><img src="/pkwh_admin/assets/icons/dashboard.png" width="28"></a>
    <a href="/pkwh_admin/manage_jadwal.php" class="active"><img src="/pkwh_admin/assets/icons/jadwal.png" width="28"></a>
    <a href="/pkwh_admin/manage_guru.php"><img src="/pkwh_admin/assets/icons/users.png" width="28"></a>
  </div>

  <div class="main">
    <div class="calendar">
      <div class="calendar-header"><?=$monthName?> <?=$year?></div>
      <div class="calendar-grid">
        <div class="day-name">Sun</div>
        <div class="day-name">Mon</div>
        <div class="day-name">Tue</div>
        <div class="day-name">Wed</div>
        <div class="day-name">Thu</div>
        <div class="day-name">Fri</div>
        <div class="day-name">Sat</div>
        <?php for ($i=0;$i<$firstDay;$i++): ?><div></div><?php endfor; ?>
        <?php for ($d=1;$d<=$daysInMonth;$d++): 
          $tgl = sprintf("%04d-%02d-%02d",$year,$month,$d);
          $status = strtolower($jadwal[$tgl] ?? '');
          ?>
          <div class="day <?=$status?>" data-date="<?=$tgl?>"><?=$d?></div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="jadwalModal">
  <div class="modal-content">
    <h3 id="modalDate"></h3>
    <form method="post" action="save_jadwal.php">
      <input type="hidden" name="tanggal" id="formTanggal">
      <div class="form-row">
        <div>
          <label>Jam Masuk Siswa</label>
          <input type="time" name="jam_masuk_siswa">
        </div>
        <div>
          <label>Jam Pulang Siswa</label>
          <input type="time" name="jam_pulang_siswa">
        </div>
      </div>
      <div class="form-row">
        <div>
          <label>Jam Masuk Guru / Staff</label>
          <input type="time" name="jam_masuk_guru">
        </div>
        <div>
          <label>Jam Pulang Guru / Staff</label>
          <input type="time" name="jam_pulang_guru">
        </div>
      </div>
      <div class="form-row">
        <div>
          <label>Jam Mulai Absensi</label>
          <input type="time" name="jam_mulai_absen">
        </div>
        <div>
          <label>Status Hari</label>
          <select name="status">
            <option value="Masuk">Masuk</option>
            <option value="Libur">Libur</option>
            <option value="Event">Event</option>
          </select>
        </div>
      </div>
      <button type="submit" class="submit-btn">Submit</button>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.day').forEach(day=>{
  day.addEventListener('click', ()=>{
    const date = day.getAttribute('data-date');
    document.getElementById('modalDate').innerText = date;
    document.getElementById('formTanggal').value = date;
    document.getElementById('jadwalModal').style.display='flex';
  });
});
document.getElementById('jadwalModal').addEventListener('click', e=>{
  if(e.target.id==='jadwalModal'){ e.currentTarget.style.display='none'; }
});
</script>
</body>
</html>
