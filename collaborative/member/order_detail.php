<?php
require '../component/connect.php';
require '../component/header.php';
?>


<?php

if (isset($_SESSION['studentID'])) {
    $_user = $_SESSION; 
} else {
    echo "User is not logged in.";
    exit;
}

$orderID = $_GET['orderId'] ?? $_SESSION['orderId'] ?? null;

if (!$orderID) {
    echo "Order ID is required.";
    exit;
}

$stmt = $pdo->prepare('
    SELECT 
        o.orderId, 
        o.orderDate, 
        o.totalAmount, 
        o.discountAmount, 
        o.orderStatus AS orderStatus, 
        oi.foodId, 
        f.foodName, 
        oi.quantity, 
        oi.price AS itemPrice, 
        oi.totalAmount AS itemTotalPrice, 
        IFNULL(pay.amount, 0) AS totalAmount, 
        IFNULL(pay.transactionReference, "") AS transactionReference,
        IFNULL(pay.paymentMethod, "") AS paymentMethod,
        IFNULL(pay.paymentStatus, "") AS paymentStatus,
        IFNULL(pay.paymentDate, "") AS paymentDate,
        f.foodImage
    FROM orders o
    LEFT JOIN order_detail oi ON o.orderId = oi.orderId
    LEFT JOIN fooddetail f ON oi.foodId = f.id
    LEFT JOIN payment pay ON o.orderId = pay.orderId
    WHERE o.orderId = ?
');
$stmt->execute([$orderID]);
$orderDetails = $stmt->fetchAll();

if (!$orderDetails) {
    echo "Order not found.";
    exit;
}

$order = $orderDetails[0]; 

$reviewCheckStmt = $pdo->prepare('
    SELECT * FROM review WHERE orderId = ? AND studentId = ?
');
$reviewCheckStmt->execute([$orderID, $_user['studentID']]);
$existingReview = $reviewCheckStmt->fetch();

$showViewReviewButton = ($existingReview) ? true : false;

// Calculate the time elapsed since the order was created
$orderDate = new DateTime($order['orderDate']);
$currentTime = new DateTime();
$timeElapsed = $currentTime->diff($orderDate);  // Calculate the time difference

// Update the order status based on time elapsed
if ($order['orderStatus'] === 'Pending' && ($timeElapsed->h * 60 + $timeElapsed->i) >= 3) {
    // Change to 'Preparing' after 3 minutes
    $stmt = $pdo->prepare('UPDATE orders SET orderStatus = "Preparing" WHERE orderId = ?');
    $stmt->execute([$orderID]);
} elseif ($order['orderStatus'] === 'Preparing' && ($timeElapsed->h * 60 + $timeElapsed->i) >= 10) {
    // Change to 'Ready for Pickup' after 10 minutes
    $stmt = $pdo->prepare('UPDATE orders SET orderStatus = "Ready for Pickup" WHERE orderId = ?');
    $stmt->execute([$orderID]);
} elseif ($order['orderStatus'] === 'Ready for Pickup' && ($timeElapsed->h * 60 + $timeElapsed->i) >= 15) {
    // Change to 'Completed' after 15 minutes
    $stmt = $pdo->prepare('UPDATE orders SET orderStatus = "Completed" WHERE orderId = ?');
    $stmt->execute([$orderID]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details for Order #<?= htmlspecialchars($order['orderId']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome icons -->
    <style>
    /* Progress Bar Styling */
    .order-status-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .order-status-step {
        width: 25%;
        text-align: center;
        font-size: 16px;
    }

    .order-status-step.active {
        color: green;
    }

    .order-status-step.inactive {
        color: lightgray;
    }

    .order-status-step i {
        font-size: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }

    .button-container {
        display: flex;
        justify-content: flex-start; 
        gap: 20px; 
        flex-wrap: wrap; 
    }

    .order-action-btn {
        padding: 12px 25px; 
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 180px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .cancel-btn {
        background-color: #dc3545;
        color: white;
    }

    .rate-btn {
        background-color: #ffc107;
        color: black;
    }

    .receipt-btn {
        background-color: #17a2b8; 
        color: white;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s ease-in-out; 
    }

    /* Button Hover Effect */
    .receipt-btn:hover {
        background-color: #138496; 
        transform: scale(1.05); 
    }

    /* Button Focus Effect */
    .receipt-btn:focus {
        outline: none; 
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
    }

    .review-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-width: 90%;
    max-height: 80%;
    overflow: hidden;
    overflow-y: auto;
    width: 80vw;
    height: 70vh;
}

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.review-media-container {
    max-height: 50vh;
    overflow-y: auto;
    margin-top: 15px;
    padding-right: 10px;
    display: flex;
    flex-direction: column;
}

.review-media-container img {
    max-width: 100%;
    height: auto;
    margin: 5px 0;
    object-fit: contain;
}

@media (max-width: 768px) {
    .review-modal {
        width: 95vw;
        height: 80vh;
    }

    .review-media-container {
        max-height: 60vh;
    }
}

@media (max-width: 480px) {
    .review-modal {
        width: 95vw;
        height: 85vh;
    }

    .review-media-container {
        max-height: 70vh;
    }
}

    </style>
</head>
<body>

<div class="order-container">
    <h2>Order Details for Order #<?= htmlspecialchars($order['orderId']) ?></h2>

    <!-- Order Status -->
    <div class="order-status-container">
        <!-- Pending -->
        <div class="order-status-step <?= strtolower($order['orderStatus']) === 'pending' ? 'active' : 'inactive' ?>">
            <i class="fa fa-hourglass-start"></i><br>Pending
        </div>
        <!-- Preparing -->
        <div class="order-status-step <?= strtolower($order['orderStatus']) === 'preparing' ? 'active' : 'inactive' ?>">
            <i class="fa fa-cogs"></i><br>Preparing
        </div>
        <!-- Ready for Pickup -->
        <div class="order-status-step <?= strtolower($order['orderStatus']) === 'ready for pickup' ? 'active' : 'inactive' ?>">
            <i class="fa fa-truck"></i><br>Ready for Pickup
        </div>
        <!-- Completed -->
        <div class="order-status-step <?= strtolower($order['orderStatus']) === 'completed' ? 'active' : 'inactive' ?>">
            <i class="fa fa-check-circle"></i><br>Completed
        </div>
        <!-- Cancelled (New addition) -->
        <div class="order-status-step <?= strtolower($order['orderStatus']) === 'cancelled' ? 'active' : 'inactive' ?>">
            <i class="fa fa-times-circle"></i><br>Cancelled
        </div>
    </div>


    <!-- Order Items -->
    <div class="order-items">
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderDetails as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['foodImage'])): ?>
                                <img src="../uploaded_files/<?= htmlspecialchars($item['foodImage']) ?>" alt="<?= htmlspecialchars($item['foodName']) ?>" width="50" height="50">
                            <?php else: ?>
                                No image available
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['foodName']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($item['itemPrice'], 2)) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($item['itemTotalPrice'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Payment Details -->
    <div class="payment-details">
        <h3>Payment Details</h3>
        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transactionReference']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['paymentMethod']) ?></p>
        <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['paymentStatus']) ?></p>
        <p><strong>Payment Date:</strong> <?= htmlspecialchars($order['paymentDate']) ?></p>
        <p><strong>Discount Amount:</strong> RM <?= htmlspecialchars(number_format($order['discountAmount'], 2)) ?></p>
        <p><strong>Total Amount:</strong> RM <?= htmlspecialchars(number_format($order['totalAmount'], 2)) ?></p>
    </div>

    <!-- Order Actions -->
    <div class="button-container">
        <?php
        $orderStatus = strtolower($order['orderStatus']);
        if ($orderStatus === 'pending') : ?>
            <button class="order-action-btn cancel-btn" id="cancelOrderBtn" data-order-id="<?= htmlspecialchars($order['orderId']) ?>">
                Cancel Order
            </button>

        <?php elseif ($orderStatus === 'preparing' || $orderStatus === 'ready for pickup') : ?>
            <div class="order-status-only">
                <strong>Status:</strong> <?= htmlspecialchars(ucwords(str_replace('readyforpickup', 'Ready for Pickup', $orderStatus))) ?>
            </div>
        <?php elseif ($orderStatus === 'completed') : ?>
            <?php if ($existingReview): ?>
                <button id="viewReviewBtn" class="order-action-btn rate-btn">View Your Review</button>
            <?php else: ?>
                <form method="post" action="review.php" class="button-form">
                    <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderID) ?>">
                    <button type="submit" class="order-action-btn rate-btn">Rate</button>
                </form>
            <?php endif; ?>
            <a href="../member/invoice.php?orderId=<?= htmlspecialchars($order['orderId']) ?>" target="_blank" class="order-action-btn receipt-btn">Generate Receipt</a>
        <?php endif; ?>
    </div>

    <?php if ($existingReview): ?>
    <div class="review-modal" id="reviewModal">
        <h3>Your Review</h3>
        <p><strong>Rating:</strong> <?= str_repeat("&#9733;", $existingReview['rating']) . str_repeat("&#9734;", 5 - $existingReview['rating']) ?></p>
        <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($existingReview['comment'])) ?></p>
        
        <?php if (!empty($existingReview['reviewMedia'])): ?>
            <h3>Review Media:</h3>
                <div style="position: relative; max-width: 500px; margin: auto;">
                    <div id="carousel" style="text-align: center;">
                        <?php
                        $mediaFiles = explode(',', $existingReview['reviewMedia']);
                        foreach ($mediaFiles as $index => $file) {
                            $display = $index === 0 ? 'block' : 'none';
                            echo '<img class="carousel-image" src="../uploads/' . htmlspecialchars(trim($file)) . '" alt="Review Media" style="display: ' . $display . '; width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px;">';
                        }
                        ?>
                    </div>
                    <!-- Navigation Arrows -->
                    <button onclick="changeSlide(-1)" style="position: absolute; top: 45%; left: 0; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; padding: 10px;">&#10094;</button>
                    <button onclick="changeSlide(1)" style="position: absolute; top: 45%; right: 0; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; padding: 10px;">&#10095;</button>
                </div>
        <?php endif; ?>

        <form id="deleteReviewForm" method="POST" action="reviewcomment_delete.php">
            <input type="hidden" name="reviewId" value="<?= htmlspecialchars($existingReview['reviewId']) ?>">
            <button type="submit" name="deleteReview">Delete Review</button>
        </form>


        <button onclick="closeModal()">Close</button>
    </div>
    <div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
