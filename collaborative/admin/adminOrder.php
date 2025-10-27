<?php
require '../component/admin_sidebar.php';
include '../component/connect.php';

// Pagination Settings
$orderPage = 10;
$totalQuery = $pdo->query('SELECT COUNT(*) FROM orders');
$totalOrder = $totalQuery->fetchColumn();
$totalPages = ceil($totalOrder / $orderPage);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $totalPages));

$start = ($page - 1) * $orderPage;

// Get status filter from URL (if any)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

// Fields for the table
$fields = [
    'orderId' => 'Order ID',
    'studentId' => 'Member ID',
    'voucherId' => 'Voucher Code',
    'orderStatus' => 'Order Status',
    'orderDate' => 'Order Date',
    'subtotal' => 'Subtotal',
    'totalAmount' => 'Total Amount',
    'discountAmount' => 'Discount Amount',
    'orderCreatedAt' => 'Time',
];

// Sorting logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;
$dir = isset($_GET['dir']) && in_array($_GET['dir'], ['asc', 'desc']) ? $_GET['dir'] : 'asc';

$query = "SELECT * FROM orders";

// Apply the status filter if present
if ($statusFilter) {
    $query .= " WHERE orderStatus = :status";
}

// Apply sorting if needed
if ($sort && array_key_exists($sort, $fields)) {
    $query .= " ORDER BY $sort $dir";
}

$query .= " LIMIT :start, :orderPage";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
if ($statusFilter) {
    $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
}
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':orderPage', $orderPage, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<body>
   <div class="product-table-container">
      <h1>Customer Orders</h1>

      <div class="search-filter-bar">
        <div class="search-bar">
            <i class="fa fa-search"></i>
            <form action="adminOrderS.php" method="post">
                <div class="input-container">
                    <input type="text" name="searchQuery" placeholder="Search order ID, member id, status, or address id..." value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>">
                    <button type="submit" name="search"></button>
                </div>
            </form>
        </div>

        <div class="action-icons">
            <i class="fa fa-filter" id="filter-btn"></i>
            <a href="adminOrderC.php">
                <i class="fa fa-plus"></i>
            </a>
        </div>

        <div class="filter-category">
            <form action="adminOrderF.php" method="post">
                <select name="monthCategory" id="categorySelect">
                    <option value="">All Months</option>
                    <?php foreach (range(1, 12) as $month): ?>
                        <option value="<?php echo $month; ?>" <?php echo isset($filterMonth) && $filterMonth == $month ? 'selected' : ''; ?>>
                            <?php echo date("F", mktime(0, 0, 0, $month, 1)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Submit</button>
            </form>
        </div>
        </div>

      <table class="product-table">
      <thead>
            <tr>
                <?php foreach ($fields as $field => $label): ?>
                    <th>
                        <a href="?page=<?= $page ?>&sort=<?= $field ?>&dir=<?= ($sort == $field && $dir == 'asc') ? 'desc' : 'asc' ?>">
                            <?= $label ?>
                            <?php if ($sort == $field): ?>
                                <span><?= $dir == 'asc' ? '↑' : '↓' ?></span>
                            <?php endif; ?>
                        </a>
                        <?php if ($sort == $field): ?>
                            <a href="?page=<?= $page ?>" style="text-decoration: none; color: red;">&times;</a>
                        <?php endif; ?>
                    </th>
                <?php endforeach; ?>
                <th>Action</th>
                <th>
                    <input type="checkbox" id="select-all">
                    <button type="button" onclick="confirmDelete()">
                        <i class="fas fa-trash fa-lg text-white" style="color:rgb(220, 38, 38)"></i>
                    </button>
                </th>
            </tr>
        </thead>
         <tbody>
            <?php
                if ($orders && count($orders) > 0) {
                    foreach ($orders as $row) {
            ?>
            <tr>
            <td><?php echo $row["orderId"]; ?></td>
            <td><?php echo $row["studentId"]; ?></td>
            <td><?php echo $row["voucherId"]; ?></td>
            <td>
                <select name="orderStatus" onchange="updateStatus('<?php echo $row['orderId']; ?>', this.value)">
                    <option value="Pending" <?php echo $row['orderStatus'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Preparing" <?php echo $row['orderStatus'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="ReadyForPickup" <?php echo $row['orderStatus'] == 'ReadyForPickup' ? 'selected' : ''; ?>>ReadyForPickup</option>
                    <option value="Completed" <?php echo $row['orderStatus'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $row['orderStatus'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </td>
            <td><?php echo $row["orderDate"]; ?></td>
            <td>RM <?php echo number_format($row["subtotal"], 2); ?></td>
            <td>RM <?php echo number_format($row["totalAmount"], 2); ?></td> 
             
            <td>RM <?php echo number_format($row["discountAmount"], 2); ?></td>
            <td><?php echo $row["orderCreatedAt"]; ?></td>  
            <td>
                <a href="adminOrderU.php?orderId=<?php echo $row['orderId'] ?>">
                    <i class="fas fa-edit" style="color:blue"></i>
                </a>
            </td>
            <td align="center">
                <input type="checkbox" name="deleteId[]" value="<?php echo $row['orderId']; ?>">
            </td>
        </tr>
            <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>No orders found.</td></tr>";
                }
            ?>
         </tbody>
      </table>

      <!-- Pagination -->
      <div class="pagination">
          <?php if ($page > 1): ?>
              <a href="?status=<?php echo urlencode($statusFilter); ?>&page=<?php echo $page - 1; ?>">&laquo; Prev</a>
          <?php endif; ?>

          <?php
          $range = 5;
          $startPage = max(1, $page - floor($range / 2));
          $endPage = min($totalPages, $startPage + $range - 1);

          if ($startPage > 1) {
              echo '<a href="?status=' . urlencode($statusFilter) . '&page=1">1</a>';
              if ($startPage > 2) echo '<span>...</span>';
          }

          for ($i = $startPage; $i <= $endPage; $i++) {
              echo '<a href="?status=' . urlencode($statusFilter) . '&page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
          }

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

   <script>
        function confirmDelete() {

        var selectedIds = [];
        $('input[name="deleteId[]"]:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire(
                'Error!',
                'Please select at least one order to delete.',
                'error'
            );
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to undo this action!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {

                $.post('adminOrderD.php', { deleteId: selectedIds }, function (data) {
                    if (data.trim() === 'success') {
                        Swal.fire(
                            'Deleted!',
                            'The selected orders have been deleted.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            `Failed to delete orders: ${data}`,
                            'error'
                        );
                    }
                }).fail(function () {
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                });
            }
        });
    }

   </script>

   <!-- update status -->
    <script>
        function updateStatus(orderId, status) {
            $.ajax({
                url: 'adminOrderStaffU.php', 
                method: 'POST',
                data: { orderId: orderId, orderStatus: status },
                success: function(response) {
                    if (response.trim() === 'success') {
                        
                        alert('Order status updated successfully');
                        
                        location.reload(); 
                    } else {
                        alert('Error updating status');
                    }
                },
                error: function() {
                    alert('An error occurred while updating the status.');
                }
            });
        }

    </script>
</body>
</html>
