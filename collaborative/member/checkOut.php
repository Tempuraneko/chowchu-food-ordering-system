<?php
require '../component/connect.php';
require '../component/header.php';

$studentID = $_SESSION['studentID'] ?? null;
$selectedVoucherID = $_GET['voucherID'] ?? null;
$availableVouchers = [];

// Retrieve cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$_SESSION['cart'] = $cartItems; 

$total = 0;

// Calculate the total cost
foreach ($cartItems as $item) {
    $total += $item['foodPrice'] * $item['quantity'];
}

// Calculate subtotal, tax, and total amount
$subtotal = $total;
$tax = $total * 0.06;  

$discountAmount = 0.00;

if ($studentID) {
    // Load available vouchers
    $stmt = $pdo->prepare("
        SELECT v.voucherID, p.type, p.discountAmount, p.description
        FROM voucher v
        JOIN promotion p ON v.promoID = p.promoID
        WHERE v.studentID = :studentID AND v.isUsed = 'No'
    ");
    $stmt->execute([':studentID' => $studentID]);
    $availableVouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If voucher is selected, get its discount amount
    if ($selectedVoucherID) {
        $discountStmt = $pdo->prepare("
            SELECT p.discountAmount
            FROM voucher v
            JOIN promotion p ON v.promoID = p.promoID
            WHERE v.voucherID = :voucherID AND v.studentID = :studentID AND v.isUsed = 'No'
        ");
        $discountStmt->execute([
            ':voucherID' => $selectedVoucherID,
            ':studentID' => $studentID
        ]);

        $voucher = $discountStmt->fetch(PDO::FETCH_ASSOC);
        if ($voucher) {
            $discountAmount = floatval($voucher['discountAmount']);
        }
    }
}

$totalAmountAfterDiscount = max(($subtotal - $discountAmount + $tax), 0);


$_SESSION['voucherID'] = $selectedVoucherID; 


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="order-container">
        <h2>Order Summary</h2>
        <?php if (!empty($cartItems)): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Food</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($cartItems as $index => $item): ?>
                        <tr data-index="<?php echo $index; ?>">
                            <td>
                                <div class="food-info">
                                    <img src="<?php echo $item['foodImage']; ?>" alt="Food Image">
                                    <div>
                                        <strong style="display: none"><?php echo $item['foodId']; ?></strong>
                                        <strong><?php echo $item['foodName']; ?></strong>
                                        <small><?php echo $item['foodDetail']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="checkout-item-quantity">
                                    <!-- <button class="decrease-quantity" data-index="<?php echo $index; ?>">-</button> -->
                                    <input type="text" value="<?php echo $item['quantity']; ?>" readonly>
                                    <!-- <button class="increase-quantity" data-index="<?php echo $index; ?>">+</button> -->
                                    <input type="hidden" name="cartData" id="cart-data">

                                </div>
                            </td>
                            <td>RM <?php echo number_format($item['foodPrice'], 2); ?></td>
                            <td>RM <span class="item-subtotal"><?php echo number_format($item['foodPrice'] * $item['quantity'], 2); ?></span></td>
                            <td>
                                <a href="../member/removeCart.php?remove=<?php echo $index; ?>" class="remove-item">&#10005;</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>

        <div class="order-summary">
            <div class="voucher-box">
                <label for="voucher-select">Select Voucher:</label>
                <select name="voucherID" id="voucher-select" onchange="onVoucherChange()">
                    <option value="">-- No Voucher --</option>
                    <?php foreach ($availableVouchers as $voucher): ?>
                        <option value="<?= $voucher['voucherID'] ?>"
                            <?= ($selectedVoucherID == $voucher['voucherID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($voucher['type']) ?> - RM<?= number_format($voucher['discountAmount'], 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="hidden" name="voucherID" value="<?= htmlspecialchars($selectedVoucherID) ?>">

            </div>

             <!-- Changed-->
             <div class="discount-row">
                <span>Discount:</span>
                <span>- RM <?php echo number_format($discountAmount, 2); ?></span>
                <input type="hidden" id="discount-amount" value="<?php echo $discountAmount; ?>">
            </div>

            <div class="discount-row">
                <span>Subtotal:</span>
                <span id="subtotal-amount">RM 0.00</span>
            </div>


            <!-- Tax Row -->
            <div class="discount-row">
                <span>Tax (6%):</span>
                <span id="tax-amount">RM <?= number_format($tax, 2); ?></span>
            </div>

            <!-- Changed-->
            <div class="total-row">
                <span>Total:</span>
                <span class="total-amount">
                    <span id="total-amount"><?php echo number_format($totalAmountAfterDiscount, 2); ?></span>
                </span>
            </div>

            <div class="checkout-btn">
                <form action="../member/processOrder.php" method="post" id="order-form">
                    <!-- <input type="hidden" name="cartData" id="cart-data">
                    <input type="hidden" name="subtotal" id="hidden-subtotal">
                    <input type="hidden" name="tax" id="hidden-tax">
                    <input type="hidden" name="totalAmount" id="hidden-total"> -->

                    <button class="stripe-button" id="proceedToPayment" type="submit" name="place_order">
                        <div class="spinner hidden" id="spinner"></div>
                        <span id="proceedToPayment">Proceed to Payment</span>
                    </button>
                </form>
            </div>

            <form method="post" action="../member/clearCart.php" onsubmit="clearSessionStorage()">
                <button type="submit" name="clear_all" class="clear-cart">Clear Cart</button>
            </form>

        </div>
    </div>

    <?php include '../component/footer.php'; ?>

</body>

</html>

<script>

    function onVoucherChange() {
        const selectedVoucherID = document.getElementById('voucher-select').value;
        const url = new URL(window.location.href);
        if (selectedVoucherID) {
            url.searchParams.set('voucherID', selectedVoucherID);
        } else {
            url.searchParams.delete('voucherID');
        }
        window.location.href = url.toString();
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!$studentID): ?>
            Swal.fire({
                icon: 'error',
                title: 'You need to log in first!',
                text: 'Please log in to proceed with the payment.',
            }).then(() => {
                
                window.location.href = 'login.php';
            });
        <?php endif; ?>

        const proceedButton = document.getElementById('proceedToPayment');
        proceedButton.addEventListener('click', function (event) {
            event.preventDefault();

            let cartItems = JSON.parse(sessionStorage.getItem("cart") || '[]');

            if (cartItems.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cart is empty!',
                    text: 'You cannot proceed with an empty cart.',
                });
            } else {
                Swal.fire({
                    title: 'Confirm your order',
                    text: 'Are you sure you want to proceed to payment?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'No, cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('order-form').submit();
                    }
                });
            }
        });
    });
</script>

