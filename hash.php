<?php
include 'conn.php'; // your database connection

// Fetch all users
$result = $conn->query("SELECT username, password FROM guru");

while ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $plainPassword = $row['password'];

    // Hash the plain password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update the database
    $stmt = $conn->prepare("UPDATE guru SET password=? WHERE username=?");
    $stmt->bind_param("si", $hashedPassword, $username);
    $stmt->execute();
}

echo "All passwords have been hashed successfully!";
