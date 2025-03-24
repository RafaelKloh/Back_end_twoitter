<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail_service
{
    public static function sendVerificationEmail($email, $code)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuração para usar SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com'; // SMTP do Zoho
            $mail->SMTPAuth = true;
            $mail->Username = 'twoitter@zohomail.com'; // Substitua pelo seu e-mail Zoho
            $mail->Password = 'KLJsproex@1'; // Substitua pela sua senha Zoho ou senha de app
            $mail->SMTPSecure = 'tls'; // Usar TLS
            $mail->Port = 587; // Porta para TLS

            // De onde o e-mail será enviado
            $mail->setFrom('twoitter@zohomail.com', 'VerifyAccount'); // Substitua pelo seu e-mail Zoho
            // Para quem o e-mail será enviado
            $mail->addAddress($email);

            // Assunto do e-mail
            $mail->Subject = 'Verification code Twoitter';
            // Corpo do e-mail
            $mail->Body = "Your verification code is: $code";

            // Envia o e-mail
            $mail->send();
        } catch (Exception $e) {
            // Caso ocorra algum erro ao enviar o e-mail
            error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
        }
    }
}
