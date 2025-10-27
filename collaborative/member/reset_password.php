<?php
require '../component/connect.php';

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM reset_tokens WHERE token = ? AND expires > ?");
$stmt->execute([$token, time()]);
$row = $stmt->fetch();

if (!$row) {
    die("Invalid or expired reset link.");
}
?>

<form action="update_password.php" method="POST">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <label>Enter New Password:</label>
  <input type="password" name="new_password" required>
  <button type="submit">Update Password</button>
</form>