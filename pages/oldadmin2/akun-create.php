<?php
session_start();

if (isset($_SESSION["username"])) {
    require_once "../../conn.php";
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../assets/styles/main.css" rel="stylesheet">
        <title>Create Akun</title>
        <style>
            .content {
                width: 100%;
                height: 100%;

                display: flex;
                align-items: baseline;
                justify-content: baseline;

                color: var(--color3);
            }

            .content form {
                width: 100%;
                height: 30%;

                background-color: var(--color1);
                border-radius: 10px;
                padding: 20px;

                display: flex;
                flex-wrap: wrap;
            }

            .content form div {
                width: 49%;
                height: auto;

                margin: 5px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: baseline;
            }

            .content form div input {
                width: 97%;
                height: auto;

                padding: 10px;

                font-size: 15px;

                border: none;
                border-radius: 10px;
            }

            .content form div input {
                width: 97%;
                height: auto;

                padding: 10px;

                font-size: 15px;

                border: none;
                border-radius: 10px;
            }

            .content form div button {
                width: 100%;
                height: auto;

                padding: 10px;

                font-size: 20px;
                font-weight: bold;

                border: none;
                border-radius: 10px;

                background-color: var(--indicator1);

                color: var(--color3);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Create Akun</h1>
            </div>

            <div class="content">
                <form method="POST">
                    <div>
                        <label>NIS</label>
                        <input type="text" name="nis" placeholder="Masukkan NIS" required>
                    </div>
                    <div>
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Masukkan Username" required>
                    </div>
                    <div>
                        <label>Nama</label>
                        <input type="text" name="nama" placeholder="Masukkan Nama" required>
                    </div>
                    <div>
                        <label>Foto</label>
                        <input type="file" nama="foto">
                    </div>
                    <div>
                        <button type="submit">Submit</button>
                    </div>
                    <div>
                        <button>View Foto</button>
                    </div>
                </form>
            </div>
        </div>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>