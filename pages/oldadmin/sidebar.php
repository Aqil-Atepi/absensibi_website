<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../assets/styles/main.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 200px;

            height: 100%;

            position: fixed;
            top: 0;
            left: 0;

            background-color: var(--color3);

            border-right: 2px solid var(--color2);

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
            color: var(--color3);
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
            border-top: 1px solid var(--color2);
            width: 100%;
            text-align: left;
        }

        .logout a {
            font-weight: 500;
            color: var(--color5);
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

            <a href="akun.php" class="<?= basename($_SERVER['PHP_SELF']) == 'akun.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/user.svg" alt="Akun Icon">
                <span>Akun</span>
            </a>
            
            <a href="jadwal.php" class="<?= basename($_SERVER['PHP_SELF']) == 'jadwal.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/calendar.svg" alt="Jadwal Icon">
                <span>Jadwal</span>
            </a>

            <a href="kelas.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kelas.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/class.svg" alt="Kelas Icon">
                <span>Kelas</span>
            </a>

            <a href="scan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'scan.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/qr.svg" alt="Scan QR Icon">
                <span>Scan QR</span>
            </a>

            <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                <img src="../../assets/svg/profile.svg" alt="Profile Icon">
                <span>Profile</span>
            </a>
        </div>

        <div class="logout">
            <a href="../../auth/logout.php">Logout</a>
        </div>
    </div>
</body>

</html>