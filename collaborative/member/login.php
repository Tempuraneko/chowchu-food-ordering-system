<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "foodshop";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Find student
$sql = "SELECT * FROM student WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  if (password_verify($password, $row['password'])) {
    $_SESSION['studentID'] = $row['studentID'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['profileImage'] = $row['profileImage'];
    echo "<script>alert('Login successful!'); window.location.href='../member/moodBasedSuggest.php';</script>";
  } else {
    echo "<script>alert('Incorrect password'); window.location.href='../html/login.html';</script>";
  }
} else {
  echo "<script>alert('Account not found'); window.location.href='../html/login.html';</script>";
}

$conn->close();
?>