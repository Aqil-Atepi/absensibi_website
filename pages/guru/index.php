<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru') {
    require_once "../../conn.php";

    $nik = $_SESSION["id"];

    function getUser($conn, $nik)
    {
        $stmt = $conn->prepare("SELECT * FROM guru WHERE nik=?");
        $stmt->bind_param("s", $nik);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    $guru = getUser($conn, $nik);

    if ($guru) {
        if ($guru["status"] == 'Aktif') {
        }
    }

    function getWaliKelas($conn, $idkelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("s", $idkelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['nama'];
    }
    $kelas = getWaliKelas($conn, $guru['walikelas']);

    function getEvent($conn, $tanggal)
    {
        $stmt = $conn->prepare("SELECT * FROM event WHERE tanggalmulai <= ? AND tanggalselesai >= ?");
        $stmt->bind_param("ss", $tanggal, $tanggal);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data : '-';
    }

    function getJadwal($conn, $hari)
    {
        $stmt = $conn->prepare("SELECT * FROM jadwal WHERE hari=?");
        $stmt->bind_param("s", $hari);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    date_default_timezone_set('Asia/Jakarta');

    $tanggal = date('Y-m-d');
    $day = date('l');

    $idnhari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $waktusekarang = date('H:i:s');
    $event = getEvent($conn, $tanggal);
    $hari = getJadwal($conn, $idnhari[$day]);

    function getAbsensi($conn, $tanggal, $limit, $offset)
    {
        $stmt = $conn->prepare("SELECT * FROM absensi WHERE tanggal=? AND status='Diproses' ORDER BY waktu ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $tanggal, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    function getNamaSiswa($conn, $nis)
    {
        $stmt = $conn->prepare("SELECT nama FROM siswa WHERE nis=?");
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['nama'];
    }

    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM absensi WHERE tanggal=? AND status='Diproses'");
    $stmt->bind_param("s", $tanggal);
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $absensi = getAbsensi($conn, $tanggal, $limit, $offset);

    function getAbsenSiswa($conn, $tanggal, $absen, $kelas)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM absensi WHERE  tanggal=? AND absen=? AND kelas=?");
        $stmt->bind_param("ssi", $tanggal, $absen, $kelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    function getDataSiswa($conn, $kelas)
    {
        if ($kelas == "") {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            return $data['total'];
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa WHERE kelas=?");
            $stmt->bind_param("i", $kelas);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            return $data['total'];
        }
    }

    function getDataKelas($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM kelas");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Dashboard</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: 700px;

                display: flex;
                flex-direction: row;
                align-items: start;
                justify-content: center;
            }

            .data {
                width: 650px;
                height: 700px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: baseline;

                gap: 20px;
            }

            /* PROFILE */
            .profile {
                width: 620px;
                height: 200px;

                background: url('../../assets/images/doodle.png') no-repeat center;
                background-size: cover;

                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .profile-container {
                width: 570px;
                height: 150px;

                padding: 10px 50px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;

                gap: 20px;

                color: var(--color2);
            }

            .photo-profile {
                width: 100px;
                height: 100px;

                overflow: hidden;
            }

            .photo-profile img {
                width: 100%;
                height: 100%;

                background-size: cover;
            }

            .profile-info {
                width: 330px;
                height: auto;
            }

            .profile-info h1 {
                font-size: 20px;
                font-weight: bold;
            }

            .profile-info p {
                font-size: 15px;
            }

            /* SUMMARY */
            .summary {
                width: 620px;
                height: 480px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;

                gap: 20px;
            }

            .summary-filter select {
                width: 620px;
                height: 40px;

                border: 2px solid var(--color3);
                border-radius: 10px;

                padding-left: 10px;
                font-size: 15px
            }

            .summary-data {
                width: 620px;
                height: 420px;

                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: repeat(2, 1fr);
                gap: 10px;
            }

            .summary-items {
                background-color: var(--color1);
                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                color: var(--color2);
            }

            .summary-items div {
                width: 250px;
                height: 150px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: center;
            }

            .summary-items h2 {
                font-size: 40px;
            }

            .summary-items p {
                font-size: 15px;
            }

            .summary-items:hover {
                background-color: var(--color3);
                color: var(--color1a);
            }

            /* TIME */

            .time {
                width: 620px;
                height: 200px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                gap: 5px;

                color: var(--color2);
            }

            .time-title h1 {
                font-size: 30px;
                font-weight: bold;
            }

            .time-container {
                width: 400px;
                height: 110px;

                background-color: var(--color3b);

                border-radius: 10px;

                display: flex;
                align-items: center;
                justify-content: center;
            }

            .time-container.masuk {
                background-color: var(--indicator1);
            }

            .time-container.telat {
                background-color: var(--indicator2);
            }

            .time-container h1 {
                width: 320px;

                text-align: center;

                font-size: 80px;
            }

            .time-status p {
                font-size: 15px;
            }

            /* TABLE */
            .table-data {
                width: 620px;
                background-color: var(--color3);
                border: 5px solid var(--color1a);
                border-radius: 10px;
                overflow: hidden;
                display: flex;
                align-items: baseline;
                justify-content: center;
            }

            .table-data table {
                width: 620px;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .table-data table thead {
                background-color: var(--color1);
                border-bottom: 5px solid var(--color1a);
                color: var(--color2);
                font-weight: bold;
            }

            .table-data table tbody tr:hover {
                background-color: var(--color2);
            }

            .table-data table tr {
                height: 40px;
            }

            .table-data table td {
                padding-left: 15px;
                max-width: 600px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table-no {
                width: 50px;
            }

            .table-nma {
                width: 420px;
            }

            .table-wkt {
                width: 150px;
            }

            /* PAGINATION */
            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 20px 0;
                gap: 8px;
            }

            .pagination a {
                background-color: var(--color2);
                color: var(--color1);
                padding: 8px 14px;
                border-radius: 8px;
                text-decoration: none;
                transition: 0.2s;
                font-weight: bold;
            }

            .pagination a:hover {
                background-color: var(--color1b);
                color: var(--color2);
            }

            .pagination a.active {
                background-color: var(--color1b);
                color: var(--color2);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Dashboard Guru</h1>
            </div>

            <div class="content">
                <div class="data">
                    <div class="profile">
                        <div class="profile-container">
                            <div class="photo-profile">
                                <img src="../../assets/images/logo-bi.png" alt="Logo SMK Bina Informatika">
                            </div>

                            <div class="profile-info">
                                <h1> <?= $guru['nama'] ?> </h1>
                                <p><?= (!empty($kelas)) ? 'Walikelas ' . $kelas : 'Guru' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="summary">
                        <div class="summary-filter">
                            <form method="GET">
                                <select name="filtersummary" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    <?php
                                    $selected = $_GET['filtersummary'] ?? '';
                                    $datakelas = getDataKelas($conn);
                                    foreach ($datakelas as $kelas) {
                                        $isSelected = ($kelas['id'] == $selected) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($kelas['id']) . '" ' . $isSelected . '>'
                                            . htmlspecialchars($kelas['nama']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </form>

                        </div>

                        <div class="summary-data">
                            <?php
                            $currentkelas = isset($_GET['filtersummary']) ? $_GET['filtersummary'] : "";

                            $totalsiswa = getDataSiswa($conn, $currentkelas);

                            $totaltepatwaktu = getAbsenSiswa($conn, $tanggal, 'Tepat Waktu', $currentkelas);
                            $totaltelat = getAbsenSiswa($conn, $tanggal, 'Telat', $currentkelas);
                            $totalizin = getAbsenSiswa($conn, $tanggal, 'Sakit', $currentkelas);
                            $totalsakit = getAbsenSiswa($conn, $tanggal, 'Izin', $currentkelas);
                            $totalalpha = getAbsenSiswa($conn, $tanggal, 'Alpha', $currentkelas);

                            $totalmasuk = $totaltepatwaktu + $totaltelat;
                            $totalizin = $totalizin + $totalsakit;
                            ?>
                            <a href="#" class="summary-items">
                                <div>
                                    <h2><?= $totalsiswa ?> Siswa</h2>
                                    <p>Total Siswa</p>
                                </div>
                            </a>

                            <a href="#" class="summary-items">
                                <div>
                                    <h2><?= $totalmasuk ?> Siswa</h2>
                                    <p>Total Siswa Masuk</p>
                                </div>
                            </a>

                            <a href="#" class="summary-items">
                                <div>
                                    <h2><?= $totalalpha ?> Siswa</h2>
                                    <p>Total Siswa Alpha</p>
                                </div>
                            </a>

                            <a href="#" class="summary-items">
                                <div>
                                    <h2><?= $totalizin ?> Siswa</h2>
                                    <p>Total Siswa Izin</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="data">
                    <div class="time">
                        <div class="time-title">
                            <h1>Batas Jam Masuk</h1>
                        </div>

                        <?php

                        if ($event == '-') {
                            $bataswaktu = $hari['waktu'];
                        } elseif (empty($event['waktu'])) {
                            $bataswaktu = 'Libur';
                        } else {
                            $bataswaktu = $event['waktu'];
                        }

                        if ($bataswaktu !== 'Libur') {
                            if ($waktusekarang > $bataswaktu) {
                                $statuswaktu = 'telat';
                                $statusText = '-- Telat --';
                            } else {
                                $statuswaktu = 'masuk';
                                $statusText = '-- Tepat Waktu --';
                            }
                        } else {
                            $statuswaktu = '';
                            $statusText = '-- Libur --';
                        }
                        ?>

                        <div class="time-container <?= $statuswaktu ?>">
                            <h1 id="time">
                                <?= ($bataswaktu !== 'Libur') ? date('H:i', strtotime($bataswaktu)) : $bataswaktu ?>
                            </h1>
                        </div>
                        <div class="time-status">
                            <p id="status"><?= $statusText ?></p>
                        </div>
                    </div>

                    <div class="table-data">
                        <table>
                            <thead>
                                <tr>
                                    <td class="table-no">No</td>
                                    <td class="table-nma">Siswa</td>
                                    <td class="table-wkt">Waktu</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = $offset + 1;
                                if (empty($absensi)) {
                                    echo "<tr>
                                            <td colspan='5' style='text-align:center; padding:10px;'>
                                                <p>Tidak ada yang absen.</p>
                                            </td>
                                        </tr>";
                                } else {
                                    foreach ($absensi as $absen) {
                                        $siswa = getNamaSiswa($conn, $absen['siswa']);
                                        echo "<tr>
                                                <td class='table-no'>{$no}</td>
                                                <td class='table-nma'>{$siswa}</td>
                                                <td class='table-wkt'>{$absen['waktu']}</td>
                                            </tr>";
                                        $no++;
                                    }
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>