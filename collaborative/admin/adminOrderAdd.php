<?php
session_start();
include '../component/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_food') {
    $foodData = json_decode($_POST['foodData'], true);

    // Initialize the session variable if it doesn't exist
    if (!isset($_SESSION['orderItems'])) {
        $_SESSION['orderItems'] = [];
    }

    // Add selected food items to the session
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

    echo json_encode(['success' => true]);
    exit();
}
?>
