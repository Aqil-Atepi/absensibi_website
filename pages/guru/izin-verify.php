<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru' && $_GET['idizin']) {
    require_once "../../conn.php";

    $idizin = $_GET["idizin"];

    $stmt = $conn->prepare("SELECT * FROM izin WHERE id=?");
    $stmt->bind_param("i", $idizin);
    $stmt->execute();
    $result = $stmt->get_result();
    $izin = $result->fetch_assoc();

    function getDataSiswa($conn, $nis)
    {
        $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis=?");
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data;
    }

    $siswa = getDataSiswa($conn, $izin['siswa']);

    function getNamaKelas($conn, $id)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['nama'] ?? '-';
    }

    $kelas = getNamaKelas($conn, $siswa['kelas']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $aksi = $_POST['aksi'];
        $id = $_POST['id'];

        if ($aksi === "terima") {
            $stmt = $conn->prepare("UPDATE izin SET status='Diterima' WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        if ($aksi === "tolak") {
            $stmt = $conn->prepare("DELETE FROM izin WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        header("Location: izin.php");
        exit;
    }

    function detectMimeFromBlob($blob)
    {
        $bytes = bin2hex(substr($blob, 0, 4));

        switch (true) {
            case str_starts_with($bytes, "ffd8"):
                return "image/jpeg";
            case str_starts_with($bytes, "89504e47"):
                return "image/png";
            case str_starts_with($bytes, "47494638"):
                return "image/gif";
            case str_starts_with($bytes, "424d"):
                return "image/bmp";
            case str_starts_with($bytes, "52494646"):
                return "image/webp";
            default:
                return "application/octet-stream";
        }
    }

    $mime = detectMimeFromBlob($izin['foto']);
    $encodedfoto = base64_encode($izin['foto']);



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
                align-items: center;
                justify-content: baseline;
            }

            /* FORM */
            .content form {
                width: 1300px;
                height: 400px;
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
            .form-items div label {
                width: 625px;
                height: 50px;
                border: 2px solid var(--color3);
                border-radius: 10px;
                overflow: hidden;
            }

            .form-items.single input {
                width: 1270px;
            }

            .form-items input {
                padding-left: 10px;
                font-size: 15px;
            }

            .form-items input[type="file"] {
                display: none;
            }

            .form-items div label {
                display: block;
                display: flex;
                align-items: center;
                justify-content: center;
                white-space: nowrap;
            }

            .form-items div label,
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

            .form-items div label:hover,
            .form-container button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

            #tolak {
                background-color: var(--indicator2);
                border-color: var(--indicator2a);
            }

            #tolak:hover {
                background-color: var(--indicator2b);
                color: var(--color4);
            }

            .form-items label {
                font-size: 15px;
                color: var(--color2);
            }

            .form-items textarea {
                width: 625px;
                height: 120px;
                padding: 10px;
                border: 2px solid var(--color3);
                border-radius: 10px;
                resize: vertical;
                font-size: 15px;
            }
        </style>
    </head>

    <body>
        <?php include "sidebar.php"; ?>

        <div class="container">
            <div class="title">
                <h1>Verify Izin <?= $siswa['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-container">
                        <div class="form-items">
                            <label>Nama</label>
                            <input type="text" name="nama" value="<?= $siswa['nama'] ?>" readonly>
                        </div>

                        <div class="form-items">
                            <label>Kelas</label>
                            <input type="text" name="kelas" value="<?= $kelas ?>" readonly>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" value="<?= $izin['tanggal'] ?>" readonly>
                        </div>

                        <div class="form-items">
                            <label>Alasan</label>
                            <input type="text" name="alasan" value="<?= $izin['alasan'] ?>" readonly>
                        </div>
                    </div>

                    <div class="form-container" style="height:200px; align-items: start;">
                        <div class="form-items">
                            <label>View Foto</label>
                            <div>
                                <a href="#" id="view-foto-link" data-foto="<?= $encodedfoto ?>" data-mime="<?= $mime ?>"
                                    target="_blank">
                                    <label id="view-foto-label">Lihat Foto</label>
                                </a>
                            </div>
                        </div>

                        <div class="form-items">
                            <label>Deskripsi</label>
                            <textarea
                                name="deskripsi" readonly><?= ($izin['deskripsi'] == "") ? "-" : $izin['deskripsi'] ?></textarea>
                        </div>
                    </div>

                    <input name="id" value="<?= $idizin ?>" hidden>

                    <div class="form-container">
                        <button type="submit" name="aksi" value="tolak" id="tolak">Tolak</button>
                        <button type="submit" name="aksi" value="terima">Terima</button>

                    </div>
                </form>
            </div>
        </div>

        <script>
            const viewLink = document.getElementById("view-foto-link");
            const viewLabel = document.getElementById("view-foto-label");

            const base64 = viewLink.dataset.foto;
            const mime = viewLink.dataset.mime || "application/octet-stream";

            if (base64 && base64.length > 10) {

                const byteChars = atob(base64);
                const byteNums = new Array(byteChars.length);

                for (let i = 0; i < byteChars.length; i++) {
                    byteNums[i] = byteChars.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNums);

                const blob = new Blob([byteArray], { type: mime });

                const blobURL = URL.createObjectURL(blob);

                viewLink.href = blobURL;
                viewLink.style.pointerEvents = "auto";
                viewLink.style.opacity = "1";

            } else {
                viewLink.href = "#";
                viewLink.style.pointerEvents = "none";
                viewLink.style.opacity = "0.5";
                viewLabel.textContent = "Tidak ada foto";
            }

        </script>


    </body>

    </html>


    <?php
} else {
    header("Location: ../../");
}
?>