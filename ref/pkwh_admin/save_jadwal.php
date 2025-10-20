<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /pkwh_admin/auth/login.php");
    exit;
}
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $jam_masuk = $_POST['jam_masuk_siswa'] ?? null;
    $status = $_POST['status'] ?? 'Masuk';

    // cek apakah tanggal sudah ada
    $stmt = $pdo->prepare("SELECT id FROM attendance WHERE tanggal = :tgl AND student_id IS NULL");
    $stmt->execute(['tgl'=>$tanggal]);
    $row = $stmt->fetch();

    if ($row) {
        // update
        $stmt = $pdo->prepare("UPDATE attendance 
                               SET jam_masuk = :jm, status = :st 
                               WHERE id = :id");
        $stmt->execute([
            'jm'=>$jam_masuk,
            'st'=>$status,
            'id'=>$row['id']
        ]);
    } else {
        // insert
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, tanggal, jam_masuk, status) 
                               VALUES (NULL, :tgl, :jm, :st)");
        $stmt->execute([
            'tgl'=>$tanggal,
            'jm'=>$jam_masuk,
            'st'=>$status
        ]);
    }

    header("Location: manage_jadwal.php");
    exit;
}
?>
