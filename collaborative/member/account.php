<?php
include '../component/connect.php';
include '../component/header.php';

$studentID = $_SESSION['studentID'];

// Get user details using PDO
$sql = "SELECT * FROM student WHERE studentID = :studentID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':studentID', $studentID, PDO::PARAM_STR); // bindParam for PDO
$stmt->execute();

if ($stmt->rowCount() === 1) {
  $student = $stmt->fetch(PDO::FETCH_ASSOC); // fetch data as associative array
} else {
  echo "User not found.";
  exit();
}

if (isset($_GET['update']) && $_GET['update'] === 'success') {
  echo "<script>alert('Account updated successfully!');</script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account</title>
  <link rel="stylesheet" href="../css/account.css" />
</head>
<body>
  <div class="account-container">

    <!-- Sidebar -->
    <aside class="sidebar">
  <div class="profile-pic">
  <img src="../<?php echo htmlspecialchars($student['profileImage']); ?>" alt="User Icon" />
  <p><?php echo htmlspecialchars($student['name']); ?></p>
</div>
      <nav class="nav-menu">
        <ul>
          <li><img src="../images/account.png" alt=""><a href="../member/account.php" class="none">My Account</a></li>
          <li><img src="../images/order.png" alt=""> <a href="../member/orderHistory.php" class="none">Order History</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <section class="account-details">
  <h2>My Account</h2>

  <form action="../member/update_account.php" method="POST" enctype="multipart/form-data">
    <!-- Profile image preview -->
    <div class="user-icon">
      <img src="../<?php echo htmlspecialchars($student['profileImage']); ?>" alt="User Icon" style="max-width: 100px;" />
      <input type="file" name="profileImage" accept="image/*" />
    </div>

    <input type="hidden" name="studentID" value="<?php echo htmlspecialchars($student['studentID']); ?>" />

    <div class="detail-row">
      <label>Username:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required />
    </div>

    <div class="detail-row">
      <label>Email Address:</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required />
    </div>

    <div class="detail-row">
      <label>Password:</label>
      <input type="password" name="password" placeholder="Enter new password or leave blank" />
    </div>

    <div class="detail-row">
      <label>Phone Number:</label>
      <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required />
    </div>

    <button type="submit">Update Account</button>
  </form>
</section>
  </div>
</body>
<?php
include '../component/footer.php';
?>
</html>