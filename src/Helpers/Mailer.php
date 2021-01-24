<?php

namespace KirchenImWeb\Helpers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function sendMail($subject, $bodyHTML, $bodyText = ''): ?bool
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->Port = 25;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        try {
            $mail->setFrom(MAIL_FROM);
            $mail->addAddress(MAIL_TO);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->msgHTML($bodyHTML);
            $mail->AltBody = $bodyText;
            return $mail->send();
        } catch (Exception $exception) {
            return false;
        }
    }
}
