<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    function getPaginatedData($conn, $table, $limit, $offset)
    {
        $availabletables = ['siswa', 'guru'];
        if (!in_array($table, $availabletables)) {
            return [];
        }

        $nomorinduk = ($table === 'siswa') ? 'nis' : 'nik';

        $stmt = $conn->prepare("SELECT * FROM $table ORDER BY $nomorinduk ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    $table = isset($_GET['table']) && in_array($_GET['table'], ['siswa', 'guru'])
        ? $_GET['table']
        : 'siswa';

    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table");
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $akuns = getPaginatedData($conn, $table, $limit, $offset);

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Akun</title>
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
                width: 800px;
                height: 50px;
                background-color: var(--color2);
                border-color: var(--color3);
            }

            #switch-data {
                background-color: var(--color1);
                border-color: var(--color1a);
            }

            #switch-data:hover {
                background-color: var(--color1b);
                color: var(--color4);
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

            .table-ni {
                width: 250px;
            }

            .table-nma {
                width: 550px;
            }

            .table-fto {
                width: 150px;
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
                <h1>Akun <?= ucfirst($table) ?></h1>
            </div>

            <div class="content">
                <div class="table-control">
                    <input id="search-bar" type="text" name="searchinput" placeholder="Cari Akun...">
                    <button id="switch-data"><?= $table === 'guru' ? 'Guru' : 'Siswa' ?></button>

                    <a id="create-data" href="akun-create.php?table=<?= $table ?>">
                        <button>Tambah Akun Baru</button>
                    </a>
                </div>

                <div class="table-data">
                    <table>
                        <thead>
                            <tr>
                                <td class="table-no">No</td>
                                <td class="table-ni"><?= $table === 'siswa' ? 'NIS' : 'NIK' ?></td>
                                <td class="table-nma">Nama</td>
                                <td class="table-fto">Foto</td>
                                <td class="table-aks">Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $offset + 1;
                            if (empty($akuns)) {
                                echo "<tr>
                                        <td colspan='5' style='text-align:center; padding:10px;'>
                                            <p>Tidak ada data yang tersedia.</p>
                                        </td>
                                    </tr>";
                            } else {
                                foreach ($akuns as $akun) {
                                    $idField = $table === 'siswa' ? 'nis' : 'nik';
                                    $encoded = !empty($akun['foto']) ? base64_encode($akun['foto']) : "";
                                    $foto = empty($akun['foto'])
                                        ? '-'
                                        : "<a href='#' class='foto-link' data-foto='$encoded'>Foto</a>";

                                    echo "<tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-ni'>{$akun[$idField]}</td>
                                            <td class='table-nma'>{$akun['nama']}</td>
                                            <td class='table-fto'>{$foto}</td>
                                            <td class='table-aks-items'>
                                                <a href='akun-edit.php?table=$table&nomorinduk={$akun[$idField]}'>
                                                    <button class='edit'><img src='../../assets/svg/edit.svg'></button>
                                                </a>
                                                <a href='akun-password.php?table=$table&nomorinduk={$akun[$idField]}'>
                                                    <button class='password'><img src='../../assets/svg/password.svg'></button>
                                                </a>";

                                    if ($akun["status"] === "Non-Aktif") {
                                        echo "<a href='akun-switch.php?table=$table&nomorinduk={$akun[$idField]}&status={$akun['status']}'>
                                                <button class='switch-off'><img src='../../assets/svg/switch-off.svg'></button>
                                              </a>";
                                    } elseif ($akun["status"] === "Aktif") {
                                        echo "<a href='akun-switch.php?table=$table&nomorinduk={$akun[$idField]}&status={$akun['status']}'>
                                                <button class='switch-on'><img src='../../assets/svg/switch-on.svg'></button>
                                              </a>";
                                    }

                                    echo "<a href='akun-delete.php?table=$table&nomorinduk={$akun[$idField]}'>
                                                <button class='delete'><img src='../../assets/svg/trash.svg'></button>
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
                        <a href="?table=<?= $table ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <script>
            const searchBar = document.getElementById('search-bar');
            const tableBody = document.querySelector('tbody');
            const switchbtn = document.getElementById('switch-data');
            let state = new URLSearchParams(window.location.search).get('table') === 'guru' ? 1 : 0;

            switchbtn.textContent = state ? 'Guru' : 'Siswa';

            switchbtn.addEventListener('click', () => {
                state = state === 0 ? 1 : 0;
                const nextTable = state === 0 ? 'siswa' : 'guru';
                window.location.href = `?table=${nextTable}`;
            });

            searchBar.addEventListener('input', () => {
                const search = searchBar.value;
                const table = state === 0 ? 'siswa' : 'guru';
                fetch(`search-akun.php?table=${table}&search=${encodeURIComponent(search)}`)
                    .then(response => response.text())
                    .then(data => {
                        tableBody.innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
            });

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