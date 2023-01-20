<?php

require_once("config.php");

if (!isset($_SESSION["id"]) && $_SESSION["id"] == 1) {
    header("location: login.php");
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getUsers();
        break;
    case "POST":
        addUser();
        break;
    case "PUT":
        editUser();
        break;
    case "DELETE":
        deleteUser();
        break;
    default:
        json_encode(['error' => 'Azione non riconosciuta']);

}

function getUsers() {
    global $connection;
    $sql = "SELECT id, name, surname, email FROM users";

    if(isset($_GET["id"])){
        $sql .= " WHERE id = " . $_GET["id"];
    }
    if($result = $connection->query($sql)){
        $users = [];
        while($row = $result->fetch_assoc()){
            $users[] = $row;
        }
        echo json_encode($users);
    } else {
        echo json_encode(['error' => 'Errore nel caricamento delle imposte']);
    }
}

function addUser() {

    global $connection;

    $name = $connection->real_escape_string($_POST["name"]);
    $surname = $connection->real_escape_string($_POST["surname"]);
    $email = $connection->real_escape_string($_POST["email"]);

    $error = [];
    if(!$name)
        $error["name"] = "Nome non inserito";
    if(!$surname)
        $error["surname"] = "Cognome non inserito";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $error["email"] = "Email non inserita o non valida";
    if(!$_FILES["file"]["name"])
        $error["file"] = "Immagine non inserita";

    if(!$error){
        $sql = "INSERT INTO users (name, surname, email) VALUES ('" . $name . "', '" . $surname . "', '" . $email . "')";
        if($connection -> query($sql)){
            $last_id = $connection->insert_id;

            $path = "/var/www/html/scripts/faces/" . $last_id;

            if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)){
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Errore nel salvataggio dell\'immagine']);
            }
        } else {
            echo json_encode(['error' => 'Errore nel salvataggio dei dati']);
        }

    } else {
        echo json_encode($error);
    }
}

function editUser(){
    global $connection;
    parse_str(file_get_contents('php://input'), $_PUT);

    $id = $connection->real_escape_string($_PUT["id"]);
    $name = $connection->real_escape_string($_PUT["name"]);
    $surname = $connection->real_escape_string($_PUT["surname"]);
    $email = $connection->real_escape_string($_PUT["email"]);

    if ($id == 1) {
        $password = $connection->real_escape_string($_PUT["password"]);
        $passwordConfirmation = $connection->real_escape_string($_PUT["passwordConfirmation"]);
    }

    $error = [];
    if(!$name)
        $error["name"] = "Nome non inserito";
    if(!$surname)
        $error["surname"] = "Cognome non inserito";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $error["email"] = "Email non inserita o non valida";

    if ($id == 1) {
        if ($password != $passwordConfirmation)
            $error["password"] = "Le password non coincidono";
    }

    if(!isset($password)){
        $sql = "UPDATE users SET name = '" .$name . "', surname = '" . $surname . "', email = '" . $email . "' WHERE id = " . $id;
    } else {
        $sql = "UPDATE users SET name = '" .$name . "', surname = '" . $surname . "', email = '" . $email . "', password = '" . password_hash($password, PASSWORD_DEFAULT) . "' WHERE id = " . $id;
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

function deleteUser(){

    global $connection;
    parse_str(file_get_contents('php://input'), $_DELETE);

    $id = $connection->real_escape_string($_DELETE["id"]);

    $sql = "DELETE FROM users WHERE id = " . $id;

    if($connection->query($sql)){

        $path = "/var/www/html/scripts/faces/" . $id;

        if (file_exists($path)) {

            $status=unlink($path);

            if($status){
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Errore nell\' eliminazione dell\'immagine']);
            }
        }
        else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['error' => 'Errore nel salvataggio dei dati']);
    }

}
