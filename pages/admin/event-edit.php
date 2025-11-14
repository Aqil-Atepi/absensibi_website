<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['idevent']) {

    require_once "../../conn.php";

    $idevent = $_GET["idevent"];

    function getEvent($conn, $id)
    {
        $stmt = $conn->prepare("SELECT * FROM event WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    $event = getEvent($conn, $idevent);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $idevent = $_POST["idevent"];
        $nama = $_POST['nama'];
        $tanggalmulai = $_POST['tanggalmulai'];
        $tanggalselesai = $_POST['tanggalselesai'];
        $status = $_POST['status'];
        $waktu = $_POST['waktu'] ? $_POST['waktu'] : null;

        $check = $conn->prepare("
            SELECT COUNT(*) AS count FROM event
            WHERE tanggalmulai <= ? AND tanggalselesai >= ?
        ");
        
        $check->bind_param("ss", $tanggalselesai, $tanggalmulai);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $check->close();

        if ($result['count'] > 0) {
            echo "<script>alert('Tanggal tersebut sudah memiliki event lain!'); window.history.back();</script>";
            exit;
        } else {
            $stmt = $conn->prepare("UPDATE event SET nama=?, tanggalmulai=?, tanggalselesai=?, status=?, waktu=? WHERE id=?");
            $stmt->bind_param('sssssi', $nama, $tanggalmulai, $tanggalselesai, $status, $waktu, $idevent);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: jadwal.php');
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Event</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: 700px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;
            }

            /* FORM */
            .content form {
                width: 1300px;
                height: 350px;
                padding: 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;
                background-color: var(--color1);
                border: 5px solid var(--color1a);
                border-radius: 10px;
            }

            .form-container {
                width: 1270px;
                height: 80px;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;
                gap: 20px;
            }

            .form-items {
                width: 625px;
                display: flex;
                flex-direction: column;
            }

            .form-items input,
            .form-items select {
                width: 625px;
                height: 50px;
                border: 2px solid var(--color3);
                border-radius: 10px;
                overflow: hidden;
                padding-left: 10px;
                font-size: 15px;
            }

            .form-items.single input {
                width: 1270px;
            }

            .form-container button {
                font-size: 20px;
                color: var(--color2);
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
            }

            .form-container button {
                width: 1270px;
                height: 50px;
                border-radius: 10px;
            }

            .form-container button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

            .form-items label {
                font-size: 15px;
                color: var(--color2);
            }
        </style>
    </head>

    <body>
        <?php include "sidebar.php"; ?>

        <div class="container">
            <div class="title">
                <h1>Edit Event <?= $event['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST">
                    <div class="form-container">
                        <div class="form-items single">
                            <label>Nama Event</label>
                            <input type="text" name="nama" placeholder="Masukkan Nama Event" value="<?= $event['nama'] ?>"
                                required>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tanggalmulai" id="tanggalmulai" placeholder="Masukkan Tanggal Mulai..."
                                value="<?= $event['tanggalmulai'] ?>" required>
                        </div>

                        <div class="form-items">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="tanggalselesai" id="tanggalselesai"
                                placeholder="Masukkan Tanggal Selesai..." value="<?= $event['tanggalselesai'] ?>" required>
                        </div>
                    </div>


                    <div class="form-container">
                        <div class="form-items">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="Masuk" <?= ($event['status'] === 'Masuk') ? 'selected' : '' ?>>Masuk</option>
                                <option value="Libur" <?= ($event['status'] === 'Libur') ? 'selected' : '' ?>>Libur</option>
                            </select>
                        </div>

                        <div class="form-items">
                            <label>Waktu Masuk</label>
                            <input type="time" name="waktu" id="waktu" placeholder="Masukkan Waktu Masuk..."
                                value="<?= $event['waktu'] ?>" required disabled>
                        </div>
                    </div>

                    <input type="text" name="idevent" value="<?= $idevent ?>" hidden>

                    <div class="form-container">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const statusSelect = document.querySelector('select[name="status"]');
                const waktuInput = document.getElementById('waktu');
                const tanggalMulai = document.getElementById('tanggalmulai');
                const tanggalSelesai = document.getElementById('tanggalselesai');

                function toggleWaktuInput() {
                    if (statusSelect.value === "Masuk") {
                        waktuInput.disabled = false;
                    } else {
                        waktuInput.disabled = true;
                        waktuInput.value = "";
                    }
                }

                function updateTanggalSelesaiMin() {
                    tanggalSelesai.min = tanggalMulai.value;

                    if (tanggalSelesai.value && tanggalSelesai.value < tanggalMulai.value) {
                        tanggalSelesai.value = tanggalMulai.value;
                    }
                }

                toggleWaktuInput();
                updateTanggalSelesaiMin();

                statusSelect.addEventListener("change", toggleWaktuInput);
                tanggalMulai.addEventListener("change", updateTanggalSelesaiMin);
            });
        </script>



    </body>

    </html>

    <?php
} else {
    header("Location: jadwal.php");
}
?>