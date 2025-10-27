<?php
include '../component/connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $foodId = $_POST['foodId'];
    $status = $_POST['status'];

    $validStatuses = ['New', 'Most Popular', 'Out of Stock', 'N/A'];
    if (!in_array($status, $validStatuses)) {
        die("Invalid status.");
    }

    $sql = "UPDATE fooddetail SET status = :status WHERE foodId = :foodId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':foodId', $foodId);
    
    if ($stmt->execute()) {
        echo 'success'; 
    } else {
        echo 'error';
    }
}
?>
