<?php

session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";
    $q = isset($_GET['q']) ? "%" . $_GET['q'] . "%" : "";

    $stmt = $conn->prepare("SELECT nama FROM siswa WHERE nama LIKE ? LIMIT 5");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $result = $stmt->get_result();

    $names = [];
    while ($row = $result->fetch_assoc()) {
        $names[] = $row['nama'];
    }

    echo json_encode($names);
}


?>