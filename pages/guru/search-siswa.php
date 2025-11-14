<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guru') {
    require_once "../../conn.php";

    $stmt = $conn->prepare("SELECT * FROM siswa WHERE nama LIKE CONCAT('%', ?, '%') OR nis LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $no = 1;
    while ($siswa = $result->fetch_assoc()) {
        $source = $siswa['nama'];

        $encoded = !empty($akun['foto']) ? base64_encode($akun['foto']) : "";
        $foto = empty($akun['foto'])
            ? '-'
            : "<a href='#' class='foto-link' data-foto='$encoded'>Foto</a>";

        echo "<tr>
            <td class='table-no'>{$no}</td>
            <td class='table-ni'>{$siswa[$idField]}</td>
            <td class='table-nma'>{$siswa['nama']}</td>
            <td class='table-fto'>{$foto}</td>
            <td class='table-aks-items'>
                <a href='siswa-edit.php?table=$table&nomorinduk={$siswa[$idField]}'>
                    <button class='edit'><img src='../../assets/svg/edit.svg' alt='Edit Icon'></button>
                </a>
                <a href='siswa-password.php?table=$table&nomorinduk={$siswa[$idField]}'>
                    <button class='password'><img src='../../assets/svg/password.svg' alt='Password Icon'></button>
                </a>";

        if ($siswa["status"] === "Non-Aktif") {
            echo "<a href='siswa-switch.php?table=$table&nomorinduk={$siswa[$idField]}&status={$siswa['status']}'>
                    <button class='switch-off'><img src='../../assets/svg/switch-off.svg' alt='Switch Off Icon'></button>
                </a>";
        } elseif ($siswa["status"] === "Aktif") {
            echo "<a href='siswa-switch.php?table=$table&nomorinduk={$siswa[$idField]}&status={$siswa['status']}'>
                    <button class='switch-on'><img src='../../assets/svg/switch-on.svg' alt='Switch On Icon'></button>
                </a>";
        }

        echo "
                <a href='siswa-delete.php?table=$table&nomorinduk={$siswa[$idField]}'>
                    <button class='delete'><img src='../../assets/svg/trash.svg' alt='Delete Icon'></button>
                </a>
            </td>
        </tr>";
        $no++;
    }

    if ($no === 1) {
        echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data yang tersedia</td></tr>";
    }

}
