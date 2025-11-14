<?php
session_start();
require_once "../../conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $idkelas = $_POST["idkelas"];
    $namakelas = $_POST["nama"];
    $walikelas = $_POST["walikelas"];

    $stmt = $conn->prepare("SELECT nama FROM kelas WHERE nama=?");
    $stmt->bind_param("s", $namakelas);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("UPDATE kelas SET nama=? WHERE id=?");
        $stmt->bind_param("si", $namakelas, $idkelas);
        $stmt->execute();
    }

    if (!empty($walikelas)) {
        $stmt = $conn->prepare("SELECT nama FROM guru WHERE nama=?");
        $stmt->bind_param("s", $walikelas);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE guru SET walikelas=NULL WHERE walikelas=?");
            $stmt->bind_param("i", $idkelas);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE guru SET walikelas=? WHERE nama=?");
            $stmt->bind_param("is", $idkelas, $walikelas);
            $stmt->execute();
            $stmt->close();

            header("Location: kelas-edit.php?idkelas=$idkelas");
        } else {
            header("Location: kelas-edit.php?idkelas=$idkelas");
        }
    } else {
        $stmt = $conn->prepare("UPDATE guru SET walikelas=NULL WHERE walikelas=?");
        $stmt->bind_param("i", $idkelas);
        $stmt->execute();
        $stmt->close();

        header("Location: kelas-edit.php?idkelas=$idkelas");
    }
    exit;
} else {
    header("Location: ../../");
    exit;
}