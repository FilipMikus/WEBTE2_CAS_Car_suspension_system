<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../../PDOTokensManager.php";
require_once "../../PDOLogsManager.php";
require_once "FilesHandler.php";
$config = require_once "../../config.php";
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
        if (isset($requestBody["r"]) & isset($requestBody["x1"]) & isset($requestBody["x2"]) &
            isset($requestBody["x3"]) & isset($requestBody["x4"]) & isset($requestBody["x5"])) {
            $commandOctave = 'octave-cli --eval "pkg load control;pkg load io;m1 = 2500; m2 = 320;k1 = 80000; k2 = 500000;b1 = 350; b2 = 15020;pkg load control;A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];C=[0 0 1 0]; D=[0 0];Aa = [[A,[0 0 0 0]\'];[C, 0]];Ba = [B;[0 0]];Ca = [C,0]; Da = D;K = [0 2.3e6 5e8 0 8e6];sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);t = 0:0.01:5;r ='.$requestBody["r"].';[y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,['.$requestBody["x1"].';'.$requestBody["x2"].';'.$requestBody["x3"].';'.$requestBody["x4"].';'.$requestBody["x5"].']);j(1).y = y;j(1).t = t;j(1).x = x;toJSON(j)"';
            $commandOutputOctave = shell_exec($commandOctave);
            $commandOutputJSON = str_replace("ans = ", "", $commandOutputOctave);
            if ($commandOutputOctave != null) {
                header("HTTP/1.1 200 OK");

                $pdoLogsManager->createLog($commandOctave, "OK");
                $readLogs = $pdoLogsManager->readAllLogs();
                $commandOutputJSON = str_replace("ans = ", "", $commandOutputOctave);
                $fileHandler->exportLogsToCsv($readLogs,"logs_export.csv");
                $fileHandler->sendLogsToEmail("logs_export.csv");
                echo $commandOutputJSON;
            } else {
                header("HTTP/1.1 400 Bad Request");
                $pdoLogsManager->createLog($commandOctave, "ERROR");
                $readLogs = $pdoLogsManager->readAllLogs();
                $fileHandler->exportLogsToCsv($readLogs, "logs_export.csv");
                $fileHandler->sendLogsToEmail("logs_export.csv");
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