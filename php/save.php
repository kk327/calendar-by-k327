<?php
    error_reporting(0);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require "connectionConfig.php";
    $data = json_decode(file_get_contents("php://input"), true);

    $authenticationStmt = $connection->prepare("SELECT password, id FROM users WHERE BINARY username = ?");
    $authenticationStmt->bind_param("s", $data["username"]);
    $authenticationStmt->execute();
    $authenticationStmt->bind_result($correctPassword, $userId);
    $authenticationStmt->fetch();

    if(!password_verify($data["password"], $correctPassword)) {
        die("Authentication failed.");
    }

    $connection->close();
    require "connectionConfig.php";

    $deleteStmt = $connection->prepare("DELETE FROM notes WHERE date = ? AND userId = ?");
    $deleteStmt->bind_param("ss", $data["date"], $userId);
    $deleteStmt->execute();
    
    if ($data["content"]) {
        $insertStmt = $connection->prepare("INSERT INTO notes VALUES(?, AES_ENCRYPT(?, ?), ?, ?)");
        $insertStmt->bind_param("ssssi", $data["date"], $data["content"], $data["password"], $data["color"], $userId);
        $insertStmt->execute();
    }

    $connection->close();
?>