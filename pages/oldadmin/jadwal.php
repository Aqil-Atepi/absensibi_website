<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../../");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../assets/styles/main.css" rel="stylesheet">
    <title>Jadwal Sekolah</title>
    <style>
        .content {
            width: 100%;
            height: 85%;

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: baseline;

            gap: 10px;
        }

        .jadwal-absensi-sekolah {
            flex: 1;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: baseline;

            gap: 10px;

            width: 100%;
            height: 100%;
        }

        .jadwal-absensi-sekolah .title{
            flex: 0.33;

            margin: 0px;

            width: 97%;
            height: 100%;

            padding: 0px 5px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            background-color: var(--color1);

            border-radius: 10px;

            color: var(--color3);
        }

        .hari-efektif-izin {
            flex: 1;

            width: 97%;
            height: 100%;

            padding: 10px 5px;

            background-color: var(--color1);

            border-radius: 10px;

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;

            gap: 10px;
        }

        .hari-efektif-izin .control {
            flex: 1;

            width: 100%;
            height: 100%;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            padding: 5px;

            gap: 10px;
        }

        .hari-efektif-izin .control div {
            flex: 1;

            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hari-efektif-izin .control div input,
        .hari-efektif-izin .control div button {
            width: 100%;
            height: 100%;

            border: none;
            border-radius: 10px;

            text-align: center;

            font-size: 20px;
        }

        .hari-efektif-izin .control div a {
            width: 100%;
            height: 100%;
        }

        .hari-efektif-izin .control div button {
            color: var(--color3);
            background-color: var(--indicator1);

            font-weight: bold;
        }

        .hari-efektif-izin .view {
            flex: 1;

            width: 100%;
            height: 100%;

            padding: 5px;
        }

        .hari-efektif-izin .view .efektif-izin {
            flex: 1;

            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hari-efektif-izin .view .efektif-izin input {
            width: 100%;
            height: 100%;

            border: none;
            border-radius: 10px;

            text-align: center;

            font-size: 50px;
            font-weight: bold;
        }

        .jadwal-sekolah {
            flex: 3;

            width: 97%;
            height: 100%;

            padding: 10px 5px;

            background-color: var(--color1);

            border-radius: 10px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 10px;
        }

        .jadwal-sekolah a {
            flex: 1;

            width: 98%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            text-decoration: none;
        }

        .jadwal-sekolah a button {
            width: 100%;
            height: 100%;

            text-align: center;

            border: none;
            border-radius: 10px;

            font-size: 20px;
            font-weight: bold;

            background-color: var(--color2);
            color: var(--color4);
        }

        .event-sekolah {
            flex: 1;

            width: 100%;
            height: 100%;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: baseline;

            gap: 10px;
        }

        .event-sekolah .control {
            flex: 1;
            width: 100%;
            height: 100%;

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;

            background-color: var(--color1);

            color: var(--color3);

            border-radius: 10px;
        }

        .event-sekolah .control p {
            font-size: 20px;
            font-weight: bold;

            padding-left: 15px;
        }

        .event-sekolah .control a {
            width: 15%;
            padding-right: 15px;
        }

        .event-sekolah .control a button {
            padding: 5px;

            width: 100%;

            border: none;
            border-radius: 10px;

            color: var(--color3);

            background-color: var(--indicator1);
        }

        .event-table {
            flex: 15;

            width: 100%;
            height: 100%;

            border: 1px solid var(--color5);
            border-radius: 10px;

            background-color: var(--color2);

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: baseline;

            overflow: hidden;
        }

        .event-table table {
            width: 100%;

            border-collapse: collapse;
        }

        .event-table table thead {
            background-color: var(--color1);
            color: var(--color3);

            border-bottom: 1px solid var(--color5);
        }

        .event-table table th {
            text-align: left;

            padding: 10px;

            font-size: 20px;
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
            <h1>Jadwal</h1>
        </div>

        <div class="content">
            <div class="jadwal-absensi-sekolah">
                <div class="title">
                    <p>Hari Efektif Izin</p>
                </div>
                <div class="hari-efektif-izin">
                    <div class="control">
                        <div class="hari-efektif">
                            <input type="number" name="hari-efektif" placeholder="Hari Efektif Sekolah">
                        </div>

                        <div class="persentase-absensi">
                            <input name="persentase-masuk" value="90%" readonly>
                        </div>

                        <div class="submit-hari-efektif">
                            <a href="#">
                                <button>Submit</button>
                            </a>
                        </div>
                    </div>

                    <div class="view">
                        <div class="efektif-izin">
                            <input name="hari-efektif-izin" value="9 Hari" readonly>
                        </div>
                    </div>
                </div>
                <div class="title">
                    <p>Jadwal Harian Sekolah</p>
                </div>
                <div class="jadwal-sekolah">
                    <a href="#">
                        <button>Senin</button>
                    </a>
                    <a href="#">
                        <button>Selasa</button>
                    </a>
                    <a href="#">
                        <button>Rabu</button>
                    </a>
                    <a href="#">
                        <button>Kamis</button>
                    </a>
                    <a href="#">
                        <button>Jumat</button>
                    </a>
                    <a href="#">
                        <button>Sabtu</button>
                    </a>
                    <a href="#">
                        <button>Minggu</button>
                    </a>
                </div>
            </div>

            <div class="event-sekolah">
                <div class="control">
                    <p>Event</p>
                    <a href="jadwal-event-create.php">
                        <button>Tambah</button>
                    </a>
                </div>

                <div class="event-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Kegiatan</th>
                                <th>Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>28 Oktober 2025</td>
                                <td>Hari Libur</td>
                                <td>Libur</td>
                            </tr>

                            <tr>
                                <td>1</td>
                                <td>28 Oktober 2025</td>
                                <td>Hari Libur</td>
                                <td>Libur</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>


</html>