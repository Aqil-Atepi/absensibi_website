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

    $inputTable = $_GET['table'];
    $nomorinduk = $_GET['nomorinduk'];
    $availableTable = ['siswa', 'guru'];

    if (!in_array($inputTable, $availableTable))
        $table = 'No Table Found!';
    else
        $table = $inputTable;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $newPassword = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($confirmPassword != $newPassword) {
            header("Location: akun-password.php?table=$table&nomorinduk=$nomorinduk&error=Password tidak sama dengan Konfirmasi Password");
        } elseif ($confirmPassword == $newPassword) {
            if ($table == 'siswa') {
                $stmt = $conn->prepare("UPDATE siswa SET password=? WHERE nis=?");
                $stmt->bind_param("ss", $hashed, $nomorinduk);
                $stmt->execute();
                $stmt->close();

                header("Location: akun.php");
            } elseif ($table == 'guru') {
                $stmt = $conn->prepare("UPDATE guru SET password=? WHERE nik=?");
                $stmt->bind_param("ss", $hashed, $nomorinduk);
                $stmt->execute();
                $stmt->close();

                header("Location: akun.php?table=guru");
            }
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
                align-items: center;
                justify-content: baseline;
            }

            /* FORM */
            .content form {
                width: 1300px;
                height: 200px;
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

            .form-items label {
                font-size: 15px;
                color: var(--color2);
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

            .form-container button {
                width: 1270px;
                height: 50px;
                border-radius: 10px;
                font-size: 20px;
                color: var(--color2);
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
            }

            .form-container button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }
        </style>
    </head>

    <body>
        <?php include "sidebar.php"; ?>

        <div class="container">
            <div class="title">
                <h1>Edit Password <?= $akun['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST">

                    <div class="form-container">
                        <div class="form-items">
                            <label>Password</label>
                            <input type="text" name="password" placeholder="Masukkan Password..." required>
                        </div>

                        <div class="form-items">
                            <label>Confirm Password</label>
                            <input type="text" name="confirm-password" placeholder="Masukkan Password..." required>
                        </div>
                    </div>

                    <input name="table" value="<?= $table ?>" hidden>

                    <div class="form-container">
                        <button type="submit">Submit</button>
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
