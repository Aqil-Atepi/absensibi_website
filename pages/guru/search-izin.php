<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && in_array($_SESSION["role"], ['guru', 'admin'])) {
    require_once "../../conn.php";
    date_default_timezone_set('Asia/Jakarta');

    $tanggal = date('Y-m-d');
    $search  = isset($_GET['search']) ? "%{$_GET['search']}%" : "%";
    $kelas   = isset($_GET['filtersummary']) ? $_GET['filtersummary'] : '';

    // Build query dynamically
    if ($kelas === '') {
        $stmt = $conn->prepare("
            SELECT i.*, s.nama AS nama_siswa, s.kelas AS id_kelas, k.nama AS nama_kelas
            FROM izin i
            JOIN siswa s ON i.siswa = s.nis
            JOIN kelas k ON s.kelas = k.id
            WHERE i.tanggal = ?
              AND i.status = 'Diproses'
              AND s.nama LIKE ?
            ORDER BY i.waktu ASC
        ");
        $stmt->bind_param("ss", $tanggal, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT i.*, s.nama AS nama_siswa, s.kelas AS id_kelas, k.nama AS nama_kelas
            FROM izin i
            JOIN siswa s ON i.siswa = s.nis
            JOIN kelas k ON s.kelas = k.id
            WHERE i.tanggal = ?
              AND i.status = 'Diproses'
              AND s.kelas = ?
              AND s.nama LIKE ?
            ORDER BY i.waktu ASC
        ");
        $stmt->bind_param("sss", $tanggal, $kelas, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $no = 1;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $namaSiswa = htmlspecialchars($row['nama_siswa']);
            $namaKelas = htmlspecialchars($row['nama_kelas']);
            $tanggalIzin = htmlspecialchars($row['tanggal']);
            $waktuIzin = htmlspecialchars($row['waktu']);
            $alasan = htmlspecialchars($row['alasan']); // <-- from izin table
            $idIzin = $row['id'];

            echo "
                <tr>
                    <td class='table-no'>{$no}</td>
                    <td class='table-nma'>{$namaSiswa}</td>
                    <td class='table-kls'>{$namaKelas}</td>
                    <td class='table-tgi'>{$tanggalIzin} {$waktuIzin}</td>
                    <td class='table-als'>{$alasan}</td>
                    <td class='table-aks-items'>
                        <a href='izin-verify.php?id={$idIzin}'>
                            <button class='verified'><img src='../../assets/svg/edit.svg'></button>
                        </a>
                    </td>
                </tr>
            ";

            $no++;
        }

    } else {
        echo "
            <tr>
                <td colspan='6' style='text-align:center; padding:10px;'>
                    <p>Tidak ada data izin.</p>
                </td>
            </tr>
        ";
    }

} else {
    header(\"Location: ../../\");
    exit;
}
?>
