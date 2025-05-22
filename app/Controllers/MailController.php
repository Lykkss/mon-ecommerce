<?php
namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    /**
     * Envoie le mail de confirmation de commande
     *
     * @param string $to        Adresse e-mail du client
     * @param array  $orderData ['items'=>…, 'total'=>…, 'ref'=>…]
     */
    public function sendOrderConfirmation(string $to, array $orderData): void
    {
        $mail = new PHPMailer(true);
        try {
            // ===== CONFIG SMTP =====
            $host = getenv('SMTP_HOST') ?: 'smtp.example.com';
            $port = getenv('SMTP_PORT') ?: 587;
            $user = getenv('SMTP_USER') ?: '';
            $pass = getenv('SMTP_PASS') ?: '';

            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->Port       = $port;
            // N’active l’auth s’il n’y a pas de user
            if (!empty($user)) {
                $mail->SMTPAuth   = true;
                $mail->Username   = $user;
                $mail->Password   = $pass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAuth   = false;
            }

            // Expéditeur & Destinataire
            $mail->setFrom('no-reply@pokemoncommerce.local', 'PokéCommerce');
            $mail->addAddress($to);

            // Contenu HTML
            $mail->isHTML(true);
            $mail->Subject = "Confirmation commande #{$orderData['ref']}";

            // Génère le corps du mail
            $body  = "<h1>Merci pour votre commande !</h1>";
            $body .= "<p>Réf: <strong>{$orderData['ref']}</strong></p>";
            $body .= "<ul>";
            foreach ($orderData['items'] as $item) {
                $body .= "<li>{$item['quantity']}× {$item['name']} à "
                       . number_format($item['price'],2,',',' ') . " €</li>";
            }
            $body .= "</ul>";
            $body .= "<p><strong>Total: "
                   . number_format($orderData['total'],2,',',' ') . " €</strong></p>";

            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
            error_log("Mail error ({$e->getMessage()})");
        }
    }
}
