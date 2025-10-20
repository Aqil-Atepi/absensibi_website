<?php
include 'header.php';

// === Ambil ID siswa dari URL ?id= ===
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==== Data Siswa ====
$qSiswa = mysqli_query($conn,"SELECT * FROM students WHERE id=$id");
$siswa  = mysqli_fetch_assoc($qSiswa);

// ==== Statistik Kehadiran ====
$qStat = mysqli_query($conn,"
 SELECT 
  SUM(status='Hadir')  as hadir,
  SUM(status='Telat')  as telat,
  SUM(status='Izin')   as izin,
  SUM(status='Alpha')  as alpha
 FROM attendance
 WHERE student_id=$id
");
$stat = mysqli_fetch_assoc($qStat);

// ==== Riwayat Kehadiran ====
$qRiwayat = mysqli_query($conn,"
 SELECT *
 FROM attendance
 WHERE student_id=$id
 ORDER BY tanggal DESC
");
?>
<h3>Profil Siswa</h3>
<div class="row">
  <!-- SIDEBAR PROFIL -->
  <div class="col-md-3">
    <div class="card p-3 text-center">
      <img src="uploads/<?= $siswa['foto'] ?>" class="img-fluid rounded mb-3" alt="foto">
      <h5><?= $siswa['nama']; ?></h5>
      <small>NIS : <?= $siswa['nis']; ?></small>
      <hr>
      <div class="text-start">
        <p>✅ Hadir : <?= $stat['hadir']; ?> Hari</p>
        <p>⚠️ Telat : <?= $stat['telat']; ?> Hari</p>
        <p>📩 Izin  : <?= $stat['izin']; ?> Hari</p>
        <p>❌ Alpha : <?= $stat['alpha']; ?> Hari</p>
      </div>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="col-md-9">
    <div class="card p-3">
      <div class="d-flex justify-content-between mb-3">
        <strong>Time Period: 1st Aug – 31st Aug 2025</strong>
        <span class="text-muted">Catatan kehadiran bulan ini</span>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Status Kehadiran</th>
            <th>Foto Tempat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($r=mysqli_fetch_assoc($qRiwayat)):
            $badge = match($r['status']){
              'Hadir'=>'success',
              'Telat'=>'warning',
              'Izin'=>'secondary',
              'Alpha'=>'danger',
              default=>'secondary'
            };
          ?>
          <tr>
            <td><?= date('d M Y',strtotime($r['tanggal'])); ?></td>
            <td><?= $r['jam_masuk']; ?></td>
            <td><span class="badge bg-<?= $badge; ?>"><?= $r['status']; ?></span></td>
            <td>
              <?php if($r['foto_tempat']): ?>
                <img src="uploads/<?= $r['foto_tempat']; ?>" width="40">
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td>
              <a href="edit_attendance.php?id=<?= $r['id']; ?>" class="btn btn-sm btn-outline-primary">✏️</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
