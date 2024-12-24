<?php
    error_reporting(0);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require "connectionConfig.php";
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $connection->prepare("SELECT password FROM users WHERE BINARY username = ?");
    $stmt->bind_param("s", $data["username"]);
    $stmt->execute();
    $stmt->bind_result($correctPassword);
    $stmt->fetch();

    if (password_verify($data["password"], $correctPassword)) {
        echo("success");
    } else {
        echo("Wrong login information");
    }
    $connection->close();
?>