<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    require_once "../../conn.php";

    $search = $_GET['search'] ?? '';

    function getKelas($conn, $search)
    {
        $stmt = $conn->prepare("SELECT * FROM kelas WHERE nama LIKE CONCAT('%', ?, '%')");
        $stmt->bind_param("s", $search);
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
        return $result->fetch_assoc();
    }

    $kelasList = getKelas($conn, $search);
    $no = 1;

    if (empty($kelasList)) {
        echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data ditemukan</td></tr>";
    } else {
        foreach ($kelasList as $kelas) {
            $siswa = getSiswa($conn, $kelas['nama']);
            $walikelasData = getWalikelas($conn, $kelas['nama']);
            $walikelas = $walikelasData ? $walikelasData['nama'] : '-';

            echo "<tr>
                <td class='table-no'>{$no}</td>
                <td class='table-nma'>{$kelas['nama']}</td>
                <td class='table-wkl'>{$walikelas}</td>
                <td class='table-jsw'>{$siswa}</td>
                <td class='table-aks-items'>
                    <a href='#'>
                        <button class='edit'><img src='../../assets/svg/edit.svg' alt='Edit Icon'></button>
                    </a>";

            if ($kelas["status"] === "Non-Aktif") {
                echo "<a href='#'>
                    <button class='switch-off'><img src='../../assets/svg/switch-off.svg' alt='Switch Off Icon'></button>
                  </a>";
            } elseif ($kelas["status"] === "Aktif") {
                echo "<a href='#'>
                    <button class='switch-on'><img src='../../assets/svg/switch-on.svg' alt='Switch On Icon'></button>
                  </a>";
            }

            echo "<a href='#'>
                <button class='delete'><img src='../../assets/svg/trash.svg' alt='Delete Icon'></button>
              </a>
            </td>
        </tr>";
            $no++;
        }
    }
}

?>