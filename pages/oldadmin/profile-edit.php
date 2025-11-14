<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../../");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/main.css" rel="stylesheet">
    <title>Edit Akun</title>
    <style>
        .content {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: baseline;
            justify-content: baseline;

            color: var(--color3);
        }

        .content form{
            width: 100%;
            height: 20%;

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
            <h1>Edit Admin</h1>
        </div>

        <div class="content">
            <form>
                <div>
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan Username">
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