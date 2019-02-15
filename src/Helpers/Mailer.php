<?php

namespace KirchenImWeb\Helpers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends AbstractHelper
{
    public function sendMail($subject, $bodyHTML, $bodyText = '')
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Host = MAIL_HOST;
        $mail->Port = 25;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        $mail->setFrom(MAIL_FROM);
        $mail->addAddress(MAIL_TO);
        $mail->Subject = $subject;
        $mail->msgHTML($bodyHTML);
        $mail->AltBody = $bodyText;

        try {
            return $mail->send();
        } catch (Exception $exception) {
            return false;
        }
    }
}
