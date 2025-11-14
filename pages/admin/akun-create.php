<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "../../conn.php";

    $table = $_POST["table"];
    $nomorinduk = $_POST["nomorinduk"];
    $nama = $_POST["nama"];

    if ($username = $_POST["username"] == "")
        $username = $_POST["nomorinduk"];
    else
        $username = $_POST["username"];

    $password = $_POST["password"];

    $foto = null;
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES["foto"]["tmp_name"]);
    }

    if ($table == "siswa") {
        $stmt = $conn->prepare("
        INSERT INTO siswa (nis, username, password, nama, foto)
        VALUES (?, ?, ?, ?, ?)
    ");
    } elseif ($table == "guru") {
        $stmt = $conn->prepare("
        INSERT INTO guru (nik, username, password, nama, foto)
        VALUES (?, ?, ?, ?, ?)
    ");
    }

    $null = null;

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt->bind_param("ssssb", $nomorinduk, $username, $hashed, $nama, $null);

    if ($foto !== null) {
        $stmt->send_long_data(4, $foto);
    }


    $stmt->execute();
    $stmt->close();

    if ($table == 'siswa')
        header("Location: akun.php");
    elseif ($table == 'guru')
        header("Location: akun.php?table=guru");
}

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['table']) {

    $inputTable = $_GET['table'];

    $availableTable = ['siswa', 'guru'];

    if (!in_array($inputTable, $availableTable))
        $table = 'No Table Found!';
    else
        $table = $inputTable;

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
                border: 2px solid var(--indicator1a);
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
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Create Akun <?= ucfirst($table) ?> Baru</h1>
            </div>

            <div class="content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-container">
                        <div class="form-items">
                            <label><?= ($table == "siswa") ? 'NIS' : 'NIK' ?></label>
                            <input type="text" name="nomorinduk" placeholder="Masukkan NIS..." required>
                        </div>

                        <div class="form-items">
                            <label>Nama</label>
                            <input type="text" name="nama" placeholder="Masukkan Nama..." required>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="Masukkan Username...">
                        </div>

                        <div class="form-items">
                            <label>Password</label>
                            <input type="text" name="password" placeholder="Masukkan Password..." required>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-items">
                            <label>Foto</label>
                            <div>
                                <input type="file"  id="file-input" name="foto" accept="image/*">
                                <label for="file-input" id="file-button">Pilih Foto</label>
                            </div>

                        </div>

                        <div class="form-items">
                            <label>View Foto</label>
                            <div>
                                <a href="#" id="view-foto-link" target="_blank">
                                    <label id="view-foto-label">Tidak Ada Foto</label>
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
            const fileInput = document.getElementById("file-input");
            const fileButton = document.getElementById("file-button");
            const viewLink = document.getElementById("view-foto-link");
            const viewLabel = document.getElementById("view-foto-label");

            viewLink.style.pointerEvents = "none";
            viewLink.style.opacity = "0.6";

            viewLink.addEventListener("click", function (e) {
                if (viewLink.style.pointerEvents === "none") {
                    e.preventDefault();
                }
            });

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
                    viewLabel.textContent = "Tidak Ada Foto";
                    viewLink.style.pointerEvents = "none";
                    viewLink.style.opacity = "0.6";
                }
            });
        </script>



    </body>

    </html>

    <?php
} else {
    header("Location: akun.php");
}
?>