<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru') {
    require_once "../../conn.php";
    date_default_timezone_set('Asia/Jakarta');

    $tanggal = date('Y-m-d');
    $currentkelas = $_GET['filtersummary'] ?? '';

    function getDataKelas($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM kelas");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getNamaKelas($conn, $id)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        return $data['nama'];
    }

    function getSiswa($conn, $kelas, $limit, $offset)
    {
        if ($kelas == '') {
            $stmt = $conn->prepare("
                SELECT * FROM siswa
                ORDER BY nis ASC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $conn->prepare("
                SELECT * FROM siswa 
                WHERE kelas = ?
                ORDER BY nis ASC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("sii", $kelas, $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    $limit = 10;
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    if ($currentkelas == '') {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa");
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa WHERE kelas = ?");
        $stmt->bind_param("i", $currentkelas);
    }
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $datasiswa = getSiswa($conn, $currentkelas, $limit, $offset);
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Siswa</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: 700px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: start;

                gap: 10px;
            }

            /* CONTROL */
            .control {
                width: 1300px;
                height: 60px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .control input,
            .control select {
                padding-left: 10px;
                border: 3px solid;
                border-radius: 10px;
                text-align: left;
                font-size: 15px;
            }

            #search-bar {
                width: 1000px;
                height: 50px;
                background-color: var(--color2);
                border-color: var(--color3);
            }

            .control select {
                width: 300px;
                height: 50px;
                background-color: var(--color2);
                border-color: var(--color3);
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

            .table-nis {
                width: 200px;
            }

            .table-nma {
                width: 400px;
            }

            .table-fto {
                width: 225px;
            }

            .table-kls {
                width: 225px;
            }

            .table-aks {
                width: 150px;
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
                background-color: var(--indicator1);
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
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Siswa</h1>
            </div>

            <div class="content">
                <div class="control">
                    <input id="search-bar" type="text" name="searchinput" placeholder="Cari Siswa...">
                    <form method="GET">
                        <select name="filtersummary" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            <?php
                            $selected = $_GET['filtersummary'] ?? '';
                            $datakelas = getDataKelas($conn);
                            foreach ($datakelas as $kelas) {
                                $isSelected = ($kelas['id'] == $selected) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($kelas['id']) . '" ' . $isSelected . '>'
                                    . htmlspecialchars($kelas['nama']) . '</option>';
                            }
                            ?>
                        </select>
                    </form>
                </div>

                <div class="table-data">
                    <table>
                        <thead>
                            <tr>
                                <td class="table-no">No</td>
                                <td class="table-nis">NIS</td>
                                <td class="table-nma">Nama Siswa</td>
                                <td class="table-kls">Kelas</td>
                                <td class="table-fto">Foto</td>
                                <td class="table-aks">Aksi</td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = $offset + 1;
                            if (empty($datasiswa)) {
                                echo "<tr><td colspan='6' style='text-align:center;padding:10px;'>Tidak ada data yang tersedia.</td></tr>";
                            } else {
                                foreach ($datasiswa as $siswa) {
                                    $encoded = !empty($siswa['foto']) ? base64_encode($siswa['foto']) : "";
                                    $foto = empty($siswa['foto'])
                                        ? '-'
                                        : "<a href='#' class='foto-link' data-foto='$encoded'>Foto</a>";

                                    $kelassiswa = empty($siswa['kelas']) ? '-' : getNamaKelas($conn, $siswa['kelas']);

                                    echo "
                                        <tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-nis'>{$siswa['nis']}</td>
                                            <td class='table-nma'>{$siswa['nama']}</td>
                                            <td class='table-kls'>{$kelassiswa}</td>
                                            <td class='table-fto'>{$foto}</td>
                                            <td class='table-aks-items'>
                                                <a href='siswa-detail.php?nis={$siswa['nis']}'>
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
            const searchBar = document.getElementById('search-bar');
            const tableBody = document.querySelector('tbody');
            const filterSelect = document.querySelector('select[name="filtersummary"]');

            function updateTable() {
                const search = searchBar.value.trim();
                const kelas = filterSelect.value;

                fetch(`search-siswa.php?search=${encodeURIComponent(search)}&filtersummary=${encodeURIComponent(kelas)}`)
                    .then(response => response.text())
                    .then(data => {
                        tableBody.innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
            }

            searchBar.addEventListener('input', updateTable);
            filterSelect.addEventListener('change', updateTable);

            document.querySelectorAll(".foto-link").forEach(link => {
                const base64 = link.dataset.foto;
                if (base64 && base64.length > 10) {
                    const byteChars = atob(base64);
                    const byteNums = new Array(byteChars.length);
                    for (let i = 0; i < byteChars.length; i++) {
                        byteNums[i] = byteChars.charCodeAt(i);
                    }
                    const byteArray = new Uint8Array(byteNums);
                    const blob = new Blob([byteArray], { type: "image/jpeg" });
                    const blobURL = URL.createObjectURL(blob);
                    link.href = blobURL;
                    link.target = "_blank";
                } else {
                    link.textContent = "-";
                    link.style.pointerEvents = "none";
                    link.style.opacity = "0.5";
                }
            });

        </script>

    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>