<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'admin') {
    require_once "../../conn.php";
    date_default_timezone_set('Asia/Jakarta');

    $siswa = $_GET["siswa"];

    $tanggal = new DateTime($_GET["tanggal"]);
    $waktu = new DateTime($_GET["waktu"]);

    $waktuabsen = new DateTime($_GET["tanggal"] . ' ' . $_GET['waktu']);
    $waktuabsen = DateTime::createFromFormat('Y-m-d H:i', $waktuabsen->format('Y-m-d H:i'));

    $waktusekarang = new DateTime("now");
    $waktusekarang = DateTime::createFromFormat('Y-m-d H:i', $waktusekarang->format('Y-m-d H:i'));

    $tanggalStr = $tanggal->format('Y-m-d');
    $waktuStr = $waktu->format('H:i');

    function getSiswa($conn, $nis)
    {
        $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis=?");
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    if ($waktuabsen->getTimestamp() === $waktusekarang->getTimestamp()) {

        $siswaabsen = getSiswa($conn, $siswa);
        $status = "Diproses";

        $stmt = $conn->prepare("INSERT INTO absensi (siswa, kelas, tanggal, waktu, status) 
                                VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sisss",
            $siswaabsen['nis'],
            $siswaabsen['kelas'],
            $tanggalStr,
            $waktuStr,
            $status
        );

        $stmt->execute();
        $stmt->close();

        header("Location: scan.php");
        exit();
    } else {
        header('Location: scan.php?error=Data Absensi Salah! Tolong Ulangi');
        exit();
    }

} else {
    header("Location: ../../");
    exit();
}
?>