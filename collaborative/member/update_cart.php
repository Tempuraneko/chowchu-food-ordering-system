<?php
session_start();

// Check if the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get the foodID and action (increase/decrease)
$foodID = $_POST['foodID'];
$action = $_POST['action'];

// Find the item in the cart
foreach ($_SESSION['cart'] as &$item) {
    if ($item['foodID'] == $foodID) {
        if ($action == 'increase') {
            $item['quantity']++;
        } elseif ($action == 'decrease' && $item['quantity'] > 1) {
            $item['quantity']--;
        }
        break;
    }
}

// Redirect back to the previous page (cart page or wherever you want to show the updated cart)
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>
