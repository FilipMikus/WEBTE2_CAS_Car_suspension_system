<?php
header('Content-Type: application/json; charset=utf-8');
include_once "../PDOTokensManager.php";

$config = require_once "../config.php";
$serverName = $config["host"];
$userName = $config["username"];
$userPassword = $config["password"];
$databaseName = $config["dbname"];

if (isset($_GET["token"])) {
    $pdoTokensManager = new PDOTokensManager($serverName, $userName, $userPassword, $databaseName);
    if ($pdoTokensManager->readByToken($_GET["token"]) != null) {
        $requestBody = json_decode(file_get_contents('php://input'), true);
        if (isset($requestBody["command"])) {
            $commandOutput = shell_exec('octave-cli --eval "' . $requestBody["command"] . '"');
            if ($commandOutput != null) {
                header("HTTP/1.1 200 OK");
                //TODO Successful command to DB logs
                //TODO Write logs to .csv
                //TODO Write logs to e-mail
                echo "{\"ans\":\"" . $commandOutput . "\"}";
            } else {
                header("HTTP/1.1 400 Bad Request");
                //TODO Unsuccessful command to DB logs
                //TODO Write logs to .csv
                //TODO Write logs to e-mail
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