<?php
session_start();
require_once "../../conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $idkelas = $_POST["idkelas"];
    $siswa = $_POST["siswa"];

    if (!empty($siswa)) {
        $stmt = $conn->prepare("SELECT nama FROM siswa WHERE nama=?");
        $stmt->bind_param("s", $siswa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE siswa SET kelas=NULL WHERE nama=?");
            $stmt->bind_param("s", $siswa);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE siswa SET kelas=? WHERE nama=?");
            $stmt->bind_param("is", $idkelas, $siswa);
            $stmt->execute();
            $stmt->close();

            header("Location: kelas-edit.php?idkelas=$idkelas");
        } else {
            header("Location: kelas-edit.php?idkelas=$idkelas");
        }
    } else {
        header("Location: kelas-edit.php?idkelas=$idkelas");
    }
    exit;
} else {
    header("Location: ../../");
    exit;
}
?>