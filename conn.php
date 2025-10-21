<?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'absensibi';

    $conn = mysqli_connect($host, $username, $password, $database);

    if (!$conn) {
        echo 'Connection Failed!
                \nError Code: ' . mysqli_connect_errno() .
                '\nError Message: ' . mysqli_connect_error();
    }
?>