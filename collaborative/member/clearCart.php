<?php
session_start();

// If form is submitted, clear session cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['cart']);

    // Output small script to clear sessionStorage and redirect
    echo "<script>
        sessionStorage.removeItem('cart');
        alert('Cart cleared successfully!');
        window.location.href = 'checkOut.php';
    </script>";
    exit;
}
?>