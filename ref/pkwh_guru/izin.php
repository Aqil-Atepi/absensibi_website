<?php include 'header.php'; ?>
<h3>List Izin</h3>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Siswa</th>
      <th>Isi Konten</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
<?php
$q=mysqli_query($conn,"
 SELECT i.id,s.nama,i.konten,i.status
 FROM izin i JOIN students s ON s.id=i.student_id
 ORDER BY i.created_at DESC");
$no=1;
while($row=mysqli_fetch_assoc($q)){
    $color = $row['status']=='Diterima'?'success':
             ($row['status']=='Pending'?'warning':'danger');

    echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama']}</td>
            <td>{$row['konten']}</td>
            <td><span class='badge bg-$color'>{$row['status']}</span></td>
            <td>";

    // tombol aksi hanya muncul jika status Pending
    if($row['status']=='Pending'){
        echo "
        <a href='izin_update.php?id={$row['id']}&status=Diterima' 
           class='btn btn-sm btn-success'
           onclick=\"return confirm('Terima izin ini?')\">Terima</a>
        <a href='izin_update.php?id={$row['id']}&status=Ditolak'
           class='btn btn-sm btn-danger'
           onclick=\"return confirm('Tolak izin ini?')\">Tolak</a>
        ";
    } else {
        echo "-"; // sudah final, tidak ada aksi
    }

    echo "</td>
        </tr>";
    $no++;
}
?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
