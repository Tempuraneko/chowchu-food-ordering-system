<?php
require '../component/connect.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM student WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expires = time() + 1800; 

        // Remove old tokens
        $pdo->prepare("DELETE FROM reset_tokens WHERE email = ?")->execute([$email]);

        // Save new token
        $pdo->prepare("INSERT INTO reset_tokens (email, token, expires) VALUES (?, ?, ?)")
            ->execute([$email, $token, $expires]);

        $resetLink = "http://localhost:8000/member/reset_password.php?token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ngjiawei0201@gmail.com'; // Your Gmail
            $mail->Password = 'tszsouqmytpxotlg';    // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ngjiawei0201@gmail.com', 'Chowchu Support');
            $mail->addAddress($email); // Recipient

            $mail->isHTML(true);
            $mail->Subject = 'Chowchu Password Reset';
            $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password.";

            $mail->send();
            echo "✅ Reset link sent to $email.";
        } catch (Exception $e) {
            echo "❌ Mail error: {$mail->ErrorInfo}";
        }

    } else {
        echo "❌ Email not found.";
    }
}
?>