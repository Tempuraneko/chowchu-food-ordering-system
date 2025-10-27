<?php
session_start();
include '../component/connect.php';
require '../component/admin_sidebar.php';

// Handle food item addition to session via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_food') {
    $foodData = json_decode($_POST['foodData'], true);

    // Initialize session variable if it doesn't exist
    if (!isset($_SESSION['orderItems'])) {
        $_SESSION['orderItems'] = [];
    }

    // Add food items to the session
    foreach ($foodData as $foodItem) {
        $foodId = $foodItem['foodId'];
        $quantity = $foodItem['quantity'];

        // Fetch food details from the database
        $query = "SELECT foodName, price FROM fooddetail WHERE foodId = :foodId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':foodId', $foodId);
        $stmt->execute();
        $food = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($food) {
            $totalPrice = $food['price'] * $quantity;

            $_SESSION['orderItems'][] = [
                'foodId' => $foodId,
                'foodName' => $food['foodName'],
                'price' => $food['price'],
                'quantity' => $quantity,
                'totalPrice' => $totalPrice
            ];
        }
    }

    // Return a success response for AJAX handling
    echo json_encode(['success' => true]);
    exit();
}

// Handle order creation if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderData = $_POST;
    $memberId = $_POST['memberId'] ?? '';
    $voucherId = $_POST['voucherId'] ?? '';
    $orderStatus = $_POST['orderStatus'] ?? 'Pending';
    $orderDate = $_POST['orderDate'] ?? date('Y-m-d H:i:s');
    $totalAmount = $_POST['totalAmount'] ?? 0;
    $discountAmount = $_POST['discountAmount'] ?? 0;
    $foodItems = $_POST['foodItems'] ?? []; 
    $quantities = $_POST['quantity'] ?? [];

    // Calculate subtotal and final total
    $subtotal = 0;
    foreach ($foodItems as $index => $foodId) {
        $query = "SELECT price FROM fooddetail WHERE foodId = :foodId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':foodId', $foodId);
        $stmt->execute();
        $food = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($food) {
            $subtotal += $food['price'] * $quantities[$index]; 
        }
    }

    $finalAmount = $subtotal - $discountAmount;

    // Save the order in session
    $_SESSION['order'] = [
        'memberId' => $memberId,
        'voucherId' => $voucherId,
        'orderStatus' => $orderStatus,
        'orderDate' => $orderDate,
        'foodItems' => $foodItems,
        'quantities' => $quantities,
        'subtotal' => $subtotal,
        'finalAmount' => $finalAmount,
        'discountAmount' => $discountAmount,
    ];

    // If the form is ready to submit, insert into the database
    if (isset($_POST['submitOrder'])) {
        $pdo->beginTransaction();
        try {
            // Insert into the 'orders' table
            $orderSql = "INSERT INTO orders (memberId, voucherId, orderStatus, orderDate, subtotal, totalAmount, discountAmount) 
                         VALUES (:memberId, :voucherId, :orderStatus, :orderDate, :subtotal, :totalAmount, :discountAmount)";
            $orderStmt = $pdo->prepare($orderSql);
            $orderStmt->execute([
                ':memberId' => $memberId,
                ':voucherId' => $voucherId,
                ':orderStatus' => $orderStatus,
                ':orderDate' => $orderDate,
                ':subtotal' => $subtotal,
                ':totalAmount' => $finalAmount,
                ':discountAmount' => $discountAmount
            ]);

            $orderId = $pdo->lastInsertId(); 

            // Insert into 'order_detail' table
            foreach ($foodItems as $index => $foodId) {
                $quantity = $quantities[$index];
                $query = "SELECT price FROM fooddetail WHERE foodId = :foodId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':foodId', $foodId);
                $stmt->execute();
                $food = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($food) {
                    $foodPrice = $food['price'];
                    $totalPrice = $foodPrice * $quantity;

                    $orderDetailSql = "INSERT INTO order_detail (orderId, foodId, quantity, price, totalAmount)
                                       VALUES (:orderId, :foodId, :quantity, :price, :totalAmount)";
                    $orderDetailStmt = $pdo->prepare($orderDetailSql);
                    $orderDetailStmt->execute([
                        ':orderId' => $orderId,
                        ':foodId' => $foodId,
                        ':quantity' => $quantity,
                        ':price' => $foodPrice,
                        ':totalAmount' => $totalPrice
                    ]);
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Order created successfully!";
            header("Location: adminOrder.php");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to create the order: " . $e->getMessage();
            header("Location: adminOrderC.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="../js/script.js"></script>
</head>
<body>
    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Create Order</h3>
            </div>
        </div>

        <div class="box">
            <form action="" method="post" enctype="multipart/form-data">
                <fieldset style="border: none;">
                    <!-- Order Details -->
                    <div class="inputBox">
                        <label for="studentId">Member ID:</label>
                        <input type="text" name="studentId" id="studentId" class="inputUser" required>
                    </div>

                    <div class="inputBox">
                        <label for="voucherId">Voucher Code:</label>
                        <input type="text" name="voucherId" id="voucherId" class="inputUser">
                    </div>

                    <div class="inputBox">
                        <label for="orderStatus">Order Status:</label>
                        <select name="orderStatus" id="orderStatus" class="inputUser">
                            <option value="Pending" selected>Pending</option>
                            <option value="Preparing">Preparing</option>
                            <option value="ReadyForPickup">Ready for Pickup</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="inputBox">
                        <label for="orderDate">Order Date:</label>
                        <input type="datetime-local" name="orderDate" id="orderDate" class="inputUser" required>
                    </div>

                    <div class="inputBox">
                        <label for="totalAmount">Total Amount (RM):</label>
                        <input type="text" name="totalAmount" id="totalAmount" class="inputUser" readonly value="0.00">
                    </div>

                    <!-- Order Item Details -->
                    <div class="inputBox">
                        <label for="foodItems">Order Items:</label>
                        <select name="foodItems[]" id="foodItems" class="inputUser" multiple required>
                            <?php
                                $foodQuery = "SELECT foodId, foodName, price FROM fooddetail WHERE status != 'Out of Stock'"; 
                                $stmt = $pdo->prepare($foodQuery);
                                $stmt->execute();
                                $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($foods as $food) {
                                    echo "<option value=\"{$food['foodId']}\" data-price=\"{$food['price']}\">{$food['foodName']} - RM {$food['price']}</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="inputBox">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity[]" class="inputUser" min="1" required>
                    </div>

                    <div class="button">
                        <button type="button" id="addFoodBtn" class="submit-btn">Add Food</button>
                    </div>

                    <div class="button">
                        <button type="submit" class="submit-btn">Create Order</button>
                        <button type="button" class="cancel-btn" onclick="window.location.href='adminOrder.php';">Cancel</button>
                    </div>
                </fieldset>
            </form>
        </div>

        <!-- Show Selected Items Below the Form -->
        <div class="order-summary">
            <h3>Food Selected</h3>
            <table>
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalOrderAmount = 0;
                    if (isset($_SESSION['orderItems'])) {
                        foreach ($_SESSION['orderItems'] as $item) {
                            $totalOrderAmount += $item['totalPrice'];
                            echo "<tr>
                                    <td>{$item['foodName']}</td>
                                    <td>RM {$item['price']}</td>
                                    <td>{$item['quantity']}</td>
                                    <td>RM " . number_format($item['totalPrice'], 2) . "</td>
                                  </tr>";
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>RM <?= number_format($totalOrderAmount, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('addFoodBtn').addEventListener('click', function() {
            const foodItemsSelect = document.getElementById('foodItems');
            const quantityInputs = document.querySelectorAll('input[name="quantity[]"]');
            
            const selectedFoodIds = Array.from(foodItemsSelect.selectedOptions).map(option => option.value);
            const quantities = Array.from(quantityInputs).map(input => input.value);

            if (selectedFoodIds.length > 0 && quantities.length > 0 && quantities.every(qty => qty > 0)) {
                const foodData = [];
                selectedFoodIds.forEach((foodId, index) => {
                    foodData.push({
                        foodId: foodId,
                        quantity: quantities[index]
                    });
                });

                const formData = new FormData();
                formData.append('action', 'add_food');
                formData.append('foodData', JSON.stringify(foodData));

                fetch('adminOrderC.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Food added to order!');
                        updateTotal();
                        displayOrderSummary();
                    }
                })
                .catch(error => console.error('Error adding food:', error));
            } else {
                alert('Please select a food item and enter a valid quantity.');
            }
        });

        function updateTotal() {
            let totalAmount = 0;
            const foodItemsSelect = document.getElementById('foodItems');
            const quantityInputs = document.querySelectorAll('input[name="quantity[]"]');

            const selectedFoodIds = Array.from(foodItemsSelect.selectedOptions).map(option => option.value);

            selectedFoodIds.forEach((foodId, index) => {
                const price = parseFloat(document.querySelector(`option[value="${foodId}"]`).textContent.split(' - RM ')[1]);
                const quantity = parseInt(quantityInputs[index].value);
                if (!isNaN(quantity) && quantity > 0) {
                    totalAmount += price * quantity;
                }
            });

            document.getElementById('totalAmount').value = totalAmount.toFixed(2);
        }

        function displayOrderSummary() {
            location.reload(); 
        }
    </script>
</body>
</html>
