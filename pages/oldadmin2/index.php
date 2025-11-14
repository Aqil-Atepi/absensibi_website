<?php
session_start();

if (isset($_SESSION["username"])) {
    require_once "../../conn.php";

    $username = $_SESSION["username"];

    function getCount($conn, $table)
    {
        $accesstable = ['siswa', 'guru', 'kelas'];

        if (!in_array($table, $accesstable)) {
            throw new Exception("Invalid table name");
        }

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table");
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc();
        return $total['total'];
    }

    function getEvent($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM eventsekolah");
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
        <title>Dashboard</title>
        <style>
            .container {
                width: auto;
                height: 100vh;
                margin-left: 200px;
                padding: 0px 10px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: baseline;
            }

            .title {
                margin: 10px 0px;
                font-size: 20px;
                font-weight: bolder;
            }

            .content {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: row;
            }

            .data {
                flex: 4;
                display: flex;
                flex-direction: column;

                gap: 10px;

                margin: 0px 5px;

                margin-bottom: 10px;
            }

            .profile {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;

                background: url("../../assets/images/doodle-inverse.png") no-repeat center;
                background-size: cover;

                border-radius: 10px;
            }

            .user {
                width: 80%;
                height: 40%;

                background-color: var(--color6);

                border-radius: 10px;

                padding: 0px 20px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;

                gap: 20px;

                color: var(--color3);
            }

            .profile-photo {
                border-radius: 10px;

                width: 100px;
                height: 100px;

                overflow: hidden;
            }

            .profile-photo img {
                width: 100%;
                height: 100%;
            }

            .info {
                display: flex;
                flex-direction: column;
                justify-content: baseline;
            }

            .info * {
                margin: 0;
            }

            .info h1 {
                font-size: 20px;
            }

            .info p {
                font-size: 15px;
            }

            .summary {
                flex: 1;

                width: 100%;
                height: auto;

                display: grid;
                grid-template-columns: repeat(1, 1fr);
                gap: 10px;
            }

            .summary-item {
                padding: 20px;

                border-radius: 10px;

                background-color: var(--color6);

                color: var(--color3);

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: center;
            }

            .summary * {
                margin: 0;
            }

            .summary-item h1 {
                font-size: 30px;
            }

            .summary-item p {
                font-size: 15px;
            }

            .data * {
                margin: 0;
            }

            .timer-container {
                width: 100%;
                height: 20%;

                padding: 10px 0px;

                background-color: yellow;

                border-radius: 10px;

                color: var(--color3);
                background-color: var(--color6);

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .timer {
                background-color: var(--indicator1);
                border-radius: 10px;
                padding: 0px 12px;
            }

            .timer h1 {
                font-size: 80px;
                font-weight: bolder;
            }

            .event-container {
                width: 100%;
                height: 80%;
                border-radius: 10px;
                background-color: var(--color6);
                color: var(--color3);

                padding: 10px 0px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;
                overflow: hidden;
            }

            .event-table {
                width: 95%;

                height: 100%;
                margin: 10px;

                background-color: var(--color2);

                border-radius: 10px;

                color: var(--color4);

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;
                overflow: hidden;

                border: 1px solid var(--color5);
            }

            .event-table table {
                width: 100%;
                border-collapse: collapse;
            }

            .event-table table thead {
                background-color: var(--color7);

                color: var(--color4);

                border-bottom: 1px solid var(--color5);
            }

            .event-table table th {
                text-align: left;

                padding: 10px;

                font-size: 15px;
                font-weight: bold;
                position: relative;
            }

            .event-table table tbody td {
                padding: 10px;
                font-size: 15px;
            }

            .event-table table tbody tr:hover {
                background-color: var(--color3);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Dashboard</h1>
            </div>
            <div class="content">
                <div class="data">
                    <div class="profile">
                        <div class="user">
                            <div class="profile-photo">
                                <img src="../../assets/images/logo-bi.png">
                            </div>
                            <div class="info">
                                <h1><?php echo "" . $username ?></h1>
                                <p>Administrasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="summary">
                        <div class="summary-item">
                            <h1><?php echo "" . getCount($conn, 'siswa') ?> Siswa</h1>
                            <p>Total Siswa</p>
                        </div>
                        <div class="summary-item">
                            <h1><?php echo "" . getCount($conn, 'guru') ?> Guru</h1>
                            <p>Total Guru</p>
                        </div>
                        <div class="summary-item">
                            <h1><?php echo "" . getCount($conn, 'kelas') ?> Kelas</h1>
                            <p>Total Kelas</p>
                        </div>
                    </div>
                </div>

                <div class="data" style="flex: 5;">
                    <div class="timer-container">
                        <p>Batas Waktu Masuk</p>
                        <div class="timer">
                            <h1>08:00 AM</h1>
                        </div>
                    </div>
                    <div class="event-container">
                        <p>Jadwal Tambahan Sekolah</p>

                        <div class="event-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $events = getEvent($conn);

                                    $no = 1;

                                    foreach ($events as $event) {
                                        $nama = $event['namaevent'];
                                        $mulai = $event['tanggalmulai'];
                                        $selesai = $event['tanggalselesai'];
                                        $keterangan = $event['keterangan'];
                                        $masuk = $event['waktumasuk'];

                                        if (date("F Y", strtotime($mulai)) == date("F Y", strtotime($selesai))) {
                                            $tanggal = date("j", strtotime($mulai)) . " - " . date("j F Y", strtotime($selesai));
                                        } else {
                                            $tanggal = date("j F Y", strtotime($mulai)) . " - " . date("j F Y", strtotime($selesai));
                                        }

                                        echo '<tr>
                                                    <td>' . $no++ . '</td>
                                                    <td>' . $tanggal . '</td>
                                                    <td>' . $nama . '</td>
                                                    </tr>';
                                    }

                                    if (count($events) == 0) {
                                        echo '<p> Tidak Ada Event </p>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
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