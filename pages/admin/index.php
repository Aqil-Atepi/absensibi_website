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
    <title>Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style>
        .container {
            background-color: green;
            margin: 10px;
        }
    </style>
</head>

<body>
    <?php
    include "header.php";
    ?>

    <div class="container">
        <div class="brief_data">

        </div>

        <div class="detail_data">

        </div>
    </div>

</body>

</html>