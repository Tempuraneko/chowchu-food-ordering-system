<?php
include '../component/connect.php';

if (isset($_POST["deleteId"]) && is_array($_POST["deleteId"])) {
    $productIds = $_POST['deleteId'];

    $pdo->beginTransaction();

    try {
        // Step 1: Fetch id from product table corresponding to the given productId
        $queryFetchIds = "SELECT id FROM product WHERE productId IN (" . implode(",", array_fill(0, count($productIds), "?")) . ")";
        $stmtFetchIds = $pdo->prepare($queryFetchIds);

        foreach ($productIds as $key => $productId) {
            $stmtFetchIds->bindValue($key + 1, $productId, PDO::PARAM_STR);
        }

        $stmtFetchIds->execute();
        $productTableIds = $stmtFetchIds->fetchAll(PDO::FETCH_COLUMN);

        if (empty($productTableIds)) {
            throw new Exception("No matching products found for the provided product IDs.");
        }

        // Step 2: Delete related rows product_image using id from product table
        $queryImages = "DELETE FROM product_images WHERE productId IN (" . implode(",", array_fill(0, count($productTableIds), "?")) . ")";
        $stmtImages = $pdo->prepare($queryImages);

        foreach ($productTableIds as $key => $id) {
            $stmtImages->bindValue($key + 1, $id, PDO::PARAM_INT);
        }

        if (!$stmtImages->execute()) {
            throw new Exception("Failed to delete product images: " . implode(", ", $stmtImages->errorInfo()));
        }

        // Step 3: Delete rows in product using the fetched id
        $queryProducts = "DELETE FROM product WHERE id IN (" . implode(",", array_fill(0, count($productTableIds), "?")) . ")";
        $stmtProducts = $pdo->prepare($queryProducts);

        foreach ($productTableIds as $key => $id) {
            $stmtProducts->bindValue($key + 1, $id, PDO::PARAM_INT);
        }

        if ($stmtProducts->execute()) {
            
            $pdo->commit();
            echo "success";
        } else {
            throw new Exception("Failed to delete products: " . implode(", ", $stmtProducts->errorInfo()));
        }

    } catch (Exception $e) {
        
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No product IDs received.";
}
?>
