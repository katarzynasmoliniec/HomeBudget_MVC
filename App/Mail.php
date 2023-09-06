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
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $to;
            $mail->Password = 'fffgwzsepcnjtykn';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('from@example.com');
            $mail->addAddress('katarzyna.smoliniec.programista@gmail.com');
            $mail->addReplyTo('from@example.com');
            $mail->Subject = $subject;
            $mail->Body = $html;

            $mail->send();
            echo 'Message has been sent';    
        } 
        catch (Exception $e) 
        {
             echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}