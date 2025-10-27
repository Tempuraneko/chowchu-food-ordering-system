<?php
include '../component/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['orderId'];
    $orderStatus = $_POST['orderStatus'];

    // Valid statuses for the order
    $validStatuses = ['Pending', 'Preparing', 'ReadyForPickup', 'Completed', 'Cancelled'];

    // Check if the status is valid
    if (!in_array($orderStatus, $validStatuses)) {
        die("Invalid status.");
    }

    // Update query
    $sql = "UPDATE orders SET orderStatus = :orderStatus WHERE orderId = :orderId";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':orderStatus', $orderStatus);
    $stmt->bindParam(':orderId', $orderId);

    // Execute the statement and return success or error
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
