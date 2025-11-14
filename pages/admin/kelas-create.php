<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_GET['request'])) {
    require_once "../../conn.php";

    $request = $_GET["request"];

    if ($request == "create") {
        $baseName = "Kelas Baru";
        $i = 1;
        $newName = $baseName . " " . $i;
        
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM kelas WHERE nama = ?");
        do {
            $stmt->bind_param("s", $newName);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $exists = $row['total'] > 0;

            if ($exists) {
                $i++;
                $newName = $baseName . " " . $i;
            }
        } while ($exists);

        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO kelas (`nama`) VALUES (?)");
        $stmt->bind_param("s", $newName);
        $stmt->execute();
        $stmt->close();

        header("Location: kelas.php");
        exit();
    }

} else {
    header("Location: ../../");
    exit();
}
?>
