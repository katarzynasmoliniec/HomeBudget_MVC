<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static function send($to, $subject, $text, $html)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = Config::PHPMAILER_USERNAME;
            $mail->Password = Config::PHPMAILER_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom(Config::PHPMAILER_SETFROM);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $text;

            $mail->send();
            echo 'Message has been sent';    
        } 
        catch (Exception $e) 
        {
             echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}