<?php
// File di configurazione per la connessione al db
$username = "root";
$password = "root";
$addr = "db";
$database = "security_project";
$port = 3306;

$connection = mysqli_connect($addr, $username, $password, $database);

if (!$connection)
    die("Errore di connessione: ".$connection->connect_error);

session_start();