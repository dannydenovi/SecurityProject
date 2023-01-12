<?php

require_once("php/config.php");

if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}


/*echo shell_exec(escapeshellcmd("sudo python3 /var/www/html/scripts/test_temp.py"));
echo shell_exec("ls");*/

?>
<html lang="it">

<head>
    <title>Face Recognition</title>

    <meta charset="utf-8">
    <link rel="icon" src="favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="dist/css/style.css" rel="stylesheet">
</head>

<body class="m-4">
    <!--BEGIN HEADER-->
    <header id="navbar" class="p-3 mb-3 border-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                    <b>FaceRecognition</b>
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li>
                        <span class="nav-link px-2 link-secondary" id="users">Utenti</span>
                    </li>
                    <li><span class="nav-link px-2 link-dark" id="logs">Logs</span></li>
                    <li>
                        <span class="nav-link px-2 link-dark" id="settings">Impostazioni</span>
                    </li>
                </ul>

                <div class="dropdown text-end">
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <!--BEGIN USER NAME-->
                        <span class="text-dark" id="namePlace"></span>
                        <!--END USER NAME-->
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li><button role="button" id="logout-button" class="dropdown-item text-danger">Logout</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <!--END HEADER-->

    <!--BEGIN MAIN-->
    <main id="main" class="container mb-5"></main>
    <!--END MAIN-->

</body>
<script src="./dist/js/index.js"></script>
<script src="./dist/js/users.js"></script>
<script>
    var logout = $("#logout-button");
    logout.click(function () {
        $.ajax({
            url: "php/loginManager.php",
            type: "POST",
            data: {
                logout: true
            },
            success: function (data) {
                var json = JSON.parse(data);

                if (json.success)
                    window.location.href = "login.php";
            }
        });
    });
    </script>
</html>