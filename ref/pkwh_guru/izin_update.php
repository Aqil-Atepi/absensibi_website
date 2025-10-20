<?php
include 'config.php'; // koneksi $conn

if (isset($_GET['id'], $_GET['status'])) {
    $id     = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    // validasi status
    $allowed = ['Diterima','Ditolak'];
    if(in_array($status,$allowed)){
        mysqli_query($conn,"UPDATE izin SET status='$status' WHERE id=$id");
    }
}
header('Location: list_izin.php');
exit;
