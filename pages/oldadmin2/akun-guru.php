<?php
session_start();

if (isset($_SESSION["username"])) {
    require_once "../../conn.php";

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    function getAkun($conn, $table, $search = "")
    {
        $accesstable = ['siswa', 'guru'];

        if (!in_array($table, $accesstable)) {
            throw new Exception("Invalid table name");
        }

        if (!empty($search)) {
            $stmt = $conn->prepare("SELECT * FROM $table 
            WHERE nama LIKE ? OR username LIKE ? OR nik LIKE ?");
            $keyword = "%$search%";
            $stmt->bind_param("sss", $keyword, $keyword, $keyword);
        } else {
            $stmt = $conn->prepare("SELECT * FROM $table");
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <title>Akun</title>
        <style>
            .content {
                width: 100%;
                height: auto;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                gap: 20px;
            }

            .action-container {
                width: 100%;
                height: auto;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;

                gap: 10px;
            }

            .search-items {
                flex: 3;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;

                gap: 10px;
            }

            .search-items input {
                flex: 5;

                padding: 12px;

                border: 1px solid var(--color2);
                border-radius: 10px;
            }

            .search-items button {
                flex: 1;

                border: 1px solid var(--color2);
                border-radius: 10px;

                padding: 12px;

                display: flex;
                align-items: center;
                justify-content: center;
            }

            .search-items button img {
                width: 20px;
                height: 20px;

                filter: invert(0%) sepia(9%) saturate(7471%) hue-rotate(85deg) brightness(104%) contrast(95%);
            }

            .filter-items {
                flex: 1;

                display: flex;
                flex-direction: row;
                align-items: baseline;
                justify-content: center;
            }

            .filter-items a {
                width: 100%;
                height: auto;

                padding: 12px;

                border: 1px solid var(--color2);
                border-radius: 10px;

                color: var(--color5);

                text-decoration: none;
            }

            .create-items {
                flex: 1;

                display: flex;
                align-items: center;
                justify-content: baseline;
            }

            .create-items a {
                width: 100%;
                height: auto;

                padding: 12px;

                border: 1px solid var(--color2);
                border-radius: 10px;

                background-color: var(--indicator1);

                color: var(--color3);

                text-decoration: none;
            }

            .data {
                width: 100%;
                height: 600px;

                border: 1px solid var(--color5);
                border-radius: 10px;

                background-color: var(--color2);

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;

                overflow: hidden;
            }

            .data table {
                width: 100%;

                border-collapse: collapse;
            }

            .data table thead {
                background-color: var(--color1);
                color: var(--color3);

                border-bottom: 1px solid var(--color5);
            }

            .data table th {
                text-align: left;

                padding: 10px;

                font-size: 20px;
                font-weight: bold;
                position: relative;
            }

            .data table tbody td {
                padding: 10px;
                font-size: 15px;
            }

            .data table tbody td a {
                color: var(--color1);

                text-decoration: none;
            }

            .data table tbody tr:hover {
                background-color: var(--color3);
            }

            .action-buttons {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;
                gap: 10px;
            }

            .action-buttons button {
                width: 30px;
                height: 30px;

                display: flex;
                align-items: center;
                justify-content: center;

                border: none;
                border-radius: 10px;
            }

            .action-buttons .edit {
                background-color: var(--indicator3);
            }

            .action-buttons .password {
                background-color: var(--indicator4);
            }

            .action-buttons .switch-on {
                background-color: var(--indicator1);
            }

            .action-buttons .switch-off {
                background-color: var(--indicator2);
            }

            .action-buttons .delete {
                background-color: var(--indicator5);
            }

            .action-buttons button img {
                width: 20px;
                height: 20px;

                filter: invert(100%) sepia(0%) saturate(7486%) hue-rotate(108deg) brightness(104%) contrast(104%);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Akun</h1>
            </div>

            <div class="content">
                <div class="action-container">
                    <form method="GET" class="search-items">
                        <input type="text" name="search" id="searchInput" placeholder="Cari akun..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" id="searchButton">
                            <img src="../../assets/svg/search.svg" alt="Search Icon">
                        </button>
                    </form>


                    <div class="filter-items">
                        <a href="akun.php">Guru</a>
                    </div>
                    <div class="create-items">
                        <a href="akun-create-guru.php">
                            Create
                        </a>
                    </div>
                </div>
                <div class="data">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search = isset($_GET['search']) ? $_GET['search'] : '';
                            $akun = getAkun($conn, 'guru', $search);

                            $no = 1;

                            if (!empty($akun)) {
                                $no = 1;
                                foreach ($akun as $data) {
                                    echo '<tr>
            <td>' . $no++ . '</td>
            <td>' . $data['nik'] . '</td>
            <td>' . $data['username'] . '</td>
            <td>' . $data['nama'] . '</td>
            <td>' . $data['status'] . '</td>
            <td>
                <a href="view-foto-akun-guru.php?nik=' . $data['nik'] . '">Foto</a>
            </td>
            <td class="action-buttons">
                <a href="akun-edit.php">
                    <button class="edit">
                        <img src="../../assets/svg/edit.svg" alt="Edit Icon">
                    </button>
                </a>
                <a href="akun-password.php">
                    <button class="password">
                        <img src="../../assets/svg/password.svg" alt="Password Icon">
                    </button>
                </a>
                <a href="akun-switch.php">
                    <button class="switch-on">
                        <img src="../../assets/svg/switch-on.svg" alt="Switch On Icon">
                    </button>
                </a>
                <a href="akun-delete.php">
                    <button class="delete">
                        <img src="../../assets/svg/trash.svg" alt="Password Icon">
                    </button>
                </a>
            </td>
        </tr>';
                                }
                            } else {
                                echo '<tr>
        <td colspan="7" style="text-align:center;">Tidak Dapat Menemukan Data</td>
    </tr>';
                            }
                            ?>
                        </tbody>
                        <!-- <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>01.02.3.045</td>
                            <td>01.02.3.045</td>
                            <td>Nama Guru</td>
                            <td>Aktif</td>
                            <td>Foto</td>
                            <td class="action-buttons">
                                <button class="edit">
                                    <img src="../../assets/svg/edit.svg" alt="Edit Icon">
                                </button>
                                <button class="password">
                                    <img src="../../assets/svg/password.svg" alt="Password Icon">
                                </button>
                                <button class="switch-on">
                                    <img src="../../assets/svg/switch-on.svg" alt="Switch On Icon">
                                </button>
                            </td>
                        </tr>
                    </tbody> -->
                    </table>
                </div>
            </div>
        </div>
    </body>


    </html>

    <?php
} else {
    header("Location: ../../");
}
?>