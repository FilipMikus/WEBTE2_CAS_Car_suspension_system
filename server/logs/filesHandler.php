<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../php_mailer/src/Exception.php';
require_once '../php_mailer/src/PHPMailer.php';
require_once '../php_mailer/src/SMTP.php';


class filesHandler
{
    public function sendLogsToEmail($logsJSON, $fileName)
    {
        $config = require_once '../config.php';
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.azet.sk";
        $mail->Port = 465;
        $mail->Username = $config["logsEmail"];
        $mail->Password = $config["logsPassword"];


        $mail->AddAddress($config["logsEmail"]);
        $mail->SetFrom($config["logsEmail"], "zaverecne zadanie");
        $mail->Subject = "LOGS ";
        $mail->Body = "EXPORTED LOGS";
        $this->exportLogsToCsv($fileName, $logsJSON);
        $mail->addAttachment($fileName);

        try {
            $mail->Send();
            //echo "Success!";
        } catch (Exception $e) {
            //echo "Fail - " . $mail->ErrorInfo;
        }

    }

    public function exportLogsToCsv($destinationPath, $logsJSON)
    {
        $jsonans = json_decode($logsJSON, true);

        $filePointer = fopen($destinationPath, 'w');

        foreach ($jsonans as $i) {

            fputcsv($filePointer, $i);
        }

        fclose($filePointer);

    }

}