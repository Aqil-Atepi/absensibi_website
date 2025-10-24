<?php include '../../conn.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Absensi SMKBI</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background:#f5f5f5; }
    .sidebar {width:220px; background:#fff; position:fixed; top:0; bottom:0; padding-top:20px;}
    .sidebar a {display:block; padding:12px 20px; color:#444; text-decoration:none;}
    .sidebar a.active, .sidebar a:hover {background:#6f42c1; color:#fff;}
    .content {margin-left:240px; padding:20px;}
    .badge-status{padding:6px 10px;border-radius:10px;}
  </style>
</head>
<body>
<div class="sidebar">
  <a href="index.php" class="<?=basename($_SERVER['PHP_SELF'])=='index.php'?'active':'';?>">Dashboard</a>
  <a href="students.php" class="<?=basename($_SERVER['PHP_SELF'])=='students.php'?'active':'';?>">Students</a>
  <a href="izin.php" class="<?=basename($_SERVER['PHP_SELF'])=='izin.php'?'active':'';?>">Izin</a>
  <a href="profile.php" class="<?=basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'';?>">Profile</a>
</div>

<div class="content">
