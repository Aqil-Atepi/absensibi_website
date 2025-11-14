<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../../");
    exit;
}

require_once "../../conn.php";

$nis = $_GET['nis'];

$stmt = $conn->prepare("SELECT foto, nama FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$base64 = base64_encode($data['foto']);
$src = 'data:image/jpeg;base64,' . $base64;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/main.css" rel="stylesheet">
    <title>View Foto - <?= htmlspecialchars($data['nama']) ?></title>
    <style>
        .foto-container {
            width: 70%;
            height: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
        }

        .foto-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="container">
        <div class="title">
            <h1>View Foto - <?= htmlspecialchars($data['nama']) ?></h1>
        </div>

        <?php
        if (!$data || empty($data['foto'])) {
            echo "No photo available.";
            exit;
        } else {
            ?>

            <div class="foto-container">
                <img src="<?= $src ?>" alt="Foto <?= htmlspecialchars($data['nama']) ?>">
            </div>
            <?php
        }
        ?>
    </div>
</body>

</html>