<?php
session_start();
include "config.php"; // pastikan file koneksi benar

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($nik) && !empty($password)) {
        $sql = "SELECT * FROM guru WHERE nik='$nik' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['id']   = $row['id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            header("Location: index.php");
            exit;
        } else {
            $error = "❌ NIK atau Password salah!";
        }
    } else {
        $error = "⚠️ Harap isi semua field!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Guru</title>
    <style>
        body {font-family: Arial, sans-serif; background: #f4f4f4; text-align: center;}
        .login-box {width: 350px; margin: 100px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.2);}
        input {width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;}
        button {width: 95%; padding: 10px; background: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer;}
        button:hover {background: #0056b3;}
        .error {color: red; margin-top: 10px;}
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Guru</h2>
        <form method="POST" action="">
            <input type="text" name="nik" placeholder="Masukkan NIK" required><br>
            <input type="password" name="password" placeholder="Masukkan Password" required><br>
            <button type="submit">Login</button>
        </form>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
    </div>
</body>
</html>
<?php
// Tutup koneksi jika perlu