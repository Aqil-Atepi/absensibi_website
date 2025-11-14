<?php
session_start();
require_once "../../conn.php";

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['idkelas'] && $_GET['siswa']) {
    $idkelas = $_GET["idkelas"];
    $siswa = $_GET["siswa"];

    $stmt = $conn->prepare("UPDATE siswa SET kelas=NULL WHERE nama=?");
    $stmt->bind_param("s", $siswa);
    $stmt->execute();
    $stmt->close();

    header("Location: kelas-edit.php?idkelas=$idkelas");
    exit;
} else {
    header("Location: ../../");
    exit;
}
?>