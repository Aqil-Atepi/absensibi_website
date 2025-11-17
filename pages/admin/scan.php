<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../");
    exit();
}

require_once "../../conn.php";
date_default_timezone_set('Asia/Jakarta');

// ==================== HANDLE AJAX POST ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $siswa = $data['siswa'] ?? '';
    $tanggalStr = $data['tanggal'] ?? '';
    $waktuStr = $data['waktu'] ?? '';
    $photoData = $data['photo'] ?? '';
    $status = "Diproses";

    if (!$siswa || !$tanggalStr || !$waktuStr || !$photoData) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Convert date/time
    $waktuabsen = DateTime::createFromFormat('Y-m-d H:i', "$tanggalStr $waktuStr");
    $waktusekarang = new DateTime("now");
    $waktusekarang = DateTime::createFromFormat('Y-m-d H:i', $waktusekarang->format('Y-m-d H:i'));

    // Allow 2 minutes tolerance
    $diff = abs($waktusekarang->getTimestamp() - $waktuabsen->getTimestamp());
    if ($diff > 120) {
        echo json_encode(['success' => false, 'message' => 'Data Absensi Salah!']);
        exit();
    }

    // Save photo
    $photoData = preg_replace('#^data:image/\w+;base64,#i', '', $photoData);
    $photoData = str_replace(' ', '+', $photoData);
    $imageData = base64_decode($photoData);

    // Get siswa info
    $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis=?");
    $stmt->bind_param("s", $siswa);
    $stmt->execute();
    $siswaabsen = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$siswaabsen) {
        echo json_encode(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        exit();
    }

    // Insert attendance
    $stmt = $conn->prepare("INSERT INTO absensi (siswa, kelas, tanggal, waktu, status, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sissss",
        $siswaabsen['nis'],
        $siswaabsen['kelas'],
        $tanggalStr,
        $waktuStr,
        $status,
        $imageData
    );
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Absensi berhasil disimpan']);
    exit();
}

// ==================== SHOW SCANNER PAGE ====================
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/main.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <title>Scan QR</title>
    <style>
        .container {
            width: auto;
            height: 100vh;
            margin-left: 200px;
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: baseline;
        }

        .title {
            margin: 10px 0;
            font-size: 20px;
            font-weight: bolder;
        }

        .scanner-control {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .camera-settings {
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .camera-settings select {
            width: 300px;
            padding: 10px;
            border-radius: 10px;
            background: var(--color2);
            font-size: 12px;
        }

        #scanner {
            width: 600px;
            height: 600px;
            background: url('../../assets/images/cam-placeholder.png') no-repeat center;
            background-size: cover;
            border-radius: 10px;
            overflow: hidden;
        }

        #scanner video {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="container">
        <div class="title">
            <h1>Scan QR</h1>
        </div>
        <div class="scanner-control">
            <div id="scanner"></div>
            <div class="camera-settings">
                <select id="selectedCam">
                    <option>Loading Cameras</option>
                </select>
            </div>
        </div>
        <canvas id="captureCanvas" style="display:none;"></canvas>

        <script>
            const html5QrCode = new Html5Qrcode("scanner");
            let hasScanned = false;

            function onScanSuccess(decodedText) {
                if (hasScanned) return;
                hasScanned = true;

                console.log("QR scanned:", decodedText);

                try {
                    const url = new URL(decodedText);
                    const siswa = url.searchParams.get("siswa");
                    const tanggal = url.searchParams.get("tanggal");
                    const waktu = url.searchParams.get("waktu");

                    if (!siswa || !tanggal || !waktu) throw "Invalid QR data";

                    // Capture photo
                    const video = document.querySelector("#scanner video");
                    if (!video) { alert("Video not ready"); return; }

                    const canvas = document.getElementById("captureCanvas");
                    const ctx = canvas.getContext("2d");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const photoData = canvas.toDataURL("image/jpeg", 0.9);

                    // Send data
                    fetch(window.location.href, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ siswa, tanggal, waktu, photo: photoData })
                    })
                        .then(res => res.json())
                        .then(res => {
                            alert(res.message);
                            location.reload();
                        })
                        .catch(err => { console.error(err); hasScanned = false; });
                } catch (e) {
                    alert("QR Code tidak valid");
                    hasScanned = false;
                }
            }

            function onScanError(errorMessage) {
                console.warn(errorMessage);
            }

            // Start camera
            const cameraSelect = document.getElementById("selectedCam");
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = "";
                    devices.forEach((device, i) => {
                        const option = document.createElement("option");
                        option.value = device.id;
                        option.text = device.label || `Camera ${i + 1}`;
                        cameraSelect.appendChild(option);
                    });
                    html5QrCode.start(devices[0].id, { fps: 10, aspectRatio: 1 }, onScanSuccess, onScanError);
                } else {
                    cameraSelect.innerHTML = "<option>No cameras found</option>";
                }
            });

            cameraSelect.addEventListener("change", e => {
                const newCam = e.target.value;
                html5QrCode.stop().then(() => html5QrCode.start(newCam, { fps: 10, aspectRatio: 1 }, onScanSuccess, onScanError))
                    .catch(err => console.error(err));
            });
        </script>
    </div>
</body>

</html>