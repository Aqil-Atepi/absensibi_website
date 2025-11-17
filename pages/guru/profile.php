<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === '') {
    require_once "../../conn.php";

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
                <h1>Edit Akun <?= $akun['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-container">
                        <div class="form-items single">
                            <label><?= ($table == "siswa") ? 'NIS' : 'NIK' ?></label>
                            <input type="text" name="nomorinduk"
                                placeholder="Masukkan <?= ($table == "siswa") ? 'NIS' : 'NIK' ?>"
                                value="<?= $akun['nomorinduk'] ?>" readonly>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Nama</label>
                            <input type="text" name="nama" placeholder="Masukkan Nama..." value="<?= $akun['nama'] ?>"
                                required>
                        </div>

                        <div class="form-items">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="Masukkan Username..."
                                value="<?= $akun['username'] ?>">
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Foto</label>
                            <div>
                                <input type="file" id="file-input" name="foto" accept="image/*">
                                <label for="file-input" id="file-button">Pilih Foto</label>
                            </div>
                        </div>

                        <div class="form-items">
                            <label>View Foto</label>
                            <div>
                                <a href="#" id="view-foto-link" data-foto="<?= $encodedfoto ?>" target="_blank">
                                    <label id="view-foto-label">Lihat Foto</label>
                                </a>
                            </div>
                        </div>
                    </div>

                    <input name="table" value="<?= $table ?>" hidden>

                    <div class="form-container">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const viewLink = document.getElementById("view-foto-link");
            const viewLabel = document.getElementById("view-foto-label");
            const base64 = viewLink.dataset.foto;

            if (base64 && base64.length > 10) {
                const byteChars = atob(base64);
                const byteNums = new Array(byteChars.length);
                for (let i = 0; i < byteChars.length; i++) byteNums[i] = byteChars.charCodeAt(i);
                const byteArray = new Uint8Array(byteNums);
                const blob = new Blob([byteArray], { type: "image/jpeg" });
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

            fileInput.addEventListener("change", function () {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    fileButton.textContent = file.name;

                    const fileURL = URL.createObjectURL(file);
                    viewLink.href = fileURL;
                    viewLabel.textContent = "Lihat Foto";

                    viewLink.style.pointerEvents = "auto";
                    viewLink.style.opacity = "1";
                } else {
                    viewLink.href = "#";
                    viewLabel.textContent = "Lihat Foto";
                    viewLink.style.pointerEvents = "none";
                    viewLink.style.opacity = "0.6";
                }
            });
        </script>

    </body>

    <?php
} else {
    header("Location: ../../");
}
?>