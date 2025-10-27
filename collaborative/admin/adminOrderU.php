<?php
include '../component/connect.php';
require '../component/admin_sidebar.php';

authManager();

if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    if (!isset($_GET['orderId'])) {
        $_SESSION['errors'] = ["No order selected."];
        header('Location: adminOrder.php');
        exit();
    }

    $orderId = $_GET['orderId'];

    $sql = "SELECT * FROM orders WHERE orderId = :orderId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['errors'] = ["Order not found."];
        header("Location: adminOrder.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $orderId = $_POST['orderId'];
    $status = $_POST['status'];
    $totalAmount = $_POST['totalAmount'];
    $discountAmount = $_POST['discountAmount'];
    $finalAmount = $_POST['finalAmount'];

    $errors = [];

    if (!$status || !$totalAmount || !$finalAmount) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!filter_var($totalAmount, FILTER_VALIDATE_FLOAT) || $totalAmount < 0) {
        $errors[] = "Total amount must be a valid positive number.";
    }

    if (!filter_var($finalAmount, FILTER_VALIDATE_FLOAT) || $finalAmount < 0) {
        $errors[] = "Final amount must be a valid positive number.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['formData'] = [
            'orderId' => $orderId,
            'status' => $status,
            'totalAmount' => $totalAmount,
            'discountAmount' => $discountAmount,
            'finalAmount' => $finalAmount,
        ];
        header("Location: adminOrderU.php?orderId=$orderId");
        exit();
    }

    $sql = "UPDATE orders SET status = :status, totalAmount = :totalAmount, discountAmount = :discountAmount, finalAmount = :finalAmount WHERE orderId = :orderId";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':totalAmount', $totalAmount, PDO::PARAM_STR);
    $stmt->bindParam(':discountAmount', $discountAmount, PDO::PARAM_STR);
    $stmt->bindParam(':finalAmount', $finalAmount, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Order updated successfully!";
        header("Location: adminOrder.php");
        exit();
    } else {
        $_SESSION['errors'] = ["Database error: Failed to update order."];
        header("Location: adminOrderU.php?orderId=$orderId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
</head>
<body>
<?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']);?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Edit Order</h3>
        </div>

        <div class="box">
            <form action="" method="post">
                <fieldset style="border: none;">

                    <div class="inputBox">
                        <label for="orderId">Order ID:</label>
                        <input type="text" name="orderId" id="orderId" value="<?php echo $order['orderId']; ?>" readonly>
                    </div>

                    <div class="inputBox">
                        <label for="totalAmount">Total Amount:</label>
                        <input type="text" name="totalAmount" id="totalAmount" value="<?php echo $order['totalAmount']; ?>" required>
                    </div>

                    <div class="inputBox">
                        <label for="discountAmount">Discount Amount:</label>
                        <input type="text" name="discountAmount" id="discountAmount" value="<?php echo $order['discountAmount']; ?>">
                    </div>

                    <div class="inputBox">
                        <label for="finalAmount">Final Amount:</label>
                        <input type="text" name="finalAmount" id="finalAmount" value="<?php echo $order['finalAmount']; ?>" required>
                    </div>

                    <div class="inputBox">
                        <label for="status">Status:</label>
                        <select name="status" id="status" required>
                            <?php
                            $statuses = ['Pending', 'Processing', 'Delivered', 'Completed', 'Cancelled'];
                            foreach ($statuses as $statusOption) {
                                $selected = (trim($order['status']) === $statusOption) ? 'selected' : '';
                                echo "<option value=\"$statusOption\" $selected>$statusOption</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="button">
                        <button type="submit" name="submit" class="submit-btn">Update</button>
                        <button type="button" class="cancel-btn" onclick="window.location.href='adminOrder.php';">Cancel</button>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
</div>

</body>
</html>
