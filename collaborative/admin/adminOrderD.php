<?php
include '../component/connect.php';
require '../component/_base2.php';

if (isset($_POST["deleteId"]) && is_array($_POST["deleteId"])) {
    $orderIds = $_POST['deleteId']; 

    $pdo->beginTransaction(); 

    try {
        // Delete items from order_detail table (related to orders)
        $deleteItemsQuery = "DELETE FROM order_detail WHERE orderId IN (" . implode(",", array_fill(0, count($orderIds), "?")) . ")";
        $stmtItems = $pdo->prepare($deleteItemsQuery);

        if ($stmtItems) {
            foreach ($orderIds as $key => $orderId) {
                $stmtItems->bindValue($key + 1, $orderId, PDO::PARAM_INT);
            }

            if (!$stmtItems->execute()) {
                throw new Exception("Failed to delete order items: " . $stmtItems->errorInfo()[2]);
            }
        }

        // Delete associated payments (if necessary) from the payment table
        $deletePaymentQuery = "DELETE FROM payment WHERE orderId IN (" . implode(",", array_fill(0, count($orderIds), "?")) . ")";
        $stmtPayment = $pdo->prepare($deletePaymentQuery);

        if ($stmtPayment) {
            foreach ($orderIds as $key => $orderId) {
                $stmtPayment->bindValue($key + 1, $orderId, PDO::PARAM_INT);
            }

            if (!$stmtPayment->execute()) {
                throw new Exception("Failed to delete payment records: " . $stmtPayment->errorInfo()[2]);
            }
        }

        // Finally, delete the orders from the orders table
        $deleteOrdersQuery = "DELETE FROM orders WHERE orderId IN (" . implode(",", array_fill(0, count($orderIds), "?")) . ")";
        $stmtOrders = $pdo->prepare($deleteOrdersQuery);

        if ($stmtOrders) {
            foreach ($orderIds as $key => $orderId) {
                $stmtOrders->bindValue($key + 1, $orderId, PDO::PARAM_INT);
            }

            if ($stmtOrders->execute()) {
                $pdo->commit(); // Commit all changes
                echo "success";
            } else {
                throw new Exception("Failed to delete orders: " . $stmtOrders->errorInfo()[2]);
            }
        } else {
            throw new Exception("Failed to prepare the query: " . $pdo->errorInfo()[2]);
        }

        // Close cursors for cleanup
        $stmtItems->closeCursor();
        $stmtPayment->closeCursor();
        $stmtOrders->closeCursor();
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback on failure
        echo "Error: " . $e->getMessage(); // Display error message
    }
} else {
    echo "No order IDs received."; // If no orders are selected
}
?>
