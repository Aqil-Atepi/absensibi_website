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
        $idabsen = $_POST['idabsen'];
        $absen = $_POST['absen'];
        $siswa = $_POST['siswa'];

        $stmt = $conn->prepare("UPDATE absensi SET absen=? WHERE id=?");
        $stmt->bind_param('si', $absen, $idabsen);
        $stmt->execute();
        $stmt->close();

        header('Location: siswa-detail.php?nis=' . $siswa);
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
            .control form {
                width: 1300px;
                height: 70px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 20px;
            }

            .control button,
            .control select {
                width: 500px;
                height: 50px;

                border: 2px solid none;
                border-radius: 10px;

                padding-left: 10px;
            }

            #terima {
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
                color: var(--color2);
                font-size: 20px;
                font-weight: bold;
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
                        <select name="absen">
                            <option value="Tepat Waktu" <?= ($absen['absen'] == 'Tepat Waktu') ? 'selected' : '' ?>>Tepat Waktu
                            </option>
                            <option value="Telat" <?= ($absen['absen'] == 'Telat') ? 'selected' : '' ?>>Telat</option>
                            <option value="Sakit" <?= ($absen['absen'] == 'Sakit') ? 'selected' : '' ?>>Sakit</option>
                            <option value="Izin" <?= ($absen['absen'] == 'Izin') ? 'selected' : '' ?>>Izin</option>
                            <option value="Alpha" <?= ($absen['absen'] == 'Alpha') ? 'selected' : '' ?>>Alpha</option>
                        </select>
                        <input type="text" name="idabsen" value="<?= $absen['id'] ?>" hidden>
                        <input type="text" name="siswa" value="<?= $siswa['nis'] ?>" hidden>
                        <button type="submit" id="terima">Update</button>
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