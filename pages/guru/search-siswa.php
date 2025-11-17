<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guru') {
    require_once "../../conn.php";

    $search = isset($_GET['search']) ? "%{$_GET['search']}%" : "%";

    $stmt = $conn->prepare("SELECT * FROM siswa WHERE nama LIKE CONCAT('%', ?, '%') OR nis LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    function getNamaKelas($conn, $kelas)
    {
        $stmt = $conn->prepare("SELECT nama FROM kelas WHERE id=?");
        $stmt->bind_param("i", $kelas);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        return $data ? $data['nama'] : '-';
    }

    $no = 1;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $source = $row['nama'];

            $encoded = !empty($row['foto']) ? base64_encode($row['foto']) : "";
            $foto = empty($row['foto'])
                ? '-'
                : "<a href='#' class='foto-link' data-foto='$encoded'>Foto</a>";
            
            $kelas = getNamaKelas($conn, $row['kelas']);

            echo "<tr>
            <td class='table-no'>{$no}</td>
            <td class='table-ni'>{$row['nis']}</td>
            <td class='table-nma'>{$row['nama']}</td>
            <td class='table-kls'>{$kelas}</td>
            <td class='table-fto'>{$foto}</td>
            <td class='table-aks-items'>
                <a href='siswa-detail.php?nis={$row['nis']}'>
                    <button class='detail'><img src='../../assets/svg/edit.svg'></button>
                </a>
            </td>
        </tr>";
            $no++;
        }
    } else {
        echo "
            <tr>
                <td colspan='6' style='text-align:center; padding:10px;'>
                    <p>Tidak ada data yang tersedia.</p>
                </td>
            </tr>
        ";
    }

}
