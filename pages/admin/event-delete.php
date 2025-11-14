<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['idevent']) {
    require_once "../../conn.php";

    $idevent = $_POST['idevent'];

    $stmt = $conn->prepare("DELETE FROM event WHERE id=?");
    $stmt->bind_param("i", $idevent);
    $stmt->execute();
    $stmt->close();

    header("Location: jadwal.php");
}

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['idevent']) {

    require_once "../../conn.php";

    $idevent = $_GET['idevent'];

    $stmt = $conn->prepare("SELECT nama FROM event WHERE id=?");
    $stmt->bind_param("i", $idevent);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

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
                height: 150px;

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
                <h1>Delete Event <?= $event['nama'] ?></h1>
            </div>

            <div class="content">
                <form method="POST">
                    <div class="form-container">
                        <p>*Warning <br> Event yang dihapus tidak bisa dikembalikan lagi.<br> Apakah anda yakin?</p>
                    </div>

                    <input name="idevent" value="<?= $idevent ?>" hidden>

                    <div class="form-container">
                        <button type="submit" id="confirm">Ya, Saya Yakin</button>

                        <a href="kelas.php">
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
    header("Location: jadwal.php");
}
?>