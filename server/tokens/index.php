<?php
header('Content-Type: application/json; charset=utf-8');
include_once "../PDOTokensManager.php";

$config = require_once "../config.php";
$serverName = $config["host"];
$userName = $config["username"];
$userPassword = $config["password"];
$databaseName = $config["dbname"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdoTokensManager = new PDOTokensManager($serverName, $userName, $userPassword, $databaseName);
    $createdToken = $pdoTokensManager->createToken();
    if ($createdToken != null) {
        header("HTTP/1.1 201 Created");
        echo json_encode($createdToken);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo "{}";
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "{}";
}