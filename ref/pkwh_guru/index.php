<?php 
include 'header.php';

// ==== Query Ringkasan ====
$qTotal = mysqli_query($conn,"SELECT COUNT(*) as jml FROM students");
$total  = mysqli_fetch_assoc($qTotal)['jml'];

$qHadir = mysqli_query($conn,"
 SELECT COUNT(*) as jml FROM attendance
 WHERE tanggal = CURDATE() AND status='Hadir'");
$hadir  = mysqli_fetch_assoc($qHadir)['jml'];

$qTelat = mysqli_query($conn,"
 SELECT COUNT(*) as jml FROM attendance
 WHERE tanggal = CURDATE() AND status='Telat'");
$telat  = mysqli_fetch_assoc($qTelat)['jml'];

$qAlpha = mysqli_query($conn,"
 SELECT COUNT(*) as jml FROM attendance
 WHERE tanggal = CURDATE() AND status='Alpha'");
$alpha  = mysqli_fetch_assoc($qAlpha)['jml'];

// ==== FILTER GRAFIK ====
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'week';
switch ($filter) {
    case 'month':
        // data harian bulan ini
        $sqlChart = "
          SELECT DATE(tanggal) as tgl,
                 SUM(status='Hadir') as hadir
          FROM attendance
          WHERE YEAR(tanggal)=YEAR(CURDATE())
            AND MONTH(tanggal)=MONTH(CURDATE())
          GROUP BY DATE(tanggal)
          ORDER BY tgl";
        break;
    case 'year':
        // data bulanan tahun ini
        $sqlChart = "
          SELECT DATE_FORMAT(tanggal,'%Y-%m') as tgl,
                 SUM(status='Hadir') as hadir
          FROM attendance
          WHERE YEAR(tanggal)=YEAR(CURDATE())
          GROUP BY DATE_FORMAT(tanggal,'%Y-%m')
          ORDER BY tgl";
        break;
    default:
        // default minggu ini (7 hari)
        $sqlChart = "
          SELECT DATE(tanggal) as tgl,
                 SUM(status='Hadir') as hadir
          FROM attendance
          WHERE YEARWEEK(tanggal,1) = YEARWEEK(CURDATE(),1)
          GROUP BY DATE(tanggal)
          ORDER BY tgl";
}

// ==== Data untuk Grafik ====
$qChart = mysqli_query($conn,$sqlChart);
$labels=[]; $data=[];
while($row=mysqli_fetch_assoc($qChart)){
    if($filter=='year'){
        $labels[] = date('M Y',strtotime($row['tgl'].'-01'));
    } else {
        $labels[] = date('d M',strtotime($row['tgl']));
    }
    $data[]   = $row['hadir'];
}

// ==== Data List Nama Hari Ini ====
$qList = mysqli_query($conn,"
 SELECT s.nama,a.jam_masuk,a.status
 FROM attendance a
 JOIN students s ON s.id=a.student_id
 WHERE a.tanggal = CURDATE()
 ORDER BY a.jam_masuk ASC
");
?>
<h3>Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card p-3 text-center">
    <h6>Total Siswa</h6><h2><?= $total ?></h2>
  </div></div>
  <div class="col-md-3"><div class="card p-3 text-center">
    <h6>Tepat Waktu</h6><h2><?= $hadir ?></h2>
  </div></div>
  <div class="col-md-3"><div class="card p-3 text-center">
    <h6>Telat</h6><h2><?= $telat ?></h2>
  </div></div>
  <div class="col-md-3"><div class="card p-3 text-center">
    <h6>Alpha</h6><h2><?= $alpha ?></h2>
  </div></div>
</div>

<!-- ====== Filter & Grafik Kehadiran ====== -->
<div class="card p-3 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Grafik Kehadiran</h5>
    <form method="get">
      <select name="filter" onchange="this.form.submit()" class="form-select">
        <option value="week"  <?= $filter=='week'?'selected':'' ?>>Per Minggu</option>
        <option value="month" <?= $filter=='month'?'selected':'' ?>>Per Bulan</option>
        <option value="year"  <?= $filter=='year'?'selected':'' ?>>Per Tahun</option>
      </select>
    </form>
  </div>
  <canvas id="chartHadir" height="90"></canvas>
</div>

<!-- ====== List Nama Siswa Hari Ini ====== -->
<div class="card p-3">
  <h5 class="mb-3">Daftar Kehadiran Hari Ini</h5>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nama Siswa</th>
        <th>Jam Masuk</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r=mysqli_fetch_assoc($qList)):
        $color = match($r['status']){
          'Hadir'=>'success',
          'Alpha'=>'danger',
          'Izin'=>'secondary',
          'Telat'=>'warning',
          default=>'secondary'
        };
      ?>
      <tr>
        <td><?= $r['nama']; ?></td>
        <td><?= $r['jam_masuk']; ?></td>
        <td><span class="badge bg-<?= $color; ?>"><?= $r['status']; ?></span></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Script Chart.js -->
<script>
const ctx = document.getElementById('chartHadir');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Jumlah Hadir',
      data: <?= json_encode($data) ?>,
      borderColor: '#6f42c1',
      backgroundColor: 'rgba(111,66,193,0.2)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});
</script>
<?php include 'footer.php'; ?>
