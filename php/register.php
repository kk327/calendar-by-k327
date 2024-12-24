<?php
    error_reporting(0);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require "connectionConfig.php";
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data["username"] || strlen($data["password"]) < 6 || strlen($data["username"]) > 255 || !$data["token"]) {
        die("Didn't pass checks performed client-side prior.");
    }

    $tokenStmt = $connection->prepare("SELECT * FROM registrationTokens WHERE BINARY token = ?");
    $tokenStmt->bind_param("s", $data["token"]);
    $tokenStmt->execute();
    $tokenResult = $tokenStmt->get_result();

    $usernameStmt = $connection->prepare("SELECT username FROM users WHERE BINARY username = ?");
    $usernameStmt->bind_param("s", $data["username"]);
    $usernameStmt->execute();
    $usernameResult = $usernameStmt->get_result();

    if ($tokenResult->num_rows == 0) {
        die("Invalid registration token.");
    } elseif ($usernameResult->num_rows != 0) {
        die("This username is taken.");
    }

    $tokenDeletionStmt = $connection->prepare("DELETE FROM registrationTokens WHERE BINARY token = ?");
    $tokenDeletionStmt->bind_param("s", $data["token"]);
    $tokenDeletionStmt->execute();

    $userCreationStmt = $connection->prepare("INSERT INTO users VALUES(NULL, ?, ?)");
    $userCreationStmt->bind_param("ss", $data["username"], password_hash($data["password"], PASSWORD_DEFAULT));
    $userCreationStmt->execute();

    echo("success");
    $connection->close();
?>