<?php
session_start();

if (isset($_SESSION["id"]) && isset($_SESSION["role"]) && in_array($_SESSION["role"], ['guru', 'admin'])) {
    require_once "../../conn.php";
    date_default_timezone_set('Asia/Jakarta');

    $tanggal = date('Y-m-d');
    $search = isset($_GET['search']) ? "%{$_GET['search']}%" : "%";
    $kelas = isset($_GET['filtersummary']) ? $_GET['filtersummary'] : '';

    // Build query dynamically
    if ($kelas === '') {
        $stmt = $conn->prepare("
            SELECT a.*, s.nama AS nama_siswa, s.kelas AS id_kelas, k.nama AS nama_kelas
            FROM absensi a
            JOIN siswa s ON a.siswa = s.nis
            JOIN kelas k ON s.kelas = k.id
            WHERE a.tanggal = ? 
              AND a.status = 'Diproses' 
              AND s.nama LIKE ?
            ORDER BY a.waktu ASC
        ");
        $stmt->bind_param("ss", $tanggal, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT a.*, s.nama AS nama_siswa, s.kelas AS id_kelas, k.nama AS nama_kelas
            FROM absensi a
            JOIN siswa s ON a.siswa = s.nis
            JOIN kelas k ON s.kelas = k.id
            WHERE a.tanggal = ? 
              AND a.status = 'Diproses' 
              AND s.kelas = ?
              AND s.nama LIKE ?
            ORDER BY a.waktu ASC
        ");
        $stmt->bind_param("sss", $tanggal, $kelas, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $no = 1;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
                <tr>
                    <td class='table-no'>{$no}</td>
                    <td class='table-nma'>" . htmlspecialchars($row['nama_siswa']) . "</td>
                    <td class='table-kls'>" . htmlspecialchars($row['nama_kelas']) . "</td>
                    <td class='table-wkt'>" . htmlspecialchars($row['waktu']) . "</td>
                    <td class='table-aks-items'>
                        <a href='#'>
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
                <td colspan='5' style='text-align:center; padding:10px;'>
                    <p>Tidak ada data absensi.</p>
                </td>
            </tr>
        ";
    }

} else {
    header("Location: ../../");
    exit;
}
?>