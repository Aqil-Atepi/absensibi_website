<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['hari']) {
    require_once "../../conn.php";

    $idhari = $_GET['hari'];

    function getHari($conn, $hari)
    {
        $stmt = $conn->prepare('SELECT * FROM jadwal WHERE id=?');
        $stmt->bind_param('s', $hari);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    $hari = getHari($conn, $idhari);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $idhari = $_POST["idhari"];
        $status = $_POST["status"];
        $waktu = $_POST["waktu"];

        $stmt = $conn->prepare("UPDATE jadwal SET status=?, waktu=? WHERE id=?");
        $stmt->bind_param("ssi", $status, $waktu, $idhari);
        $stmt->execute();
        $stmt->close();

        header("Location: jadwal.php");
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Jadwal</title>
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
                height: 170px;
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
                <h1>Jadwal Hari <?= $hari['hari'] ?></h1>
            </div>

            <div class="content">
                <form method="POST">
                    <div class="form-container">
                        <div class="form-items">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="Masuk" <?= ($hari['status'] === 'Masuk') ? 'selected' : '' ?>>Masuk</option>
                                <option value="Libur" <?= ($hari['status'] === 'Libur') ? 'selected' : '' ?>>Libur</option>
                            </select>
                        </div>

                        <div class="form-items">
                            <label>Waktu Masuk</label>
                            <input type="time" name="waktu" id="waktu" placeholder="Masukkan Waktu Masuk..."
                                value="<?= $hari['waktu'] ?>" required disabled>
                        </div>
                    </div>

                    <input type="text" name="idhari" value="<?= $hari['id'] ?>" hidden>

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

                function toggleWaktuInput() {
                    if (statusSelect.value === "Masuk") {
                        waktuInput.disabled = false;
                    } else {
                        waktuInput.disabled = true;
                        waktuInput.value = "";
                    }
                }

                toggleWaktuInput();

                statusSelect.addEventListener("change", toggleWaktuInput);
            });
        </script>

    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>