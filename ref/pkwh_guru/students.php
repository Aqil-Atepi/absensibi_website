<?php include 'header.php'; ?>
<h3>Absen Kelas</h3>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Kelas</th>
      <th>Jumlah Masuk</th>
      <th>Walas</th>
    </tr>
  </thead>
  <tbody>
<?php
$q=mysqli_query($conn,"SELECT kelas, COUNT(a.id) as hadir,
       COUNT(s.id) as total
 FROM students s
 LEFT JOIN attendance a
 ON s.id=a.student_id AND a.tanggal=CURDATE() AND a.status='Hadir'
 GROUP BY kelas");
while($r=mysqli_fetch_assoc($q)){
   echo "<tr>
   <td>{$r['kelas']}</td>
   <td>{$r['hadir']} / {$r['total']} Siswa</td>
   <td>Nama Walas</td>
   </tr>";
}
?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
