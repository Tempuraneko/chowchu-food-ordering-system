<?php
require '../component/admin_sidebar.php';
include '../component/connect.php';

$productPage = 10;

// Define the fields you want to display in the table
$fields = [
    'foodId' => 'Food ID',
    'foodImage' => 'Food Image',
    'foodName' => 'Food Name',
    'price' => 'Price',
    'status' => 'Status'
    
];

$totalQuery = $pdo->query('SELECT COUNT(*) FROM fooddetail');
$totalFood = $totalQuery->fetchColumn();

// Calculate the total pages for pagination
$totalPages = ceil($totalFood / $productPage);

// Get the current page from the URL or default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $totalPages));

// Calculate the offset for the SQL query
$start = ($page - 1) * $productPage;

// Sort functionality
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;
$dir = isset($_GET['dir']) && in_array($_GET['dir'], ['asc', 'desc']) ? $_GET['dir'] : 'asc';

$query = "SELECT * FROM fooddetail"; 

if ($sort && array_key_exists($sort, $fields)) {
    $query .= " ORDER BY $sort $dir";
}

// Limit the number of results based on the pagination
$query .= " LIMIT :start, :productPage";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':productPage', $productPage, PDO::PARAM_INT);
$stmt->execute();

// Fetch the results from the database
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <h1>Food</h1>
        
        <div class="search-filter-bar">
            <div class="search-bar">
                <i class="fa fa-search"></i>
                <form action="adminFoodS.php" method="post">
                    <div class="input-container">
                        <input type="text" name="searchQuery" placeholder="Search food ID, Name..." value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>">
                        <button type="submit" name="search"></button>
                    </div>
                </form>
            </div>
            
            <div class="action-icons">
                <i class="fa fa-filter" id="filter-btn"></i>
                <a href="adminFoodC.php">
                    <i class="fa fa-plus"></i>
                </a>
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
                        </th>
                    <?php endforeach; ?>
                    <th>Action</th>
                    <th>
                        <input type="checkbox" id="select-all">
                        <button type="button" onclick="confirmDelete()">
                            <i class="fas fa-trash fa-lg text-white" style="color:rgb(220, 38, 38)"></i>
                        </button>
                    </th>
                    <th>View Detail</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($foods && count($foods) > 0): ?>
                    <?php foreach ($foods as $row): ?>
                            <td><?php echo $row["foodId"]; ?></td>
                            <td>
                                <img src="../uploaded_files/<?php echo $row["foodImage"]; ?>" alt="Food Image" class="product-img">
                            </td>
                            <td><?php echo $row["foodName"]; ?></td>
                            <td>RM <?php echo number_format($row["price"], 2); ?></td>
                            <td>
                                <select name="status" onchange="updateStatus('<?php echo $row['foodId']; ?>', this.value)">
                                    <option value="New" <?php echo $row['status'] == 'New' ? 'selected' : ''; ?>>New</option>
                                    <option value="Most Popular" <?php echo $row['status'] == 'Most Popular' ? 'selected' : ''; ?>>Most Popular</option>
                                    <option value="Out of Stock" <?php echo $row['status'] == 'Out of Stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                    <option value="N/A" <?php echo $row['status'] == 'N/A' ? 'selected' : ''; ?>>N/A</option>
                                </select>
                            </td>
                            <td>
                                <a href="adminFoodU.php?foodId=<?php echo $row['foodId']?>"> 
                                    <i class="fas fa-edit fa-lg text-white" style="color: rgb(59, 130, 246)"></i>
                                </a>
                            </td>
                            <td align="center">
                                <input type="checkbox" name="deleteId[]" value="<?php echo $row['foodId']; ?>">
                            </td>
                            <td>
                                <button class="btn btn-info" onclick="showProductDetails()">View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

        <!-- Pagination Section (Again) -->
        <div class="pagination">
            <a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>">&laquo;</a>
            
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php } ?>
            
            <a href="?page=<?php echo $page < $totalPages ? $page + 1 : $totalPages; ?>">&raquo;</a>
        </div>
    </div>

<script>
    function confirmDelete() {
        const selectedFoods = [];
        
        $("input[name='deleteId[]']:checked").each(function() {
            selectedFoods.push($(this).val());
        });

        if (selectedFoods.length === 0) {
            Swal.fire(
                'No Foods Selected!',
                'Please select foods to delete.',
                'warning'
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
                $.post('adminFoodD.php', { deleteId: selectedFoods }, function (data) {
                    if (data.trim() === 'success') {
                        Swal.fire(
                            'Deleted!',
                            'The selected foods have been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            `Failed to delete foods: ${data}`,
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

<script>
function updateStatus(foodId, status) {
            $.ajax({
                url: 'adminFoodStatusU.php', 
                method: 'POST',
                data: { foodId: foodId, status: status },
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
