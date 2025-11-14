<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    $table = $_GET['table'] ?? 'siswa';
    $search = $_GET['search'] ?? '';

    $availabletables = ['siswa', 'guru'];
    if (!in_array($table, $availabletables)) {
        exit('Invalid table!');
    }

    if ($table === 'siswa') {
        $stmt = $conn->prepare("SELECT * FROM siswa WHERE nama LIKE CONCAT('%', ?, '%') OR nis LIKE CONCAT('%', ?, '%')");
    } else {
        $stmt = $conn->prepare("SELECT * FROM guru WHERE nama LIKE CONCAT('%', ?, '%') OR nik LIKE CONCAT('%', ?, '%')");
    }

    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $no = 1;
    while ($akun = $result->fetch_assoc()) {
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
                    <button class='edit'><img src='../../assets/svg/edit.svg' alt='Edit Icon'></button>
                </a>
                <a href='akun-password.php?table=$table&nomorinduk={$akun[$idField]}'>
                    <button class='password'><img src='../../assets/svg/password.svg' alt='Password Icon'></button>
                </a>";

        if ($akun["status"] === "Non-Aktif") {
            echo "<a href='akun-switch.php?table=$table&nomorinduk={$akun[$idField]}&status={$akun['status']}'>
                    <button class='switch-off'><img src='../../assets/svg/switch-off.svg' alt='Switch Off Icon'></button>
                </a>";
        } elseif ($akun["status"] === "Aktif") {
            echo "<a href='akun-switch.php?table=$table&nomorinduk={$akun[$idField]}&status={$akun['status']}'>
                    <button class='switch-on'><img src='../../assets/svg/switch-on.svg' alt='Switch On Icon'></button>
                </a>";
        }

        echo "
                <a href='akun-delete.php?table=$table&nomorinduk={$akun[$idField]}'>
                    <button class='delete'><img src='../../assets/svg/trash.svg' alt='Delete Icon'></button>
                </a>
            </td>
        </tr>";
        $no++;
    }

    if ($no === 1) {
        echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data ditemukan</td></tr>";
    }

}
?>