<?php
    try {
        $connection = new mysqli("hostname", "username", "password", "database");
    } catch(Exception $e) {
        die("Connection to the database failed.");
    }
?>