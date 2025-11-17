<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru' && $_GET['nis']) {
    require_once "../../conn.php";

    $nis = $_GET["nis"];

    $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis=?");
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $datasiswa = $stmt->get_result()->fetch_assoc();

    function getNamaKelas($conn, $kelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("i", $kelas);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        return $data["nama"] ?? '-';
    }

    $kelassiswa = getNamaKelas($conn, $datasiswa["kelas"]);
    $encoded = !empty($datasiswa['foto']) ? base64_encode($datasiswa['foto']) : "";

    function getAbsensi($conn, $siswa, $limit, $offset)
    {
        $stmt = $conn->prepare("
                SELECT * FROM absensi 
                WHERE siswa = ?
                ORDER BY tanggal DESC 
                LIMIT ? OFFSET ?
            ");
        $stmt->bind_param("sii", $siswa, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    $limit = 10;
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM absensi WHERE siswa = ?");
    $stmt->bind_param("s", $datasiswa['nis']);
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $absensi = getAbsensi($conn, $datasiswa['nis'], $limit, $offset);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Detail Siswa</title>
        <style>
            .content {
                width: 1300px;
                height: 700px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: start;
                gap: 10px;
            }

            /* DETAIL */
            .detail {
                width: 1300px;
                height: 70px;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .detail input,
            .detail button {
                width: 315px;
                height: 50px;
                padding-left: 10px;
                border: 3px solid;
                border-radius: 10px;
                font-size: 15px;
            }

            .detail button {
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
                color: var(--color2);
            }

            .detail button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

            /* TABLE */
            .table-data {
                width: 1300px;
                background-color: var(--color3);
                border: 5px solid var(--color1a);
                border-radius: 10px;
                overflow: hidden;
                display: flex;
                align-items: baseline;
                justify-content: center;
            }

            .table-data table {
                width: 1300px;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .table-data table thead {
                background-color: var(--color1);
                border-bottom: 5px solid var(--color1a);
                color: var(--color2);
                font-weight: bold;
            }

            .table-data table tbody tr:hover {
                background-color: var(--color2);
            }

            .table-data table tr {
                height: 40px;
            }

            .table-data table td {
                padding-left: 15px;
                max-width: 600px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table-no {
                width: 100px;
            }

            .table-tgl {
                width: 300px;
            }

            .table-wkt {
                width: 300px;
            }

            .table-abs {
                width: 300px;
            }

            .table-aks {
                width: 300px;
            }

            .table-aks-items {
                width: 250px;
                height: 40px;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;
                gap: 10px;
            }

            .table-aks-items button {
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                border-radius: 10px;
            }

            .table-aks-items .detail {
                background-color: var(--indicator3);
            }

            .table-aks-items button img {
                width: 20px;
                height: 20px;
                filter: invert(100%) sepia(0%) saturate(7486%) hue-rotate(108deg) brightness(104%) contrast(104%);
            }

            /* PAGINATION */
            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 20px 0;
                gap: 8px;
            }

            .pagination a {
                background-color: var(--color2);
                color: var(--color1);
                padding: 8px 14px;
                border-radius: 8px;
                text-decoration: none;
                transition: 0.2s;
                font-weight: bold;
            }

            .pagination a:hover {
                background-color: var(--color1b);
                color: var(--color2);
            }

            .pagination a.active {
                background-color: var(--color1b);
                color: var(--color2);
            }
        </style>
    </head>

    <body>
        <?php include "sidebar.php"; ?>

        <div class="container">
            <div class="title">
                <h1>Detail Siswa <?= htmlspecialchars($datasiswa['nama']) ?></h1>
            </div>

            <div class="content">
                <div class="detail">
                    <input type="text" value="<?= htmlspecialchars($datasiswa['nis']) ?>" readonly>
                    <input type="text" value="<?= htmlspecialchars($datasiswa['nama']) ?>" readonly>
                    <input type="text" value="<?= htmlspecialchars($kelassiswa) ?>" readonly>
                    <button id="view-foto-button" data-foto="<?= $encoded ?>">Lihat Foto</button>
                </div>

                <div class="table-data">
                    <table>
                        <thead>
                            <tr>
                                <td class="table-no">No</td>
                                <td class="table-tgl">Tanggal Absen</td>
                                <td class="table-wkt">Waktu Absen</td>
                                <td class="table-abs">Absen</td>
                                <td class="table-aks">Aksi</td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = $offset + 1;
                            if (empty($absensi)) {
                                echo "<tr><td colspan='4' style='text-align:center;padding:10px;'>Tidak ada yang absen.</td></tr>";
                            } else {
                                foreach ($absensi as $absen) {
                                    $tanggal = new DateTime($absen["tanggal"]);
                                    $tanggalabsen = $tanggal->format('j F Y');

                                    $waktu = date('H:i', strtotime($absen['waktu']));

                                    echo "
                                        <tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-tgl'>{$tanggalabsen}</td>
                                            <td class='table-wkt'>{$waktu}</td>
                                            <td class='table-abs'>{$absen['absen']}</td>
                                            <td class='table-aks-items'>
                                                <a href='detail-absensi.php?idabsen={$absen['id']}'>
                                                    <button class='detail'><img src='../../assets/svg/edit.svg'></button>
                                                </a>
                                            </td>
                                        </tr>";
                                    $no++;
                                }
                            }
                            ?>
                        </tbody>

                    </table>
                </div>

                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <script>
            const fotoBtn = document.getElementById("view-foto-button");
            const base64 = fotoBtn.dataset.foto;

            if (base64 && base64.length > 10) {
                const byteChars = atob(base64);
                const byteNums = new Array(byteChars.length);
                for (let i = 0; i < byteChars.length; i++) {
                    byteNums[i] = byteChars.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNums);
                const blob = new Blob([byteArray], { type: "image/jpeg" });
                const blobURL = URL.createObjectURL(blob);

                fotoBtn.addEventListener("click", () => {
                    window.open(blobURL, "_blank");
                });
            } else {
                fotoBtn.textContent = "Tidak Ada Foto";
                fotoBtn.disabled = true;
                fotoBtn.style.opacity = "0.6";
                fotoBtn.style.cursor = "not-allowed";
            }
        </script>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>