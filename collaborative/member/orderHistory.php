<?php
require '../component/connect.php';
include '../component/header.php';

if (isset($_SESSION['studentID'])) {
    $_user = $_SESSION; 
} else {
    echo "User is not logged in.";
    exit;
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 5; // 5 orders per page
$offset = ($page - 1) * $limit;

// Base query to fetch only completed orders
$query = 'SELECT o.orderId, o.orderDate, o.totalAmount, o.orderStatus AS orderStatus, 
    oi.foodId, f.foodName, oi.quantity, oi.price AS itemPrice, 
    oi.totalAmount AS itemTotalPrice, pay.amount AS paymentAmount, f.foodImage
    FROM orders o
    LEFT JOIN order_detail oi ON o.orderId = oi.orderId
    LEFT JOIN fooddetail f ON oi.foodId = f.id
    LEFT JOIN payment pay ON o.orderId = pay.orderId
    WHERE o.studentId = ? AND o.orderStatus = "Completed"';

$params = [$_user['studentID']];

// Sort by orderId descending
$query .= ' ORDER BY o.orderId DESC';

// Add limit and offset for pagination
$query .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

// Prepare and execute
$stm = $pdo->prepare($query);
$stm->execute($params);
$result = $stm->fetchAll();

// Get total count for pagination
$countQuery = 'SELECT COUNT(DISTINCT o.orderId) AS total FROM orders o WHERE o.studentId = ? AND o.orderStatus = "Completed"';
$countParams = [$_user['studentID']];
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalOrders = $countStmt->fetch()['total'];
$totalPages = ceil($totalOrders / $limit);

$_title = 'Completed Orders';
?>

<div class="main-content-wrapper">

<div class="account-container">
    <aside class="sidebar">
  <div class="profile-pic">
  <img src="../<?php echo htmlspecialchars($student['profileImage']); ?>" alt="User Icon" />
  <p><?php echo htmlspecialchars($student['name']); ?></p>
</div>
      <nav class="nav-menu">
        <ul>
          <li><img src="../images/account.png" alt=""><a href="../member/account.php" class="none">My Account</a></li>
          <li><img src="../images/order.png" alt=""> <a href="../member/orderHistory.php" class="none">Order History</a></li>
        </ul>
      </nav>
    </aside>
</div>

<div class="track-order-container">

    <!-- Title -->
    <h1>Order History</h1> 

    <?php
    if (count($result) == 0) {
        echo "<p>You have no past orders. Start shopping now!</p>";
    } else {
        $currentOrderId = null;
        foreach ($result as $row) {
            if ($currentOrderId !== $row['orderId']) {
                if ($currentOrderId !== null) {
                    echo '</div>'; 
                    echo '</div>'; 
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

            echo '<div class="order-item">';
            echo '<img src="' . (!empty($row['foodImage']) ? "../uploaded_files/" . htmlspecialchars($row['foodImage']) : "../uploaded_files/default-image.jpg") . '" alt="Product Image">';
            echo '<div class="item-details">';
            echo '<strong>' . htmlspecialchars($row['foodName']) . '</strong>';
            echo '<span>' . htmlspecialchars($row['quantity']) . ' x RM' . htmlspecialchars(number_format($row['itemPrice'], 2)) . '</span>';
            echo '</div>';
            echo '</div>';

            $nextRow = next($result);
            if (!$nextRow || $currentOrderId !== $nextRow['orderId']) {
                echo '<div class="order-total">';
                echo '<div>Total Amount: RM ' . htmlspecialchars(number_format($row['totalAmount'], 2)) . '</div>';
                echo '<div>Payment Amount: RM ' . htmlspecialchars(number_format($row['paymentAmount'], 2)) . '</div>';
                echo '</div>';
            }
            if ($nextRow) {
                prev($result); 
            }
        }
        // Close last order card
        if ($currentOrderId !== null) {
            echo '</div>'; 
            echo '</div>'; 
            echo '</a>';
        }
    }
    ?>

    <!-- Pagination Links -->
    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php
    $range = 5;
    $startPage = max(1, $page - floor($range / 2));
    $endPage = min($totalPages, $startPage + $range - 1); 
    
    // Show first page
    if ($startPage > 1) {
        echo '<a href="?page=1">1</a>';
        if ($startPage > 2) echo '<span>...</span>';
    }

    // Display pages within the range
    for ($i = $startPage; $i <= $endPage; $i++) {
        echo '<a href="?page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
    }

    // Show last page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) echo '<span>...</span>';
        echo '<a href="?page=' . $totalPages . '">' . $totalPages . '</a>';
    }
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

</div>
</div>
<?php include '../component/footer.php'; ?>
