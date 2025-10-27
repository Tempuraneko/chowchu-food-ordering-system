<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "foodshop";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Step 1: Generate new studentID
$result = $conn->query("SELECT studentID FROM student ORDER BY studentID DESC LIMIT 1");

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $lastId = $row['studentID'];         
  $num = intval(substr($lastId, 3)) + 1;
  $newId = "STU" . str_pad($num, 3, "0", STR_PAD_LEFT);
} else {
  $newId = "STU001";
}

// Step 2: Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Step 3: Handle profile image upload
$targetDir = "../images/";
$imageName = basename($_FILES["profileImage"]["name"]);
$targetFile = $targetDir . $imageName;
$imagePath = "images/" . $imageName;

// Move uploaded file to server
if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
    // Step 4: Insert data into database with image path
    $sql = "INSERT INTO student (studentID, name, email, phone, password, profileImage) 
            VALUES ('$newId', '$name', '$email', '$phone', '$password', '$imagePath')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../html/signup.html?success=true");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error uploading profile image.";
}

$conn->close();
?>