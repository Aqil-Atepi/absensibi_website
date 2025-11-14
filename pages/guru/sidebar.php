<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../assets/styles/main.css" rel="stylesheet">
    <link href="../../assets/images/logo-bi.png" rel="icon">
    <style>
        .sidebar {
            width: 200px;

            height: 100%;

            position: fixed;
            top: 0;
            left: 0;

            background-color: var(--color2);

            border-right: 2px solid var(--color3);

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar a span {
            flex: 1;
            text-align: left;
        }

        .sidebar a img {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            filter: invert(30%) sepia(50%) saturate(470%) hue-rotate(230deg) brightness(95%) contrast(90%);
            transition: filter 0.2s ease-in-out;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--color1);
            color: var(--color2);
        }

        .sidebar a:hover img,
        .sidebar a.active img {
            filter: brightness(0) invert(1);
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .nav-links a {
            color: var(--color1);
        }

        .nav-links img {
            width: 100px;
            height: auto;
            display: block;
            margin: 10px auto;
        }

        .logout {
            border-top: 1px solid var(--color3);
            width: 100%;
            text-align: left;
        }

        .logout a {
            font-weight: 500;
            color: var(--color3a);
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="nav-links">
            <img src="../../assets/images/logo-bi.png" style="width: 100px; height: 100px;">

            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/dashboard.svg" alt="Dashboard Icon">
                <span>Dashboard</span>
            </a>

            <a href="absensi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'absensi.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/qr.svg" alt="Absensi Icon">
                <span>Absensi</span>
            </a>

            <a href="siswa.php" class="<?= basename($_SERVER['PHP_SELF']) == 'siswa.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/user.svg" alt="Siswa Icon">
                <span>Siswa</span>
            </a>
            
            <a href="izin.php" class="<?= basename($_SERVER['PHP_SELF']) == 'izin.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/mail.svg" alt="Jadwal Icon">
                <span>Izin</span>
            </a>

            <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/profile.svg" alt="Kelas Icon">
                <span>Profile</span>
            </a>
        </div>

        <div class="logout">
            <a href="../../auth/logout.php">Logout</a>
        </div>
    </div>
</body>

</html>