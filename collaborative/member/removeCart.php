<?php
session_start();

$cartWasEmpty = false;

if (isset($_GET['remove'])) {
    $index = $_GET['remove'];

    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
        $cartWasEmpty = true;
    }
} else {
    echo "No item to remove.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updating cart...</title>
    <script>
        // Get current cart from sessionStorage
        let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

        // Remove item from sessionStorage cart by index
        const indexToRemove = <?php echo json_encode($_GET['remove']); ?>;
        cart.splice(indexToRemove, 1);

        // Save updated cart or clear if empty
        if (cart.length === 0) {
            sessionStorage.removeItem("cart");
        } else {
            sessionStorage.setItem("cart", JSON.stringify(cart));
        }

        // Redirect back to cart page
        window.location.href = "checkOut.php";
    </script>
</head>
<body>
    <p>Updating cart...</p>
</body>
</html>
