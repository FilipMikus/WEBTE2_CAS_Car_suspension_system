<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../PDOLogsManager.php";

$config = require_once "../config.php";
$serverName = $config["host"];
$userName = $config["username"];
$userPassword = $config["password"];
$databaseName = $config["dbname"];

$pdoLogsManager = new PDOLogsManager($serverName, $userName, $userPassword, $databaseName);
$result = $pdoLogsManager->readAllLogs();
if ($result != null) {
    header("HTTP/1.1 200 OK");
    echo json_encode($result);
} else {
    header("HTTP/1.1 404 Not Found");
    echo "{}";
}
