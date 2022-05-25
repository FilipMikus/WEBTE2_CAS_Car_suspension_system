<?php
header('Content-Type: application/json; charset=utf-8');
include_once "../PDOTokensManager.php";
include_once "../PDOLogsManager.php";
require_once "suspension_system_car/FilesHandler.php";
$config = require_once "../config.php";
$serverName = $config["host"];
$userName = $config["username"];
$userPassword = $config["password"];
$databaseName = $config["dbname"];

if (isset($_GET["token"])) {
    $pdoTokensManager = new PDOTokensManager($serverName, $userName, $userPassword, $databaseName);
    $pdoLogsManager = new PDOLogsManager($serverName, $userName, $userPassword, $databaseName);
    $fileHandler = new FilesHandler();
    if ($pdoTokensManager->readByToken($_GET["token"]) != null) {
        $requestBody = json_decode(file_get_contents('php://input'), true);
        if (isset($requestBody["command"])) {
            $commandOctave = 'octave-cli --eval "' . $requestBody["command"] . '"';
            $commandOutput = shell_exec($commandOctave);
            if ($commandOutput != null) {
                header("HTTP/1.1 200 OK");
                $pdoLogsManager->createLog($commandOctave, "OK");
                $readLogs = $pdoLogsManager->readAllLogs();
                $fileHandler->exportLogsToCsv($readLogs,"suspension_system_car/logs_export.csv");
                $fileHandler->sendLogsToEmail("suspension_system_car/logs_export.csv");
                echo "{\"ans\":\"" . $commandOutput . "\"}";
            } else {
                header("HTTP/1.1 400 Bad Request");
                $pdoLogsManager->createLog($commandOctave, "ERROR");
                $readLogs = $pdoLogsManager->readAllLogs();
                $fileHandler->exportLogsToCsv($readLogs, "suspension_system_car/logs_export.csv");
                $fileHandler->sendLogsToEmail("suspension_system_car/logs_export.csv");
                echo "{\"ans\":\"error\"}";
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo "{\"ans\":\"error\"}";
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo "{}";
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    echo "{}";
}