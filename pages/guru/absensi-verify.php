<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru' && $_GET['idabsen']) {
    require_once "../../conn.php";

    $idabsen = $_GET["idabsen"];

    $stmt = $conn->prepare("SELECT * FROM absensi WHERE id=?");
    $stmt->bind_param("i", $idabsen);
    $stmt->execute();
    $result = $stmt->get_result();
    $absen = $result->fetch_assoc();

    function getDataSiswa($conn, $nis)
    {
        $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis=?");
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    $siswa = getDataSiswa($conn, $absen['siswa']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        $idabsen = $_POST['idabsen'];

        if ($status == 'Ditolak') {
            $stmt = $conn->prepare('DELETE FROM absensi WHERE id=?');
            $stmt->bind_param('i', $idabsen);
            $stmt->execute();
            $stmt->close();

            header('Location: absensi.php');
        } elseif ($status == 'Diterima') {
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

            if ($event == '-') {
                $bataswaktu = $hari['waktu'];
            } elseif (!empty($event['waktu'])) {
                $bataswaktu = $event['waktu'];
            }

            if ($bataswaktu !== 'Libur') {
                if ($waktusekarang > $bataswaktu) {
                    $statuswaktu = 'telat';
                } else {
                    $statuswaktu = 'masuk';
                }
            }

            if ($statuswaktu == 'masuk') {
                $stmt = $conn->prepare("UPDATE absensi SET status='Diterima', absen='Tepat Waktu' WHERE id=?");
                $stmt->bind_param('i', $idabsen);
                $stmt->execute();
                $stmt->close();

                header('Location: absensi.php');
            } elseif ($statuswaktu == 'telat') {
                $stmt = $conn->prepare("UPDATE absensi SET status='Diterima', absen='Telat' WHERE id=?");
                $stmt->bind_param('i', $idabsen);
                $stmt->execute();
                $stmt->close();

                header('Location: absensi.php');
            }
        }
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Absensi</title>
        <style>
            .content {
                width: 1300px;
                height: 700px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: start;

                gap: 10px;
            }

            /* COMPARE */
            .compare {
                width: 1300px;
                height: 620px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .compare-img {
                width: 645px;
                height: 620px;

                display: flex;
                align-items: center;
                justify-content: center;
            }

            .compare-img img {
                width: 640px;
                height: 615px;

                object-fit: contain;
            }

            /* CONTROL */
            .control {
                width: 1300px;
                height: 70px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .control button {
                width: 500px;
                height: 50px;

                border: 2px solid none;
                border-radius: 10px;

                color: var(--color2);
                font-size: 20px;
                font-weight: bold;
            }

            #tolak {
                background-color: var(--indicator2);
                border-color: var(--indicator2a);
            }

            #tolak:hover {
                background-color: var(--indicator2b);
                color: var(--color4);
            }

            #terima {
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
            }

            #terima:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Verifikasi Absen <?= $siswa['nama'] ?></h1>
            </div>

            <div class="content">
                <div class="compare">
                    <div class="compare-img">
                        <?php if (!empty($siswa['foto'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($siswa['foto']) ?>" alt="Foto Siswa">
                        <?php else: ?>
                            <img src="../../assets/images/placeholder.png" alt="Default Foto">
                        <?php endif; ?>
                    </div>

                    <div class="compare-img">
                        <?php if (!empty($absen['foto'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($absen['foto']) ?>" alt="Foto Absen">
                        <?php else: ?>
                            <img src="../../assets/images/placeholder.png" alt="Default Foto">
                        <?php endif; ?>
                    </div>
                </div>


                <div class="control">
                    <form method="POST">
                        <input type="text" name="idabsen" value="<?= $absen['id'] ?>" hidden>
                        <input type="text" name="status" value="Ditolak" hidden>
                        <button type="submit" id="tolak">Tolak</button>
                    </form>

                    <form method="POST">
                        <input type="text" name="idabsen" value="<?= $absen['id'] ?>" hidden>
                        <input type="text" name="status" value="Diterima" hidden>
                        <button type="submit" id="terima">Terima</button>
                    </form>
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