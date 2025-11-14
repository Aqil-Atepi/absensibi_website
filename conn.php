<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "absensibi";

    $conn = mysqli_connect($host, $user, $pass, $db);

    if (!$conn) {
        die("Connection Error: ". mysqli_connect_error());
    }
?>