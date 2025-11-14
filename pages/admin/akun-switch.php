<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['table'] && $_GET['nomorinduk'] && $_GET['status']) {
    require_once "../../conn.php";

    $inputTable = $_GET['table'];
    $nomorinduk = $_GET['nomorinduk'];
    $currentstatus = $_GET['status'];

    $availableTable = ['siswa', 'guru'];

    if (!in_array($inputTable, $availableTable))
        $table = 'No Table Found!';
    else
        $table = $inputTable;

    if ($currentstatus == 'Non-Aktif')
        $status = 'Aktif';
    else if ($currentstatus == 'Aktif')
        $status = 'Non-Aktif';


    if ($table == 'siswa') {
        $stmt = $conn->prepare('UPDATE siswa SET status=? WHERE nis=?');
        $stmt->bind_param('ss', $status, $nomorinduk);
        $stmt->execute();
        $stmt->close();
        header('Location: akun.php');
    } elseif ($table == 'guru') {
        $stmt = $conn->prepare('UPDATE guru SET status=? WHERE nik=?');
        $stmt->bind_param('ss', $status, $nomorinduk);
        $stmt->execute();
        $stmt->close();
        header('Location: akun.php?table=guru');
    }

} else {
    header("Location: ../../");
}
?>