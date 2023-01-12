<?php

require_once("php/config.php");

if(isset($_SESSION['id'])){
    header("Location: index.php");
    exit();
}

?>
<html lang="it">

<head>
    <title>Face Recognition</title>
    <meta charset="utf-8">
    <link rel="icon" src="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <script src="./dist/js/login.js"></script>
    <link href="dist/css/style.css" rel="stylesheet">

</head>

<body class="text-center login-body">

    <main class="form-signin w-100 m-auto">
            <img class="mb-4" src="favicon.ico" alt="" width="72" height="72">
            <h1 class="h3 mb-3 fw-normal">Login</h1>

            <div class="form-floating">
                <input type="email" class="form-control" id="email" placeholder="name@example.com">
                <label for="floatingInput">Email</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>

            <button id="loginButton" class="w-100 btn btn-lg btn-primary" type="button">Accedi</button>
            <p class="mt-5 mb-3 text-muted">Anchesi - De Novi &copy; 2023</p>
    </main>

</body>
</html>