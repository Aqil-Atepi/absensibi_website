<?php
include 'config.php';
session_start();

// Pastikan user login
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$id   = intval($_POST['id']);
$role = mysqli_real_escape_string($conn, $_POST['role']);
$nik  = mysqli_real_escape_string($conn, $_POST['nik']);
$pass = $_POST['password'] ?? '';

// ==== Upload Foto ====
$foto_sql = '';
if(!empty($_FILES['foto']['name'])){
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    // Validasi ekstensi
    $allowed = ['jpg','jpeg','png','gif'];
    if(in_array($ext, $allowed)){
        $newName = 'guru_'.$id.'_'.time().'.'.$ext;

        // pastikan folder uploads ada
        if(!is_dir('uploads')) mkdir('uploads');

        if(move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/'.$newName)){
            $foto_sql = ", foto='".$newName."'";
        }
    }
}

// ==== Update Password hanya jika diisi ====
$pw_sql = '';
if(!empty($pass)){
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $pw_sql = ", password='$hash'";
}

// ==== Query Update ====
$sql = "UPDATE users 
        SET role='$role', nik='$nik' 
        $pw_sql $foto_sql 
        WHERE id=$id";

if(mysqli_query($conn,$sql)){
    header('Location: profile.php?success=1');
} else {
    echo "Gagal update: " . mysqli_error($conn);
}
exit;
