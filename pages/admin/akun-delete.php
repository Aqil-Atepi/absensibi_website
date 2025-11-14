<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['table'] && $_GET['nomorinduk']) {

    require_once "../../conn.php";

    $inputTable = $_GET['table'];
    $nomorinduk = $_GET['nomorinduk'];
    $availableTable = ['siswa', 'guru'];

    if (!in_array($inputTable, $availableTable))
        $table = 'No Table Found!';
    else
        $table = $inputTable;

    if ($table == 'siswa') {
        $stmt = $conn->prepare("SELECT nama FROM siswa WHERE nis=?");
        $stmt->bind_param("s", $nomorinduk);
        $stmt->execute();
        $result = $stmt->get_result();
        $akun = $result->fetch_assoc();
    } elseif ($table == 'guru') {
        $stmt = $conn->prepare("SELECT nama FROM guru WHERE nik=?");
        $stmt->bind_param("s", $nomorinduk);
        $stmt->execute();
        $result = $stmt->get_result();
        $akun = $result->fetch_assoc();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $table = $_POST["table"];
        $nomorinduk = $_POST["nomorinduk"];

        if ($table == 'siswa') {
            $stmt = $conn->prepare("DELETE FROM siswa WHERE nis=?");
            $stmt->bind_param("s", $nomorinduk);
            $stmt->execute();
            header("Location: akun.php");
        } elseif ($table == 'guru') {
            $stmt = $conn->prepare("DELETE FROM guru WHERE nik=?");
            $stmt->bind_param("s", $nomorinduk);
            $stmt->execute();
            header("Location: akun.php?table=guru");
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
        <title>Akun</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: 700px;
                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: baseline;
            }

            /* FORM */
            .content form {
                width: 500px;
                height: 300px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;
            }

            .form-container {
                width: 450px;
                height: 120px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                text-align: center;

                gap: 10px;
            }

            .form-container p {
                font-size: 17px;
                color: var(--color2);
            }

            .form-container button {
                width: 400px;
                height: 50px;

                font-size: 17px;
                color: var(--color2);

                border-radius: 10px;
            }

            #confirm {
                background-color: var(--indicator1);

                border: 2px solid var(--indicator1a);
            }

            #confirm:hover {
                background-color: var(--indicator1b);
            }

            #decline {
                background-color: var(--indicator2);

                border: 2px solid var(--indicator2a);
            }

            #decline:hover {
                background-color: var(--indicator2b);
            }
        </style>
    </head>

    <body>
        <?php include "sidebar.php"; ?>

        <div class="container">
            <div class="title">
                <h1>Delete Akun <?= $akun['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST">
                    <div class="form-container">
                        <p>*Warning <br> Akun yang dihapus tidak bisa dikembalikan lagi. <br> Apakah anda yakin?</p>
                    </div>

                    <input name="table" value="<?= $table ?>" hidden>
                    <input name="nomorinduk" value="<?= $nomorinduk ?>" hidden>

                    <div class="form-container">
                        <button type="submit" id="confirm">Ya, Saya Yakin</button>

                        <a href="akun.php?table=<?= ($table == 'siswa') ? 'siswa' : 'guru' ?>">
                            <button type="button" id="decline">Tidak, Saya Tidak Yakin</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </body>

    </html>

    <?php
} else {
    header("Location: akun.php");
}
?>