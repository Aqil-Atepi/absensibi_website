<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === 'guru') {
    require_once "../../conn.php";

    date_default_timezone_set('Asia/Jakarta');
    $currentkelas = $_GET['filtersummary'] ?? '';

    function getDataIzin($conn, $limit, $offset)
    {
        $stmt = $conn->prepare("SELECT * FROM izin WHERE status='Diproses' 
                LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    function getNamaSiswa($conn, $nis)
    {
        $stmt = $conn->prepare("SELECT nama FROM siswa WHERE nis = ?");
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['nama'] ?? '-';
    }

    function getNamaKelas($conn, $kelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("i", $kelas);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        return $data['nama'] ?? '-';
    }

    function getDataKelas($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM kelas");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    $limit = 10;
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    if ($currentkelas == '') {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM izin WHERE status = 'Diproses'");
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM izin WHERE kelas = ? AND status = 'Diproses'");
        $stmt->bind_param("s", $currentkelas);
    }
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $dataizin = getDataIzin($conn, $limit, $offset);

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Izin</title>
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

            .table-nma {
                width: 600px;
            }

            .table-kls {
                width: 200px;
            }

            .table-als {
                width: 250px;
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

            .table-aks-items .verified {
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
                <h1>Izin</h1>
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
                                <td class="table-nma">Nama Siswa</td>
                                <td class="table-kls">Kelas</td>
                                <td class="table-als">Alasan</td>
                                <td class="table-aks">Aksi</td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = $offset + 1;
                            if (empty($dataizin)) {
                                echo "<tr><td colspan='5' style='text-align:center;padding:10px;'>Tidak ada yang izin.</td></tr>";
                            } else {
                                foreach ($dataizin as $izin) {
                                    $siswa = getNamaSiswa($conn, $izin['siswa']);
                                    $kelas = getNamaKelas($conn, $izin['kelas']);
                                    echo "
                                        <tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-nma'>{$siswa}</td>
                                            <td class='table-kls'>{$kelas}</td>
                                            <td class='table-wkt'>{$izin['alasan']}</td>
                                            <td class='table-aks-items'>
                                                <a href='izin-verify.php?idizin={$izin['id']}'>
                                                    <button class='verified'><img src='../../assets/svg/edit.svg'></button>
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
        </div>

        <script>
            const searchBar = document.getElementById('search-bar');
            const tableBody = document.querySelector('tbody');
            const filterSelect = document.querySelector('select[name="filtersummary"]');

            function updateTable() {
                const search = searchBar.value.trim();
                const kelas = filterSelect.value;

                fetch(`search-izin.php?search=${encodeURIComponent(search)}&filtersummary=${encodeURIComponent(kelas)}`)
                    .then(response => response.text())
                    .then(data => {
                        tableBody.innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
            }

            searchBar.addEventListener('input', updateTable);
            filterSelect.addEventListener('change', updateTable);

        </script>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>