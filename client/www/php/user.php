<?php

require_once("config.php");

if (!isset($_SESSION["id"]) && $_SESSION["id"] == 1) {
    header("location: login.php");
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getUser($_GET["id"]);
        break;
    case "PUT":
        editUser();
        break;
    default:
        json_encode(['error' => 'Azione non riconosciuta']);

}

function getUser($id) {
    global $connection;
    $sql = "SELECT name, surname, email FROM users WHERE id = " . $id;

    if ($result = $connection->query($sql)) {
        $info = $result->fetch_assoc();
        echo json_encode($info);
    } else {
        echo json_encode(['error' => 'Errore nel recupero dei dati']);
    }
}

function editUser(){
    global $connection;
    parse_str(file_get_contents('php://input'), $_PUT);

    $name = $connection->real_escape_string($_PUT["name"]);
    $surname = $connection->real_escape_string($_PUT["surname"]);
    $email = $connection->real_escape_string($_PUT["email"]);
    $password = $connection->real_escape_string($_PUT["password"]);
    $passwordConfirmation = $connection->real_escape_string($_PUT["passwordConfirmation"]);

    $error = [];
    if(!$name)
        $error["name"] = "Nome non inserito";
    if(!$surname)
        $error["surname"] = "Cognome non inserito";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $error["email"] = "Email non inserita o non valida";

    if($password != $passwordConfirmation)
        $error["password"] = "Le password non coincidono";

    if(!$password){
        $sql = "UPDATE users SET name = '" .$name . "', surname = '" . $surname . "', email = '" . $email . "' WHERE id = " . $_SESSION["id"];
    } else {
        $sql = "UPDATE users SET name = '" .$name . "', surname = '" . $surname . "', email = '" . $email . "', password = '" . password_hash($password, PASSWORD_DEFAULT) . "' WHERE id = " . $_SESSION["id"];
    }


    if(!$error){
        if($connection->query($sql)){
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Errore nel salvataggio dei dati']);
        }
    } else {
        echo json_encode($error);
    }
}
