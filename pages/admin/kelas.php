<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    function getPaginatedData($conn, $limit, $offset)
    {
        $stmt = $conn->prepare("SELECT * FROM kelas ORDER BY nama ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getSiswa($conn, $kelas)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa WHERE kelas=?");
        $stmt->bind_param("s", $kelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    function getWalikelas($conn, $kelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM guru WHERE walikelas=?");
        $stmt->bind_param("s", $kelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['nama'] : '-';
    }


    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM kelas");
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $semuakelas = getPaginatedData($conn, $limit, $offset);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Kelas</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            /* TABLE */
            .table-control {
                width: 1300px;
                height: 60px;
                display: flex;
                flex-direction: row;
                align-items: baseline;
                justify-content: baseline;
                gap: 10px;
            }

            .table-control input,
            .table-control button {
                padding-left: 10px;
                border: 3px solid;
                border-radius: 10px;
                text-align: left;
                font-size: 15px;
            }

            .table-control button {
                width: 240px;
                height: 50px;
                color: var(--color2);
            }

            #search-bar {
                width: 1050px;
                height: 50px;
                background-color: var(--color2);
                border-color: var(--color3);
            }

            #create-data button {
                background-color: var(--indicator1);
                border-color: var(--indicator1a);
            }

            #create-data button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

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
                width: 300px;
            }

            .table-wkl {
                width: 400px;
            }

            .table-jsw {
                width: 250px;
            }

            .table-aks {
                width: 250px;
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

            .table-aks-items .edit {
                background-color: var(--indicator3);
            }

            .table-aks-items .password {
                background-color: var(--indicator4);
            }

            .table-aks-items .switch-on {
                background-color: var(--indicator1);
            }

            .table-aks-items .switch-off {
                background-color: var(--indicator2);
            }

            .table-aks-items .delete {
                background-color: var(--indicator2b);
            }

            .table-aks-items button img {
                width: 20px;
                height: 20px;
                filter: invert(100%) sepia(0%) saturate(7486%) hue-rotate(108deg) brightness(104%) contrast(104%);
            }

            .foto-link {
                text-decoration: none;
                color: var(--color1);
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
                <h1>Kelas</h1>
            </div>

            <div class="content">
                <div class="table-control">
                    <input id="search-bar" type="text" name="searchinput" placeholder="Cari Kelas...">

                    <a id="create-data" href="kelas-create.php?request=create">
                        <button>Tambah Kelas Baru</button>
                    </a>
                </div>

                <div class="table-data">
                    <table>
                        <thead>
                            <tr>
                                <td class="table-no">No</td>
                                <td class="table-nma">Nama</td>
                                <td class="table-wkl">Walikelas</td>
                                <td class="table-jsw">Jumlah Siswa</td>
                                <td class="table-aks">Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $no = $offset + 1;
                            if (empty($semuakelas)) {
                                echo "<tr>
                                        <td colspan='5' style='text-align:center; padding:10px;'>
                                            <p>Tidak ada data yang tersedia.</p>
                                        </td>
                                    </tr>";
                            } else {
                                foreach ($semuakelas as $kelas) {
                                    $siswa = getSiswa($conn, $kelas['id']);
                                    $walikelas = getWalikelas($conn, $kelas['id']);

                                    if ($siswa == 0)
                                        $siswa = '-';

                                    echo "<tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-nma'>{$kelas['nama']}</td>
                                            <td class='table-wkl'>{$walikelas}</td>
                                            <td class='table-jsw'>{$siswa}</td>
                                            <td class='table-aks-items'>
                                                <a href='kelas-edit.php?idkelas={$kelas['id']}'>
                                                    <button class='edit'><img src='../../assets/svg/edit.svg' alt='Edit Icon'></button>
                                                </a>
                                                <a href='kelas-delete.php?idkelas={$kelas['id']}&walikelas=$walikelas&siswa=$siswa'>
                                                    <button class='delete'><img src='../../assets/svg/trash.svg' alt='Delete Icon'></button>
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

            searchBar.addEventListener('input', () => {
                const search = searchBar.value;
                fetch(`search-kelas.php?search=${encodeURIComponent(search)}`)
                    .then(response => response.text())
                    .then(data => {
                        tableBody.innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
            });
        </script>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>