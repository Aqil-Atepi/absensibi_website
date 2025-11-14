<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $_GET['idkelas']) {
    require_once "../../conn.php";

    $idkelas = $_GET["idkelas"];

    function getKelas($conn, $idkelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("s", $idkelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['nama'];
    }

    function getSiswa($conn, $idkelas, $limit, $offset)
    {
        $stmt = $conn->prepare("SELECT nama FROM siswa WHERE kelas=? LIMIT ? OFFSET ?");
        $stmt->bind_param("iii", $idkelas, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getWalikelas($conn, $idkelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM guru WHERE walikelas=?");
        $stmt->bind_param("s", $idkelas);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['nama'] : null;
    }

    $limit = 8;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM siswa WHERE kelas=?");
    $stmt->bind_param("i", $idkelas);
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $kelas = getKelas($conn, $idkelas);
    $walikelas = getWalikelas($conn, $idkelas);
    $semuasiswa = getSiswa($conn, $idkelas, $limit, $offset);

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>idkelas</title>
        <style>
            /* GENERAL */
            .content {
                width: 1300px;
                height: 700px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;

                gap: 10px;
            }

            /* FORM DETAIL */
            .detail {
                width: 1300px;
                height: 100px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;
            }

            .detail {
                position: relative;
                z-index: 1;
            }

            .table-detail {
                position: relative;
                z-index: 0;
            }


            .detail form {
                width: 1290px;
                height: 90px;

                padding: 5px;

                display: flex;
                flex-direction: row;
                align-items: start;
                justify-content: center;

                gap: 10px;
            }

            .form-container {
                width: 505px;
                height: 75px;

                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: start;
            }

            .form-container.submit {
                width: 240px;
            }

            .form-container label {
                font-size: 15px;
                color: var(--color2);
            }

            .form-container input,
            .form-container button {
                height: 50px;

                border-radius: 10px;
            }

            .form-container input {
                width: 505px;

                border: 2px solid var(--color3);

                z-index: 15;

                overflow: hidden;

                padding-left: 10px;
            }

            .form-container button {
                width: 240px;

                margin-top: 23px;

                font-size: 15px;

                color: var(--color2);

                background-color: var(--indicator1);
                border: 2px solid var(--indicator1a);
            }

            .form-container button:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

            .searchinput {
                height: auto;
            }

            .dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                background-color: var(--color2);
                border: 2px solid var(--color3);
                border-top: none;
                z-index: 9999;
                /* ✅ Bring to the very top */
                width: 100%;
                max-height: 200px;
                overflow-y: auto;
                border-radius: 10px;
            }


            .dropdown .no-result {
                color: var(--color3a);
                background-color: var(--color2);
                cursor: default;
                user-select: none;
            }


            .dropdown div {
                padding: 8px 10px;
                cursor: pointer;
            }

            .dropdown div:hover {
                background-color: #f0f0f0;
            }

            /* TABLE DETAIL */
            .table-detail {
                width: 1300px;
                height: 590px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: start;

                gap: 10px;
            }

            .table-control {
                width: 1300px;
                height: 100px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;
            }

            .table-control form {
                width: 1290px;

                padding: 5px;

                display: flex;
                flex-direction: row;
                align-items: start;
                justify-content: center;

                gap: 10px;
            }

            .form-container.searchinput {
                position: relative;
            }


            .searchinput.siswa,
            .searchinput.siswa input {
                width: 1020px;
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
                max-width: 1000px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table-no {
                width: 100px;
            }

            .table-nma {
                width: 940px;
            }

            .table-aks {
                width: 260px;
            }

            .table-aks-items {
                width: 260px;
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
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Edit Kelas <?= $kelas ?></h1>
            </div>

            <div class="content">
                <div class="detail">
                    <form method="POST" action="update-kelas.php">
                        <div class="form-container">
                            <label>Nama Kelas</label>
                            <input type="text" name="nama" placeholder="Masukkan Nama Kelas..." value="<?= $kelas ?>"
                                required>
                        </div>

                        <div class="form-container searchinput">
                            <label>Walikelas</label>
                            <input type="text" name="walikelas" id="searchinput-guru" placeholder="Masukkan Nama Guru..."
                                value="<?= $walikelas ?>">
                            <div id="dropdown-guru" class="dropdown" style="display: none;"></div>
                        </div>

                        <input type="text" name="idkelas" value="<?= $idkelas ?>" hidden>

                        <div class="form-container submit">
                            <button type="submit">Update</button>
                        </div>
                    </form>
                </div>

                <div class="table-detail">
                    <div class="table-control">
                        <form method="POST" action="add-siswa-kelas.php">
                            <div class="form-container searchinput siswa">
                                <label>Nama Siswa</label>
                                <input type="text" name="siswa" id="searchinput-siswa" placeholder="Masukkan Nama Siswa...">
                                <div id="dropdown-siswa" class="dropdown" style="display: none;"></div>
                            </div>

                            <input type="text" name="idkelas" value="<?= $idkelas ?>" hidden>

                            <div class="form-container submit">
                                <button type="submit">Add Siswa</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-data">
                        <table>
                            <thead>
                                <tr>
                                    <td class="table-no">No</td>
                                    <td class="table-nma">Nama Siswa</td>
                                    <td class="table-aks">Aksi</td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = $offset + 1;

                                if (empty($semuasiswa)) {
                                    echo "<tr>
                                                <td colspan='5' style='text-align:center; padding:10px;'>
                                                    <p>Tidak ada data yang tersedia.</p>
                                                </td>
                                            </tr>";
                                } else {
                                    foreach ($semuasiswa as $siswa) {
                                        echo "<tr>
                                                    <td class='table-no'>{$no}</td>
                                                    <td class='table-nma'>{$siswa['nama']}</td>
                                                    <td class='table-aks-items'>
                                                        <a href='remove-siswa-kelas.php?idkelas=$idkelas&siswa={$siswa['nama']}'>
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
                            <a href="?idkelas=<?= $idkelas ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        function setupAutocomplete(inputId, dropdownId, fetchUrl) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);

            function closeAllDropdowns() {
                document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none');
            }

            function showDropdownMessage(message) {
                dropdown.innerHTML = '';
                const msg = document.createElement('div');
                msg.textContent = message;
                msg.className = 'no-result';
                dropdown.appendChild(msg);
                dropdown.style.display = 'block';
            }

            function fetchResults(query) {
                fetch(fetchUrl + '?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        const results = data.slice(0, 5);

                        if (results.length === 0) {
                            showDropdownMessage('No Result');
                        } else {
                            results.forEach(name => {
                                const div = document.createElement('div');
                                div.textContent = name;
                                div.addEventListener('click', () => {
                                    input.value = name;
                                    closeAllDropdowns();
                                });
                                dropdown.appendChild(div);
                            });
                            dropdown.style.display = 'block';
                        }
                    })
                    .catch(() => {
                        showDropdownMessage('Error loading results');
                    });
            }

            input.addEventListener('input', function () {
                const query = this.value.trim();
                closeAllDropdowns();
                if (!query) {
                    showDropdownMessage('No Result');
                    return;
                }
                fetchResults(query);
            });

            input.addEventListener('focus', function () {
                const query = this.value.trim();
                closeAllDropdowns();
                if (!query) {
                    showDropdownMessage('No Result');
                } else {
                    fetchResults(query);
                }
            });

            document.addEventListener('click', function (e) {
                const isInput = e.target.closest('.form-container.searchinput');
                if (!isInput) {
                    closeAllDropdowns();
                }
            });
        }

        setupAutocomplete('searchinput-guru', 'dropdown-guru', 'get-walikelas.php');
        setupAutocomplete('searchinput-siswa', 'dropdown-siswa', 'get-siswa.php');
    </script>



    </html>

    <?php
} else {
}
?>