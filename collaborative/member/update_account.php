<?php
session_start();

$servername = "sql205.infinityfree.com";
$username = "if0_38898413";
$password = "Knight2900";
$database = "if0_38898413_foodshop";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$studentID = $_POST['studentID'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$newPassword = $_POST['password'];

// Optional: handle profile image upload
$profileImagePath = null;
if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
    $targetDir = "../images/";
    $imageName = basename($_FILES["profileImage"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
        $profileImagePath = "images/" . $imageName;
    }
}

// Build update query dynamically
$updateFields = "name=?, email=?, phone=?";
$params = [$name, $email, $phone];
$types = "sss";

if (!empty($newPassword)) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateFields .= ", password=?";
    $params[] = $hashedPassword;
    $types .= "s";
}

if ($profileImagePath !== null) {
    $updateFields .= ", profileImage=?";
    $params[] = $profileImagePath;
    $types .= "s";
}

$params[] = $studentID;
$types .= "s";

$sql = "UPDATE student SET $updateFields WHERE studentID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    header("Location: ../member/account.php?update=success");
    exit();
} else {
    echo "Update failed: " . $conn->error;
}

$conn->close();
?>