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

    $stmt = $connection->prepare("SELECT date, AES_DECRYPT(content, ?), color FROM notes WHERE userId = ?");
    $stmt->bind_param("si", $data["password"], $userId);
    $stmt->execute();
    $stmtResult = $stmt->get_result();

    $object = "[";
    $first = true;
    while ($row = $stmtResult->fetch_array()) {
        if ($first) {
            $first = false;
        } else {
            $object .= ",";
        }

        $newLines = "[";
        $previousPosition = 0;
        for ($i2 = 0; $i2 < substr_count($row[1], PHP_EOL); $i2++) {
            if ($i2 != 0) {
                $newLines .= ",";
            }
            $newLines .= '"' . strpos($row[1], PHP_EOL, $previousPosition) . '"';
            $previousPosition = strpos($row[1], PHP_EOL, $previousPosition) + 1;
        }
        $newLines .= "]";

        $object .= '{"year":' . date("Y", strtotime($row[0])) + 0 . ',"month":' . date("m", strtotime($row[0])) - 1 . ',"day":' . date("d", strtotime($row[0])) - 1 . ',"color":"' . $row[2] . '","content":"' . str_replace(PHP_EOL, "", $row[1]) . '", "newLines":' . $newLines . '}';
    }
    $object = $object . "]";

    echo($object);
    $connection->close();
?>