<?php endif; ?>

</div>
 
<?php require '../component/footer.php'; ?>

<script>
    document.getElementById('viewReviewBtn')?.addEventListener('click', function() {
    const reviewModal = document.getElementById('reviewModal');
    const modalOverlay = document.getElementById('modalOverlay');
    
    if (reviewModal && modalOverlay) {
        reviewModal.style.display = 'block';
        modalOverlay.style.display = 'block';
    }
});

// Close review modal
function closeModal() {
    const reviewModal = document.getElementById('reviewModal');
    const modalOverlay = document.getElementById('modalOverlay');

    if (reviewModal && modalOverlay) {
        reviewModal.style.display = 'none';
        modalOverlay.style.display = 'none';
    }
}
</script>


<script>
$(document).ready(function () {
    $('#cancelOrderBtn').click(function () {
        var orderId = $(this).data('order-id'); 

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to cancel this order?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX POST request to cancel order
                $.post('../member/cancel_order.php', { orderId: orderId }, function (response) {
                    if (response.trim() === 'success') {
                        Swal.fire(
                            'Cancelled!',
                            'Your order has been cancelled successfully.',
                            'success'
                        ).then(() => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to cancel the order. Please try again.',
                            'error'
                        );
                    }
                }).fail(function () {
                    Swal.fire(
                        'Error!',
                        'Something went wrong with the server.',
                        'error'
                    );
                });
            }
        });
    });
});
</script>

<script>
document.getElementById('deleteReviewForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission

    if (confirm('Are you sure you want to delete this review?')) {
        var formData = new FormData(this);

        fetch('reviewcomment_delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                alert('Review deleted successfully!');
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Error occurred while deleting the review.');
            }
        })
        .catch(error => {
            alert('Error: ' + error); // Handle AJAX errors
        });
    }
});
</script>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-image');

function changeSlide(direction) {
    slides[currentSlide].style.display = 'none';
    currentSlide = (currentSlide + direction + slides.length) % slides.length;
    slides[currentSlide].style.display = 'block';
}
</script>

</body>
</html>


