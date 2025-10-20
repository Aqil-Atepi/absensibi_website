<?php
include 'header.php';
include 'config.php';

// Ambil data guru yang login (misal disimpan di $_SESSION['user_id'])
session_start();
$id = $_SESSION['guru_id'] ?? 0;

$q = mysqli_query($conn,"SELECT * FROM guru WHERE id=$id");
$guru = mysqli_fetch_assoc($q);
?>

<div class="container mt-4">
  <h3>Profile</h3>

  <form action="profile_update.php" method="post" enctype="multipart/form-data"
        class="p-4 rounded" style="background:#6a1b9a;color:#fff;">
    <input type="hidden" name="id" value="<?= $guru['id']; ?>">

    <!-- FOTO -->
    <div class="mb-3 text-center">
      <img src="uploads/<?= $guru['foto'] ?: 'default.png'; ?>" width="120" class="rounded-circle mb-2">
      <input type="file" name="foto" class="form-control bg-light text-dark">
    </div>

    <!-- ROLE -->
    <div class="mb-3">
      <label class="form-label">Role</label>
      <input type="text" name="role" class="form-control"
             value="<?= htmlspecialchars($guru['role']); ?>">
    </div>

    <!-- NIK -->
    <div class="mb-3">
      <label class="form-label">NIK</label>
      <input type="text" name="nik" class="form-control"
             value="<?= htmlspecialchars($guru['nik']); ?>">
    </div>

    <!-- PASSWORD -->
    <div class="mb-3">
      <label class="form-label">Password (kosongkan jika tidak ganti)</label>
      <input type="password" name="password" class="form-control">
    </div>

    <button type="submit" class="btn btn-light">Simpan Perubahan</button>
  </form>
</div>

<?php include 'footer.php'; ?>
