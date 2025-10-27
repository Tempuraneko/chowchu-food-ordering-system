<?php
include '../component/connect.php';
require '../component/admin_sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $formData = $_POST;
    $foodId = $_POST['foodId'] ?? '';
    $foodName = $_POST['foodName'] ?? '';
    $foodImage = $_POST['foodImage'] ?? '';
    $foodDetail = $_POST['foodDetail'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? 'active'; 
    $foodType = $_POST['foodType'] ?? ''; 

    $errors = [];

    // Handle image upload
    if ($_FILES['foodImage']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['foodImage']['name'];
        $fileSize = $_FILES['foodImage']['size'];
        $tmpName = $_FILES['foodImage']['tmp_name'];

        $validImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'avif', 'webp', 'pdf'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $validImageExtensions)) {
            $errors[] = "Invalid image extension. Only JPG, JPEG, PNG, GIF, SVG, AVIF, WebP, PDF allowed.";
        }

        if ($fileSize > 1000000) { 
            $errors[] = "Image size is too large. Max size: 1MB.";
        }

        if (empty($errors)) {
            $uniqueFileName = uniqid() . '.' . $imageExtension;
            $uploadDir = '../uploaded_files/';
            $uploadPath = $uploadDir . $uniqueFileName;

            if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                $errors[] = "Upload directory is not writable.";
            } elseif (!move_uploaded_file($tmpName, $uploadPath)) {
                $errors[] = "Failed to upload image.";
            } else {
                $foodImage = $uniqueFileName;  
            }
        }
    } else {
        $errors[] = "Image upload error. Please try again.";
    }

    if (!$foodId || !$foodName || !$foodDetail || !$price) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    $check = "SELECT COUNT(*) AS count FROM fooddetail WHERE foodId = :foodId";
    $checkStmt = $pdo->prepare($check);
    $checkStmt->bindParam(':foodId', $foodId, PDO::PARAM_STR);
    $checkStmt->execute();
    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        $errors[] = "The Food ID '$foodId' already exists. Please choose another!";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['formData'] = $formData;
        header("Location: adminFoodC.php");
        exit();
    }

    $pdo->beginTransaction();
    try {
        $sql = "INSERT INTO fooddetail (foodId, foodImage, foodName, foodDetail, price, status, foodType) 
        VALUES (:foodId, :foodImage, :foodName, :foodDetail, :price, :status, :foodType)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':foodId' => $foodId,
            ':foodImage' => $foodImage,
            ':foodName' => $foodName,
            ':foodDetail' => $foodDetail,
            ':price' => $price,
            ':status' => $status,
            ':foodType' => $foodType  
        ]);

        $pdo->commit();
        $_SESSION['success'] = "Product added successfully!";
        header("Location: adminFood.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
        $_SESSION['errors'] = $errors;
        header("Location: adminFoodC.php");
        exit();
    }
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
                <h3>Create Product</h3>
            </div>
        </div>

        <div class="box">
            <form action="" method="post" enctype="multipart/form-data">
                <fieldset style="border: none;">
                    <div class="inputBox">
                        <label for="foodId">Food ID:</label>
                        <input type="text" name="foodId" id="foodId" class="inputUser" required>
                    </div>

                    <div class="inputBox">
                        <label for="foodImage">Food Image:</label>
                        <input type="file" name="foodImage" id="foodImage" accept=".jpg,.jpeg,.png,.gif,.svg,.avif,.webp,.pdf" class="inputUser" required>
                        <br>
                        <img id="imagePreview" src="#" alt="Selected Image" style="display: none; width: 100px; height: auto;">
                    </div>

                    <div class="inputBox">
                        <label for="foodName">Food Name:</label>
                        <input type="text" name="foodName" id="foodName" class="inputUser" required>
                    </div>

                    <div class="inputBox">
                        <label for="foodDetail">Food Detail:</label>
                        <textarea name="foodDetail" id="foodDetail" class="inputUser" rows="5" cols="30" required></textarea>
                    </div>

                    <div class="inputBox">
                        <label for="price">Price:</label>
                        <input type="text" name="price" id="price" class="inputUser" required>
                    </div>

                    <div class="inputBox">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="inputUser">
                            <option value="Most Popular" selected>Most Popular</option>
                            <option value="New">New</option>
                            <option value="Out Of Stock">Out Of Stock</option>
                            <option value="N/A">N/A</option>
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
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="cancel-btn" onclick="window.location.href='adminProduct.php';">Cancel</button>
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
            imagePreview.src = e.target.result; // Set image source
            imagePreview.style.display = "block"; // Show the preview
        }
        reader.readAsDataURL(file); // Read file as data URL
    } else {
        imagePreview.style.display = "none"; // Hide if no file selected
    }
});
</script>
</body>
</html>
