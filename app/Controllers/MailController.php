<?php
namespace App\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    public function sendOrderConfirmation($to, $orderData)
    {
        $mail = new PHPMailer(true);
        try {
            // Serveur SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'email@example.com';
            $mail->Password   = 'Test1234';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinataire & expéditeur
            $mail->setFrom('no-reply@pokemoncommerce.local', 'PokéCommerce');
            $mail->addAddress($to);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre commande';
            $mail->Body    = "<h1>Merci pour votre commande !</h1><p> Réf: {$orderData['ref']} …</p>";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mail error: {$mail->ErrorInfo}");
        }
    }
}
