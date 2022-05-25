<?php

require_once 'php_mailer/src/Exception.php';
require_once 'php_mailer/src/PHPMailer.php';
require_once 'php_mailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class filesHandler
{

    public function sendLogsToEmail($fileName)
    {

        $outlook_mail = new PHPMailer;

        $outlook_mail->IsSMTP();

        $outlook_mail->Host = 'smtp-mail.outlook.com';

        $outlook_mail->Port = 587;
        $outlook_mail->SMTPSecure = 'tls';
        $outlook_mail->SMTPAuth = true;
        $outlook_mail->Username = ("webte4@outlook.com");
        $outlook_mail->Password = "zadanie4";

        $outlook_mail->setFrom('webte4@outlook.com', 'WEBTE');
        $outlook_mail->AddAddress("webte4@outlook.com", "MY");
        $outlook_mail->Subject = 'WEBTE ZAVERECNE ZADANIE';
        $outlook_mail->Body = 'WEBTE LOGS';
        $outlook_mail->addAttachment($fileName);
        if (!$outlook_mail->Send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $outlook_mail->ErrorInfo;
            exit;
        }
    }

    public function exportLogsToCsv($logs, $fileName)
    {
        $filePointer = fopen($fileName, 'w');
        foreach ($logs as $log) {
            if ($log != null) {
                $arr = [];
                foreach ($log as $logged) {
                    array_push($arr, $logged);
                }
                fputcsv($filePointer, $arr);
            }
        }
        fclose($filePointer);
    }
}