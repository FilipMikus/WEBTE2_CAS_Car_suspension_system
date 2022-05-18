<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../PDOTranslationsManager.php";

$config = require_once "../config.php";
$serverName = $config["host"];
$userName = $config["username"];
$userPassword = $config["password"];
$databaseName = $config["dbname"];

if (isset($_GET["language"])) {
    $pdoTranslationsManager = new PDOTranslationsManager($serverName, $userName, $userPassword, $databaseName);
    $result = $pdoTranslationsManager->readAllByLanguageCode($_GET["language"]);
    if ($result != null) {
        header("HTTP/1.1 200 OK");
        echo json_encode($result);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo "{}";
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo "{}";
}