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
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <title>Scan QR</title>
    <style>
        .container {
            width: auto;
            height: 100vh;
            margin-left: 200px;
            padding: 0px 10px;

            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: baseline;
        }

        .title {
            margin: 10px 0px;
            font-size: 20px;
            font-weight: bolder;
        }

        .scanner-control {
            width: 100%;
            height: auto;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 20px;
        }

        .camera-settings {
            width: 100%;
            height: auto;
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
    <?php
    include "sidebar.php";
    ?>

    <div class="container">
        <div class="title">
            <h1>Scan QR</h1>
        </div>

        <div class="scanner-control">
            <div id="scanner"></div>
            <div id="result"></div>

            <div class="camera-settings">
                <div class="camera-selection">
                    <select id="selectedCam">
                        <option value="">Loading Cameras</option>
                    </select>
                </div>
            </div>
        </div>

        <script>
            const html5QrCode = new Html5Qrcode("scanner");
            const cameraSelect = document.getElementById("selectedCam");
            let currentCameraId = null;

            function onScanSuccess(decodedText, decodedResult) {
                document.getElementById("result").innerText = `QR Code Data: ${decodedText}`;
                console.log(`Decoded text: ${decodedText}`, decodedResult);
            }

            function onScanError(errorMessage) {
                console.warn(errorMessage);
            }

            function startCamera(cameraId) {
                const config = {
                    fps: 10,
                    aspectRatio: 1
                };

                html5QrCode.start(cameraId, config, onScanSuccess, onScanError)
                    .catch(err => console.error("Camera start error:", err));
            }

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = "";
                    devices.forEach((device, index) => {
                        const option = document.createElement("option");
                        option.value = device.id;
                        option.text = device.label || `Camera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    currentCameraId = devices[0].id;
                    cameraSelect.value = currentCameraId;
                    startCamera(currentCameraId);
                } else {
                    cameraSelect.innerHTML = "<option>No cameras found</option>";
                }
            }).catch(err => {
                console.error(err);
                cameraSelect.innerHTML = "<option>Error loading cameras</option>";
            });

            cameraSelect.addEventListener("change", (e) => {
                const newCameraId = e.target.value;
                if (newCameraId && newCameraId !== currentCameraId) {
                    html5QrCode.stop().then(() => {
                        currentCameraId = newCameraId;
                        startCamera(currentCameraId);
                    }).catch(err => console.error("Stop camera error:", err));
                }
            });
        </script>
    </div>
</body>

</html>