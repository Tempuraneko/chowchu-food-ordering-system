<?php
require '../component/connect.php';

if (isset($_POST['token'], $_POST['new_password'])) {
    $token = $_POST['token'];
    $newPass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT * FROM reset_tokens WHERE token = ? AND expires > ?");
    $stmt->execute([$token, time()]);
    $row = $stmt->fetch();

    if ($row) {
        $email = $row['email'];
        $pdo->prepare("UPDATE student SET password = ? WHERE email = ?")
            ->execute([$newPass, $email]);

        $pdo->prepare("DELETE FROM reset_tokens WHERE email = ?")->execute([$email]);

        echo "✅ Password has been updated successfully!";
        header("Location: ../html/login.html?status=success");
    } else {
        echo "❌ Invalid or expired token.";
    }
}
?>