<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    function getPaginatedData($conn, $limit, $offset)
    {
        $stmt = $conn->prepare("SELECT * FROM event ORDER BY tanggalmulai ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getJadwal($conn, $id)
    {
        $stmt = $conn->prepare("SELECT * FROM jadwal WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    $limit = 7;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM event");
    $stmt->execute();
    $totalRows = $stmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    $events = getPaginatedData($conn, $limit, $offset);

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <title>Jadwal</title>
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

            /* WEEKDAY CONTROL */
            .week-control {
                width: 1300px;
                height: 180px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: start;

                padding-top: 10px;

                color: var(--color2);
            }

            .week-control h2 {
                font-size: 20px;
            }

            .week-items {
                width: 1300px;
                height: 100px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .week-items button {
                width: 172px;
                height: 90px;

                border-radius: 10px;

                color: var(--color2);
                font-size: 20px;
                font-weight: bold;
            }

            .week-items button.masuk {
                background-color: var(--indicator1);
                border: 2px solid var(--indicator1a);
            }

            .week-items button.masuk:hover {
                background-color: var(--indicator1b);
                color: var(--color4);
            }

            .week-items button.libur {
                background-color: var(--color3);
                border: 2px solid var(--color3a);
                color: var(--color4);
            }

            .week-items button.libur:hover {
                background-color: var(--color3b);
                color: var(--color2);
            }

            .week-info {
                width: 1300px;
                height: 25px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;

                gap: 20px;
            }

            .week-info-items {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: start;

                gap: 10px;
            }

            .week-info-symbol {
                width: 15px;
                height: 15px;

                border-radius: 2px;
            }

            .masuk {
                background-color: var(--indicator1);
            }

            .libur {
                background-color: var(--color3);
            }

            /* EVENT */
            .event-data {
                width: 1300px;
                height: 510px;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: baseline;

                gap: 10px;
            }

            .event-control {
                width: 1300px;
                height: 60px;

                background-color: var(--color1);

                border: 5px solid var(--color1a);
                border-radius: 10px;

                display: flex;
                flex-direction: flex;
                align-items: center;
                justify-content: space-between;

                padding: 0 10px;
            }

            .event-control h2 {
                font-size: 20px;
                color: var(--color2);
            }

            .event-control button {
                width: 200px;
                height: 40px;

                background-color: var(--indicator1);
                border: 2px solid var(--indicator1a);
                border-radius: 10px;

                font-size: 15px;
                color: var(--color2);
            }

            .event-control button:hover {
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

            .table-nma {
                width: 300px;
            }

            .table-tgl {
                width: 350px;
            }

            .table-sts {
                width: 150px;
            }

            .table-wkt {
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
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Jadwal Sekolah</h1>
            </div>

            <div class="content">
                <div class="week-control">
                    <h2>Jadwal Harian Sekolah</h2>
                    <div class="week-items">
                        <?php
                        $totalhari = 7;

                        for ($i = 1; $i <= $totalhari; $i++) {
                            $jadwal = getJadwal($conn, $i);

                            echo "<a href='jadwal-edit.php?hari={$jadwal['id']}'>
                                    <button class='" . (($jadwal['status'] == 'Masuk') ? 'masuk' : 'libur') . "'>{$jadwal['hari']}</button>
                                </a>";
                        }
                        ?>
                    </div>
                    <div class="week-info">
                        <div class="week-info-items">
                            <div class="week-info-symbol masuk"></div>
                            <span>Masuk Sekolah Biasa</span>
                        </div>

                        <div class="week-info-items">
                            <div class="week-info-symbol libur"></div>
                            <span>Kosong (Libur Sekolah)</span>
                        </div>
                    </div>
                </div>

                <div class="event-data">
                    <div class="event-control">
                        <h2>Event Sekolah</h2>
                        <a href="event-create.php">
                            <button>Tambah Event</button>
                        </a>
                    </div>

                    <div class="table-data">
                        <table>
                            <thead>
                                <tr>
                                    <td class="table-no">No</td>
                                    <td class="table-nma">Nama Event</td>
                                    <td class="table-tgl">Tanggal Event</td>
                                    <td class="table-sts">Status</td>
                                    <td class="table-wkt">Waktu</td>
                                    <td class="table-aks">Aksi</td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = $offset + 1;
                                if (empty($events)) {
                                    echo "<tr>
                                        <td colspan='6' style='text-align:center; padding:10px;'>
                                            <p>Tidak ada data yang tersedia.</p>
                                        </td>
                                    </tr>";
                                } else {
                                    foreach ($events as $event) {
                                        $tglmulai = new DateTime($event['tanggalmulai']);
                                        $tglselesai = new DateTime($event['tanggalselesai']);

                                        if (!empty($event['waktu']))
                                            $waktu = date('H:i', strtotime($event['waktu']));
                                        else
                                            $waktu = '-';

                                        if ($tglmulai->format('j F Y') === $tglselesai->format('j F Y')) {
                                            $tanggal = $tglselesai->format('j F Y');
                                        } elseif ($tglmulai->format('F Y') === $tglselesai->format('F Y')) {
                                            $tanggal = $tglmulai->format('j') . ' - ' . $tglselesai->format('j F Y');
                                        } elseif ($tglmulai->format('F') === $tglselesai->format('F')) {
                                            $tanggal = $tglmulai->format('j F') . ' - ' . $tglselesai->format('j F Y');
                                        } else {
                                            $tanggal = $tglmulai->format('j F Y') . ' - ' . $tglselesai->format('j F Y');
                                        }

                                        echo "<tr>
                                            <td class='table-no'>{$no}</td>
                                            <td class='table-nma'>{$event['nama']}</td>
                                            <td class='table-tgl'>{$tanggal}</td>
                                            <td class='table-sts'>{$event['status']}</td>
                                            <td class='table-wkt'>{$waktu}</td>
                                            <td class='table-aks-items'>
                                                <a href='event-edit.php?idevent={$event['id']}'>
                                                    <button class='edit'><img src='../../assets/svg/edit.svg'></button>
                                                </a>
                                                <a href='event-delete.php?idevent={$event['id']}'>
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
        </div>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>