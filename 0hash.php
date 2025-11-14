<?php
require_once "conn.php";

$stmt = $conn->prepare("SELECT * FROM administratif WHERE username='admin'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $nik = $row["username"];
    $pass = $row["password"];

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE administratif SET password=? WHERE username=?");
    $stmt->bind_param("ss", $hashed, $nik);
    $stmt->execute();

    echo "Password Hashed " . $nik;

?>