<?php
require '../component/connect.php';  // Ensure the connection is included
session_start();

// Step 1: Ensure the user is logged in
if (!isset($_SESSION['studentID'])) {
    echo "User not logged in.";  // Error message if the user is not logged in
    exit;
}

// Step 2: Ensure orderId is sent in the POST request
if (!isset($_POST['orderId'])) {
    echo "No order ID received.";  // Error message if orderId is missing
    exit;
}

$orderId = intval($_POST['orderId']);  // Make sure to sanitize the orderId

// Step 3: Prepare and execute the SQL to update the order status
$stmt = $pdo->prepare('UPDATE orders SET orderStatus = "Cancelled" WHERE orderId = ?');
if ($stmt->execute([$orderId])) {
    echo "success";  // Success message if the query runs correctly
} else {
    echo "Database update failed.";  // Error message if the database query fails
}
?>
