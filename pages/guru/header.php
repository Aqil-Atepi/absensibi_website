<?php include '../../conn.php'; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Absensi SMKBI</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <style>
    .sidebar {
      width: 220px;
      background: #fff;
      position: fixed;
      top: 0;
      bottom: 0;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
    }

    .nav-links {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 20px;
      color: #444;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.2s, color 0.2s;
    }

    .sidebar a span {
      flex: 1;
      /* Keeps text aligned left and takes remaining space */
      text-align: left;
    }

    .sidebar a img {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
      /* prevents icon from resizing */
      filter: invert(29%) sepia(52%) saturate(469%) hue-rotate(226deg) brightness(93%) contrast(91%);
      transition: filter 0.2s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #6f42c1;
      color: #fff;
    }

    .sidebar a:hover img,
    .sidebar a.active img {
      filter: brightness(0) invert(1);
    }



    .nav-links img {
      width: 100px;
      height: auto;
      display: block;
      margin: 10px auto;
    }

    .nav-links svg {
      width: 18px;
      height: 18px;
    }

    .logout {
      border-top: 1px solid #ddd;
      width: 100%;
      padding-top: 10px;
      text-align: left;
    }

    .content {
      margin-left: 240px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="nav-links">
      <img src="../../assets/images/logo_bi.png">

      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <img src="../../assets/svg/dashboard.svg" alt="Dashboard icon">
        <span>Dashboard</span>
      </a>

      <a href="siswa.php" class="<?= basename($_SERVER['PHP_SELF']) == 'siswa.php' ? 'active' : ''; ?>">
        <img src="../../assets/svg/user.svg" alt="Siswa icon">
        <span>Siswa</span>
      </a>

      <a href="izin.php" class="<?= basename($_SERVER['PHP_SELF']) == 'izin.php' ? 'active' : ''; ?>">
        <img src="../../assets/svg/mail.svg" alt="Izin icon">
        <span>Izin</span>
      </a>

      <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
        <img src="../../assets/svg/profile.svg" alt="Profile icon">
        <span>Profile</span>
      </a>
    </div>

    <div class="logout">
      <a href="../../auth/logout.php">Log Out</a>
    </div>
  </div>


  <div class="content">
</body>

</html>