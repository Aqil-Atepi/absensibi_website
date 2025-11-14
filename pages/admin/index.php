<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    function getAkun($conn, $id) {
        $stmt = $conn->prepare("SELECT username FROM administratif WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data["username"];
    }

    $admin = getAkun($conn, $_SESSION["id"]);

    function getCount($conn, $table)
    {
        $availabletables = ['siswa', 'guru', 'kelas'];

        if (!in_array($table, $availabletables)) {
            return 'No Table Found!';
        }

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $data = $row['total'];
        $stmt->close();
        return $data;
    }

    function getTable($conn)
    {
        $stmt = $conn->prepare('SELECT * FROM event');
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

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
                align-items: center;
                justify-content: baseline;
            }

            .data {
                width: 650px;
                height: 700px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: baseline;

                gap: 30px;
            }

            .top-data {
                width: 620px;
                height: 200px;
            }

            .bottom-data {
                width: 620px;
                height: 460px;
            }

            /* PROFILE */

            .top-data.profile {
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
                font-size: 30px;
                font-weight: bold;
            }

            .profile-info p {
                font-size: 15px;
            }

            /* SUMMARY DATA */

            .bottom-data.summary {
                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: center;

                gap: 10px;
            }

            .summary-items {
                width: 620px;
                height: 146px;

                padding: 20px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: center;

                color: var(--color2);
            }

            .summary-items h1 {
                font-size: 35px;
                font-weight: bold;
            }

            .summary-items p {
                font-size: 20px;
            }

            .summary-items:hover {
                background-color: var(--color3);
                color: var(--color1a);
            }

            /* TIME */

            .top-data.time {
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

            /* EVENT */

            .bottom-data.event {
                background-color: var(--color3);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                overflow: hidden;

                display: flex;
                align-items: baseline;
                justify-content: center;
            }

            .bottom-data.event table {
                width: 620px;

                border-collapse: collapse;
                table-layout: fixed;
            }

            .bottom-data.event table thead {
                background-color: var(--color1);
                border-bottom: 5px solid var(--color1a);

                color: var(--color2);
                font-weight: bold;
            }

            .bottom-data.event table tr {
                height: 40px;
            }

            .bottom-data.event table td {
                padding-left: 10px;

                max-width: 300px;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table-no {
                width: 50px;
            }

            .table-tgl {
                width: 160px;
            }

            .table-evn {
                width: 160px;
            }

            .table-sts {
                width: 100px;
            }

            .table-wkt {
                width: 150px;
            }

            .bottom-data.event table tbody tr:hover {
                background-color: var(--color2);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Dashboard Admin</h1>
            </div>

            <div class="content">
                <div class="data">
                    <div class="top-data profile">
                        <div class="profile-container">
                            <div class="photo-profile">
                                <img src="../../assets/images/logo-bi.png" alt="Logo SMK Bina Informatika">
                            </div>

                            <div class="profile-info">
                                <h1> <?= $admin ?> </h1>
                                <p>Administrasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="bottom-data summary">

                        <?php
                        $totals = getCount($conn, 'siswa');
                        $totalg = getCount($conn, 'guru');
                        $totalk = getCount($conn, 'kelas');
                        ?>

                        <a href="akun.php">
                            <div class="summary-items">
                                <h1> <?php echo '' . $totals ?> Siswa</h1>
                                <p>Total Siswa</p>
                            </div>
                        </a>

                        <a href="akun.php?table=guru">
                            <div class="summary-items">
                                <h1> <?php echo '' . $totalg ?> Guru</h1>
                                <p>Total Guru</p>
                            </div>
                        </a>

                        <a href="kelas.php">
                            <div class="summary-items">
                                <h1> <?php echo '' . $totalk ?> Kelas</h1>
                                <p>Total Kelas</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="data">
                    <div class="top-data time">
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
                                <?= ($bataswaktu !== 'Libur') ? date('H:i', strtotime($bataswaktu)) : $bataswaktu ?></h1>
                        </div>
                        <div class="time-status">
                            <p id="status"><?= $statusText ?></p>
                        </div>
                    </div>


                    <div class="bottom-data event">
                        <table>
                            <thead>
                                <tr>
                                    <td class="table-no">No</td>
                                    <td class="table-evn">Event</td>
                                    <td class="table-tgl">Tanggal</td>
                                    <td class="table-sts">Status</td>
                                    <td class="table-wkt">Waktu Masuk</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $events = getTable($conn);

                                $no = 1;

                                if (empty($events)) {
                                    echo "<tr>
                                        <td colspan='5' style='text-align:center; padding:10px;'>
                                            <p>Tidak ada data yang tersedia.</p>
                                        </td>
                                    </tr>";
                                } else {

                                    foreach ($events as $event) {
                                        $tglmulai = new DateTime($event['tanggalmulai']);
                                        $tglselesai = new DateTime($event['tanggalselesai']);

                                        if (!empty($event['waktu']))
                                            $waktu = date('H:i', strtotime($event['waktu']));
                                        else
                                            $waktu = '-';

                                        if ($tglmulai->format('j F Y') === $tglselesai->format('j F Y')) {
                                            $tanggal = $tglselesai->format('j F Y');
                                        } elseif ($tglmulai->format('F Y') === $tglselesai->format('F Y')) {
                                            $tanggal = $tglmulai->format('j') . ' - ' . $tglselesai->format('j F Y');
                                        } elseif ($tglmulai->format('Y') === $tglselesai->format('Y')) {
                                            $tanggal = $tglmulai->format('j F') . ' - ' . $tglselesai->format('j F Y');
                                        } else {
                                            $tanggal = $tglmulai->format('j F Y') . ' - ' . $tglselesai->format('j F Y');
                                        }

                                        echo '<tr>
                                                <td class="table-no">' . $no++ . '</td>
                                                <td class="table-evn">' . $event['nama'] . '</td>
                                                <td class="table-tgl">' . $tanggal . '</td>
                                                <td class="table-sts">' . $event['status'] . '</td>
                                                <td class="table-wkt">' . $waktu . '</td>
                                            </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- <script>
            const jadwalWaktu = "<?php echo isset($hari['waktu']) ? $hari['waktu'] : '08:00:00'; ?>";

            function updateTime() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, "0");
                const m = String(now.getMinutes()).padStart(2, "0");
                const s = String(now.getSeconds()).padStart(2, "0");

                // Convert jadwal waktu to Date for comparison
                const [jh, jm, js] = jadwalWaktu.split(":").map(Number);
                const jadwalDate = new Date();
                jadwalDate.setHours(jh, jm, js);

                let status;
                const nowDate = new Date();
                if (nowDate <= jadwalDate) {
                    status = "-- Tepat Waktu --";
                    document.querySelector(".time-container").style.backgroundColor = "var(--indicator1)";
                } else {
                    status = "-- Telat --";
                    document.querySelector(".time-container").style.backgroundColor = "var(--indicator2)";
                }

                document.getElementById("time").textContent = `${h}:${m}:${s}`;
                document.getElementById("status").textContent = status;
            }

            updateTime();
            setInterval(updateTime, 1000);
        </script> -->

    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>