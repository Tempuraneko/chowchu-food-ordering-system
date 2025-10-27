<?php
include '../component/connect.php';
require '../component/admin_sidebar.php';

if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    if (!isset($_GET['foodId'])) {
        $_SESSION['errors'] = ["No product selected."];
        header('Location: adminFood.php');
        exit();
    }
    
    $foodId = $_GET['foodId'];

    $sql = "SELECT * FROM fooddetail WHERE foodId = :foodId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':foodId', $foodId, PDO::PARAM_STR);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['errors'] = ["Product not found."];
        header("Location: adminFood.php");
        exit();
    }
}
?>

<?php
if (isset($_POST['submit'])) {
    $formData = $_POST;
    $foodId = $_POST['foodId'] ?? '';
    $foodName = $_POST['foodName'] ?? '';
    $foodImage = $product['foodImage'];
    $foodDetail = $_POST['foodDetail'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? 'active'; 

    $errors = [];

    // Handle image upload
    if (!empty($_FILES['foodImage']['name']) && $_FILES['foodImage']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['foodImage']['name'];
        $fileSize = $_FILES['foodImage']['size'];
        $tmpName = $_FILES['foodImage']['tmp_name'];

        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $validImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'avif', 'webp', 'pdf'];

        if (!in_array($imageExtension, $validImageExtensions)) {
            $errors[] = "Invalid Image Extension. Only JPG, JPEG, PNG, GIF, SVG, AVIF, WebP, PDF allowed.";
        }

        if ($fileSize > 1000000) {
            $errors[] = "Sorry, the image size is too large.";
        }

        if (empty($errors)) {
            $newImageName = uniqid('', true) . '.' . $imageExtension;

            $uploadDir = '../uploaded_files/';
            $uploadPath = $uploadDir . $newImageName;

            if (!move_uploaded_file($tmpName, $uploadPath)) {
                $errors[] = "Failed to upload image.";
            } else {
                $foodImage = $newImageName;
            }
        }
    }

    if (!$foodId || !$foodName || !$foodDetail || !$price) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: adminFoodU.php?foodId=$foodId");
        exit();
    }

    $sql = "UPDATE fooddetail SET foodName = :foodName, foodDetail = :foodDetail, price = :price, status = :status";

    if ($foodImage != $product['foodImage']) {
        $sql .= ", foodImage = :foodImage";
    }

    $sql .= " WHERE foodId = :foodId";
    $stmt = $pdo->prepare($sql);


    if ($foodImage != $product['foodImage']) {
        $stmt->bindParam(':foodImage', $foodImage, PDO::PARAM_STR);
    }
    $stmt->bindParam(':foodName', $foodName, PDO::PARAM_STR);
    $stmt->bindParam(':foodDetail', $foodDetail, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':foodId', $foodId, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product updated successfully!";
        header("Location: adminFood.php");
        exit();
    } else {
        $errorInfo = $stmt->errorInfo();
        $_SESSION['errors'] = ["Database error: Unable to update product. " . $errorInfo[2]];
        header("Location: adminFoodU.php?foodId=$foodId");
        exit();
    }
}

if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        echo "<p>$error</p>";
    }
    unset($_SESSION['errors']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="../js/adminForm.js"></script>
    <script src="../js/script.js"></script>
</head>
<body>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Edit Product</h3>
            </div>
        </div>

        <div class="box">
        <form action="" method="post" enctype="multipart/form-data">
            <fieldset style="border: none;">
                <div class="inputBox">
                    <label for="foodId">Food ID:</label>
                    <input type="text" name="foodId" id="foodId" class="inputUser" value="<?= htmlspecialchars($product['foodId']) ?>" readonly>
                </div>

                <div class="inputBox">
                <div class="inputBox">
                    <label for="foodImage">Food Image:</label>
                    <input type="file" name="foodImage" id="foodImage" accept=".jpg,.jpeg,.png,.gif,.svg,.avif,.webp,.pdf" class="inputUser">
                    <br>
                    <img id="imagePreview" src="#" alt="Selected Image" style="display: none; width: 100px; height: auto;">
                </div>

                <div class="inputBox">
                    
                    <img src="../uploaded_files/<?= htmlspecialchars($product['foodImage']) ?>" alt="Current Image" width="100">
                </div>

                <div class="inputBox">
                    <label for="foodName">Food Name:</label>
                    <input type="text" name="foodName" id="foodName" class="inputUser" value="<?= htmlspecialchars($product['foodName']) ?>" required>
                </div>

                <div class="inputBox">
                    <label for="foodDetail">Food Detail:</label>
                    <textarea name="foodDetail" id="foodDetail" class="inputUser" rows="5" cols="30" required><?= htmlspecialchars($product['foodDetail']) ?></textarea>
                </div>

                <div class="inputBox">
                    <label for="price">Price:</label>
                    <input type="text" name="price" id="price" class="inputUser" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>

                <div class="inputBox">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="inputUser">
                        <option value="Most Popular" <?= ($product['status'] == "Most Popular") ? "selected" : "" ?>>Most Popular</option>
                        <option value="New" <?= ($product['status'] == "New") ? "selected" : "" ?>>New</option>
                        <option value="Out Of Stock" <?= ($product['status'] == "Out Of Stock") ? "selected" : "" ?>>Out Of Stock</option>
                    </select>
                </div>

                <div class="inputBox">
                    <label for="foodType">Food Type:</label>
                    <select name="foodType" id="foodType" class="inputUser" required>
                        <option value="Cake">Cake</option>
                        <option value="Noodle">Noodle</option>
                        <option value="Pizza">Pizza</option>
                        <option value="Sidedish">Sidedish</option>
                        <option value="Veggie">Veggie</option>
                        <option value="Drink">Drink</option>
                        <option value="Salad">Salad</option> 
                    </select>
                </div>

                <div class="button">
                    <button type="submit" name="submit" class="submit-btn">Update</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='adminFood.php';">Cancel</button>
                </div>
            </fieldset>
        </form>
        </div>
    </div>
    <script>
document.getElementById('foodImage').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const imagePreview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result; // Set the preview image source
            imagePreview.style.display = "block"; // Show the image
        }
        reader.readAsDataURL(file); // Read file as data URL
    } else {
        imagePreview.style.display = "none"; // Hide if no file selected
    }
});
</script>
</body>
</html>
