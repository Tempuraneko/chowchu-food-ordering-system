<?php
require '../component/connect.php';
require_once '../member/config.php';      // Include config.php for Stripe keys
require_once '../stripe-php-10.3.0/init.php';  // Include Stripe SDK

session_start();

// Check if the request has the necessary session_id from Stripe
if (!isset($_GET['session_id'])) {
    echo "Invalid request. No session ID provided.";
    exit;
}
$sessionId = $_GET['session_id'];

// Set the Stripe secret API key
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    // Retrieve the Stripe session to check payment status and details
    $session = \Stripe\Checkout\Session::retrieve($sessionId);

    // Ensure the payment was successful
    if ($session->payment_status == 'paid') {

        $customerId = $session->customer;
        
        // Check if necessary session data is available
        if (!isset($_SESSION['orderDetails'], $_SESSION['cartItems'])) {
            echo "Order details not found.";
            exit;
        }

        // Retrieve order details and cart items from session
        $orderDetails = $_SESSION['orderDetails'];
        $cartItems = $_SESSION['cartItems'];

        // Begin database transaction to insert order details
        $pdo->beginTransaction();

        // Insert the order into the 'orders' table
        $stmt = $pdo->prepare("INSERT INTO orders (studentId, voucherId, orderStatus, orderDate, subtotal, totalAmount, discountAmount) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $orderDetails['studentID'], 
            $orderDetails['voucherID'], 
            $orderDetails['orderStatus'], 
            $orderDetails['orderDate'], 
            $orderDetails['subtotal'], 
            $orderDetails['totalAmount'],
            $orderDetails['discountAmount']
        ]);

        // Get the order ID of the newly inserted order
        $orderID = $pdo->lastInsertId();
        if (!$orderID) {
            throw new Exception("Failed to retrieve order ID.");
        }

        // Insert each cart item into the 'order_detail' table
        foreach ($cartItems as $item) {
            $foodId = $item['foodId'];  // Food ID from the cart
            $quantity = $item['quantity'];
            $price = $item['foodPrice'];
            $itemTotal = $quantity * $price;

            // Fetch the corresponding food detail ID from 'fooddetail' table
            $stmt = $pdo->prepare("SELECT id FROM fooddetail WHERE foodId= ?");
            $stmt->execute([$foodId]);
            $foodDetail = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$foodDetail) {
                throw new Exception("Food ID not found: $foodId");
            }

            $actualFoodId = $foodDetail['id']; // Use the food detail id for insertion

            // Insert the item into 'order_detail'
            $stmt = $pdo->prepare("INSERT INTO order_detail (orderId, foodId, quantity, price, totalAmount) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$orderID, $actualFoodId, $quantity, $price, $itemTotal]);
        }

        // Insert payment details into the 'payment' table
        $paymentAmount = $session->amount_total / 100; 
        $paymentStatus = $session->payment_status;  
        $paymentMethod = $session->payment_method_types[0];
        $paymentDate = date('Y-m-d H:i:s');
        $transactionReference = $session->payment_intent;
        $voucherCode = $orderDetails['voucherID'] ?? null;
        $discountAmount = $_SESSION['orderDetails']['discountAmount'];
        // Insert payment information into 'payment' table
        $stmt = $pdo->prepare("INSERT INTO payment (orderId, studentID, paymentMethod, paymentStatus, paymentDate, amount, transactionReference, voucherCode, discountAmount) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $orderID, 
            $orderDetails['studentID'], 
            $paymentMethod, 
            $paymentStatus, 
            $paymentDate, 
            $paymentAmount, 
            $transactionReference, 
            $voucherCode, 
            $discountAmount
        ]);

        // Commit the transaction
        $pdo->commit();
        $_SESSION['orderId'] = $orderID;  


        // Update the voucher table to mark the voucher as used and set the collect date
    if ($voucherCode) {
        $stmt = $pdo->prepare("UPDATE voucher SET isUsed = 'Yes', collectDate = NOW() WHERE voucherID = ?");
        $stmt->execute([$voucherCode]);
    }
    
        // Clear session data
        unset($_SESSION['cart']);
        unset($_SESSION['cartItems']);
        unset($_SESSION['orderDetails']);

        echo "<html><head><script>
            // Clear cart in sessionStorage
            sessionStorage.removeItem('cart');
            sessionStorage.removeItem('cartItems');
            sessionStorage.removeItem('orderDetails');
            // Then redirect
            window.location.href = '../member/order_detail.php?orderID=" . $orderID . "';
        </script></head><body></body></html>";
        exit;

    } else {
        echo "Payment failed. Please try again.";
        exit;
    }
} catch (Exception $e) {
    echo "Error processing payment: " . $e->getMessage();
    exit;
}
?>
