<?php
require '../component/connect.php';

require_once '../member/config.php';     
require_once '../stripe-php-10.3.0/init.php';  

session_start();

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$studentID = $_SESSION['studentID'] ?? null;
$voucherID = $_SESSION['voucherID'] ?? null;

if (!$studentID || empty($cartItems)) {
    echo "Invalid request. No student ID or empty cart.";
    exit;
}

// Calculate subtotal
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['foodPrice'] * $item['quantity'];
}

// Apply discount if there's a voucher
$discountAmount = 0;
if ($voucherID) {
    $stmt = $pdo->prepare("SELECT p.discountAmount FROM voucher v JOIN promotion p ON v.promoID = p.promoID WHERE v.voucherID = ?");
    $stmt->bindParam(1, $voucherID, PDO::PARAM_STR);
    $stmt->execute();
    $discountAmount = $stmt->fetchColumn();
}

$discountedSubtotal = max($subtotal - $discountAmount, 0);  

$tax = $subtotal * 0.06;  
$tax = round($tax, 2);

// Ensure the final total is rounded correctly
$finalAmount = round($discountedSubtotal + $tax, 2);

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY); 

$coupon = null;

if ($discountAmount > 0) {
    // Create Stripe coupon if there's a discount
    $coupon = \Stripe\Coupon::create([
        'amount_off' => (int)($discountAmount * 100), 
        'currency' => 'myr',
        'duration' => 'once',
    ]);
}

// Prepare line items for Stripe
$lineItems = array_merge(
    array_map(function ($item) {
        return [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item['foodName'],
                ],
                'unit_amount' => round($item['foodPrice'] * 100),
            ],
            'quantity' => (int)$item['quantity'],
        ];
    }, $cartItems),

    [[
        'price_data' => [
            'currency' => 'myr',
            'product_data' => [
                'name' => 'Tax (6%)',
            ],
            'unit_amount' => round($tax * 100),
        ],
        'quantity' => 1,
    ]]
);

// Create a Stripe Checkout Session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $lineItems,
    'discounts' => $discountAmount > 0 ? [['coupon' => $coupon->id]] : [], 
    'mode' => 'payment',
    'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => STRIPE_CANCEL_URL,
    'customer_email' => $_SESSION['customer_email'] ?? null,
    'metadata' => [
    'subtotal' => $subtotal,
    'discount' => $discountAmount,
    'tax' => $tax,
    'totalAmount' => $finalAmount,
    ],
]);

// Store the order data in the session
$_SESSION['cartItems'] = $cartItems;
$_SESSION['orderDetails'] = [
    'studentID' => $studentID,
    'orderStatus' => 'Pending',
    'orderDate' => date('Y-m-d H:i:s'),
    'voucherID' => $voucherID,
    'subtotal' => $subtotal,
    'discountAmount' => $discountAmount,
    'tax' => $tax,
    'totalAmount' => $finalAmount,
];

// Redirect to the Stripe Checkout page
header("Location: " . $session->url);
exit;
?>
