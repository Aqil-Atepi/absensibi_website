<?php
session_start();
// && $_SESSION["role"] === 'admin' && isset($_SESSION["error"])
if (isset($_SESSION["id"]) && isset($_SESSION["role"])) {
    require_once "../../conn.php";

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <link href="../../assets/styles/main.css" rel="stylesheet">
        <link href="../../assets/images/logo-bi.png" rel="icon">
        <style>
            #popup-screen {
                position: absolute;
                top: 0;
                left: 0;

                width: 100vw;
                height: 100vh;

                display: flex;
                align-items: center;
                justify-content: center;
            }

            #shadow-bg {
                position: absolute;
                top: 0;
                left: 0;

                width: 100vw;
                height: 100vh;

                background-color: var(--color4);
                opacity: 0.8;
            }

            #popup {
                width: 500px;
                height: 500px;

                background-color: var(--color3);

                z-index: 10;

                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                gap: 10px;

                color: var(--color4);
            }

            #error img {
                width: 100px;
                height: 100px;
                filter: brightness(0) saturate(100%) invert(13%) sepia(99%) saturate(6833%) hue-rotate(0deg) brightness(99%) contrast(112%);
            }

            #popup h2 {
                font-size: 35px;
                font-weight: bold;
            }

            #popup p {
                width: 450px;
                height: 100px;
                
                text-align: center;
                text-overflow: ellipsis;
                overflow: hidden;
            }
        </style>
    </head>

    <body>
        <div id="popup-screen">
            <div id="shadow-bg"></div>
            <div id="popup">
                <div id="error"><img src='../../assets/svg/error.svg'></div>
                <h2>Error</h2>
                <p>Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum</p>
            </div>
        </div>
    </body>

    </html>

    <?php
} else {
    header("Location: ../../");
}
?>