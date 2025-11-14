<?php
session_start();

if (isset($_SESSION["username"])) {
    require_once "../../conn.php";

    $username = $_SESSION["username"];

    function getCount($conn, $table)
    {
        $accesstable = ['siswa', 'guru', 'kelas'];

        if (!in_array($table, $accesstable)) {
            throw new Exception("Invalid table name");
        }

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table");
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc();
        return $total['total'];
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <title>Dashboard</title>
        <style>
            .content {
                width: 90%;
                height: 15%;

                background-color: var(--color1);

                border-radius: 10px;

                padding: 20px;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;

                color: var(--color3);
            }

            .user {
                width: 50%;
                height: auto;

                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: baseline;

                gap: 20px;
            }

            .profile {
                border-radius: 10px;

                width: 120px;
                height: 120px;

                overflow: hidden;
            }

            .profile img {
                width: 100%;
                height: 100%;
            }

            .info {
                display: flex;
                flex-direction: column;
                align-items: baseline;
                justify-content: center;
            }

            .info h1 {
                font-size: 30px;
            }

            .info p {
                font-size: 20px;
            }

            .edit {
                width: 20%;
                height: 100%;

                display: flex;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .edit a {
                flex: 1;
            }

            .edit button {
                width: 100%;
                height: auto;

                padding: 10px;

                border: none;
                border-radius: 10px;

                font-size: 20px;
                font-weight: bold;
            }

            .edit .edit-profile button {
                color: var(--color3);
                background-color: var(--indicator1);
            }

            .edit .password-profile button {
                color: var(--color4);
                background-color: var(--indicator3);
            }
        </style>
    </head>

    <body>
        <?php
        include "sidebar.php";
        ?>

        <div class="container">
            <div class="title">
                <h1>Profile</h1>
            </div>

            <div class="content">
                <div class="user">
                    <div class="profile">
                        <img src="../../assets/images/logo-bi.png">
                    </div>
                    <div class="info">
                        <h1><?php echo "" . $username?></h1>
                        <p>Administrasi</p>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>