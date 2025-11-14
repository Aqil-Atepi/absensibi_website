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
            width: 90%;
            height: 30%;

            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: baseline;
        }

        .content form {
            width: 100%;
            height: 100%;

            padding: 10px;

            background-color: var(--color1);
            border-radius: 10px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: baseline;
        }

        .content-items {
            width: 100%;
            height: 33%;

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;

            gap: 10px;
        }

        .content-items .items {
            flex: 1;

            width: 100%;
            height: auto;

            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: center;

            padding: 10px;
        }

        .content-items .items label {
            width: 100%;
            height: 20%;

            color: var(--color3);
            font-size: 15px;
        }

        .content-items .items input[type=text],
        .content-items .items input[type=date],
        .content-items .items input[type=time],
        .content-items .items .dropdown select {
            width: 97%;
            height: 80%;

            padding: 10px;
            border: none;
            border-radius: 10px;
            text-align: center;

            background-color: var(--color3);
        }

        .content-items .items input[type=text] {
            text-align: left;
        }

        .content-items .items .dropdown {
            width: 103%;
            height: 80%;
        }

        .content-items .items .switch-box .switch {
            width: 500px;
            height: 40px;

            position: absolute;

            background-color: #00000028;
        }

        .content-items .items button {
            width: 97%;
            height: 80%;

            padding: 10px;
            border: none;
            border-radius: 10px;
            background-color: var(--indicator1);
            color: var(--color3);
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="container">
        <div class="title">
            <h1>Tambah Event</h1>
        </div>

        <div class="content">
            <form method="POST">
                <div class="content-items">
                    <div class="items">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal-mulai" placeholder="Masukkan Tanggal Mulai Event">
                    </div>

                    <div class="items">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tanggal-akhir" placeholder="Masukkan Tanggal Akhir Event">
                    </div>
                </div>

                <div class="content-items">
                    <div class="items">
                        <label>Nama Event</label>
                        <input type="text" name="nama-event" placeholder="Masukkan Nama Event">
                    </div>

                    <div class="items">
                        <label>Masuk / Libur</label>
                        <div class="dropdown">
                            <select id="masuklibur">
                                <option value="libur">Libur</option>
                                <option value="masuk">Masuk</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="content-items">
                    <div class="items">
                        <label>Waktu Masuk</label>
                        <input type="time" name="waktu-masuk" placeholder="Masukkan Waktu Masuk Event">
                    </div>

                    <div class="items">
                        <br>
                        <button type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>