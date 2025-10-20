<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbnm = 'absensibi';

$konn = mysqli_connect($host, $user, $pass, $dbnm);

if (!$konn) {
    die("Connection failed: " . mysqli_connect_error());
}
