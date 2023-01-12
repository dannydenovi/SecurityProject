<?php

require_once("./config.php");

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $connection->real_escape_string($_POST['email']);
    $password = $connection->real_escape_string($_POST['password']);
    if ($result = $connection->query("SELECT * FROM users WHERE email = '$email'")) {

        $row = $result->fetch_array(MYSQLI_ASSOC);
        if (password_verify($password, $row['password']) && $row["id"] == 1) {
            $_SESSION['id'] = $row['id'];
            echo json_encode(['success' => true]);
        } else
            echo json_encode(['error' => 'Email o password errati']);
    } else
        echo json_encode(['error' => 'Utente non trovato']);
} else if($_POST["logout"] == "true"){
    session_destroy();
    echo json_encode(['success' => true]);
} else {
    die(json_encode(['error' => 'Email o password non inseriti' . $email . $password]));
}

$connection->close();