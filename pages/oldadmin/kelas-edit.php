<?php session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../../");
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/main.css" rel="stylesheet">
    <title>Edit Kelas</title>
    <style>
        .content {
            width: 95%;
            height: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: baseline;
            border-radius: 10px;
            gap: 20px;
            color: var(--color3);

            padding: 10px;
        }

        .class-info {
            width: 100%;
            height: 20%;

            padding: 10px;

            background-color: var(--color1);
            border-radius: 10px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .class-head {
            width: 100%;

            padding: 0px 10px;

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;

            gap: 10px;
        }

        .class-items {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: center;
        }

        .class-items * {
            width: 100%;
            font-size: 15px;
        }

        .class-set {
            display: flex;
            flex-direction: row;
            align-items: baseline;
            justify-content: center;
            gap: 5px;
        }

        .class-set * {
            padding: 5px;
            padding-left: 10px;
            border: none;
            border-radius: 10px;
        }

        .class-set input {
            flex: 3;
        }

        .class-set a {
            flex: 1;
        }

        .class-set.add input {
            flex: 5;
        }

        .class-set.add a {
            flex: 1;
        }

        .class-set button {
            color: var(--color3);
            border: none;
            background-color: var(--indicator1);
        }

        .class-add {
            width: 100%;

            padding: 0px 10px;
            
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;

            gap: 10px;
        }

        .class-student {
            width: 101.5%;
            height: 80%;

            background-color: var(--color2);

            border: 1px solid var(--color5);
            border-radius: 10px;

            overflow: hidden;
        }

        .class-student table {
            width: 100%;

            border-collapse: collapse;
        }

        .class-student table thead{
            background-color: var(--color1);
            color: var(--color3);

            border-bottom: 1px solid var(--color5);
        }

        .class-student table th {
            text-align: left;

            padding: 10px;

            font-size: 20px;
            font-weight: bold;
            position: relative;
        }

        .class-student table tbody td{
            padding: 10px;
            font-size: 15px;

            color: var(--color5);
        }

        .class-student table tbody tr:hover{
            background-color: var(--color3);
        }

        .action-buttons button {
            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;
            border-radius: 10px;
        }

        .action-buttons button img {
            width: 20px;
            height: 20px;

            filter: invert(100%) sepia(0%) saturate(7486%) hue-rotate(108deg) brightness(104%) contrast(104%);
        }

        .action-buttons .delete {
            background-color: var(--indicator5);
        }
    </style>
</head>

<body> 
    <?php include "sidebar.php"; ?>
    <div class="container">
        <div class="title">
            <h1>Edit Kelas</h1>
        </div>
        <div class="content">
            <div class="class-info">
                <div class="class-head">
                    <div class="class-items">
                        <label>Kelas</label>
                        <div class="class-set">
                            <input type="text" name="kelas" placeholder="Nama Kelas...">
                            <a href="#">
                                <button> Update </button>
                            </a>
                        </div>
                    </div>
                    <div class="class-items"> <label>Walikelas</label>
                        <div class="class-set">
                            <input type="text" name="walikelas" placeholder="Nama Guru...">
                            <a href="#">
                                <button> Update </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="class-add">
                    <div class="class-items">
                        <label>Siswa</label>
                        <div class="class-set add">
                            <input type="text" name="siswa" placeholder="Nama Siswa...">
                            <a href="#">
                                <button> Tambah </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="class-student">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>01.02.3.045</td>
                            <td>Nama Siswa</td>
                            <td class="action-buttons"> 
                                <a href="kelas-delete-siswa.php">
                                    <button class="delete">
                                        <img src="../../assets/svg/trash.svg" alt="Password Icon">
                                    </button>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>