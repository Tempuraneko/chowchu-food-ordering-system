<?php
require '../component/connect.php';
include '../component/header.php';

// Ensure that the user is logged in
if (isset($_SESSION['studentID'])) {
    $_user = $_SESSION;
} else {
    echo "<script>
            if (confirm('You need to log in first! Would you like to log in?')) {
                window.location.href = 'login.php';
            } else {
                window.location.href = '../member/index.php';  
            }
          </script>";
    exit;
}

// Get selected status from the request (default to 'all')
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 5; // 5 orders per page
$offset = ($page - 1) * $limit;

// Base query
$query = 'SELECT o.orderId, o.orderDate, o.totalAmount, o.orderStatus AS orderStatus, 
    oi.foodId, f.foodName, oi.quantity, oi.price AS itemPrice, 
    oi.totalAmount AS itemTotalPrice, pay.amount AS paymentAmount, f.foodImage
    FROM orders o
    LEFT JOIN order_detail oi ON o.orderId = oi.orderId
    LEFT JOIN fooddetail f ON oi.foodId = f.id
    LEFT JOIN payment pay ON o.orderId = pay.orderId
    WHERE o.studentId = ?';

$params = [$_user['studentID']];

// Add status filter if selected
if ($statusFilter !== 'all') {
    $query .= ' AND o.orderStatus = ?';
    $params[] = $statusFilter;
}

// Sort by orderId descending
$query .= ' ORDER BY o.orderId DESC';

// Add limit and offset for pagination
$query .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

// Prepare and execute
$stm = $pdo->prepare($query);
$stm->execute($params);
$result = $stm->fetchAll();

// Get total count for pagination
$countQuery = 'SELECT COUNT(DISTINCT o.orderId) AS total FROM orders o WHERE o.studentId = ?';
$countParams = [$_user['studentID']];
if ($statusFilter !== 'all') {
    $countQuery .= ' AND o.orderStatus = ?';
    $countParams[] = $statusFilter;
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalOrders = $countStmt->fetch()['total'];
$totalPages = ceil($totalOrders / $limit);

$_title = 'Track Order';
?>

<div class="track-order-container">

    <!-- Filter Form -->
    <div class="status-navbar">
        <a href="?status=all" class="<?php echo $statusFilter === 'all' ? 'active' : ''; ?>">All</a>
        <a href="?status=Pending" class="<?php echo $statusFilter === 'Pending' ? 'active' : ''; ?>">Pending</a>
        <a href="?status=Ready for Pickup" class="<?php echo $statusFilter === 'Ready for Pickup' ? 'active' : ''; ?>">Ready For Pickup</a>
        <a href="?status=Completed" class="<?php echo $statusFilter === 'Completed' ? 'active' : ''; ?>">Completed</a>
        <a href="?status=Cancelled" class="<?php echo $statusFilter === 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
    </div>

    <?php
    if (count($result) == 0) {
        echo "<script>
            alert('You have no order. Start Order now !!!');
            window.location.href = '../member/menu.php';  
        </script>";
       
    } else {
        $currentOrderId = null;
        foreach ($result as $row) {
            if ($currentOrderId !== $row['orderId']) {
                if ($currentOrderId !== null) {
                    echo '</div>'; // Close previous order-items
                    echo '</div>'; // Close previous order-card
                    echo '</a>';
                }
                // New order card
                echo '<a href="order_detail.php?orderId=' . htmlspecialchars($row['orderId']) . '" class="order-card-link">';
                echo '<div class="order-card">';
                echo '<div class="order-header">Order ID: ' . htmlspecialchars($row['orderId']) . '</div>';
                echo '<div class="order-info">';
                echo '<div>Order Date: ' . htmlspecialchars($row['orderDate']) . '</div>';
                echo '<div>Status: ' . htmlspecialchars($row['orderStatus']) . '</div>';
                echo '</div>';
                echo '<div class="order-items">';
                $currentOrderId = $row['orderId'];
            }

            // Display item
            echo '<div class="order-item">';
            echo '<img src="' . (!empty($row['foodImage']) ? "../uploaded_files/" . htmlspecialchars($row['foodImage']) : "../uploaded_files/default-image.jpg") . '" alt="Product Image">';
            echo '<div class="item-details">';
            echo '<strong>' . htmlspecialchars($row['foodName']) . '</strong>';
            echo '<span>' . htmlspecialchars($row['quantity']) . ' x RM' . htmlspecialchars(number_format($row['itemPrice'], 2)) . '</span>';
            echo '</div>';
            echo '</div>';

            // Check if next item is different order
            $nextRow = next($result);
            if (!$nextRow || $currentOrderId !== $nextRow['orderId']) {
                echo '<div class="order-total">';
                echo '<div>Total Amount: RM ' . htmlspecialchars(number_format($row['totalAmount'], 2)) . '</div>';
                echo '<div>Payment Amount: RM ' . htmlspecialchars(number_format($row['paymentAmount'], 2)) . '</div>';
                echo '</div>';
            }
            if ($nextRow) {
                prev($result); // Move back pointer
            }
        }
        // Close last order card
        if ($currentOrderId !== null) {
            echo '</div>'; // Close last order-items
            echo '</div>'; // Close last order-card
            echo '</a>';
        }
    }
    ?>

    <!-- Pagination Links -->
    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?status=<?php echo urlencode($statusFilter); ?>&page=<?php echo $page - 1; ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php
    // Display first 5 pages, and then show a "Next" with "..." for others
    $range = 5;
    $startPage = max(1, $page - floor($range / 2));
    $endPage = min($totalPages, $startPage + $range - 1); 
    
    // Show first page
    if ($startPage > 1) {
        echo '<a href="?status=' . urlencode($statusFilter) . '&page=1">1</a>';
        if ($startPage > 2) echo '<span>...</span>';
    }

    // Display pages within the range
    for ($i = $startPage; $i <= $endPage; $i++) {
        echo '<a href="?status=' . urlencode($statusFilter) . '&page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
    }

    // Show last page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) echo '<span>...</span>';
        echo '<a href="?status=' . urlencode($statusFilter) . '&page=' . $totalPages . '">' . $totalPages . '</a>';
    }
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?status=<?php echo urlencode($statusFilter); ?>&page=<?php echo $page + 1; ?>">Next &raquo;</a>
    <?php endif; ?>
</div>


</div>

<?php include '../component/footer.php'; ?>
