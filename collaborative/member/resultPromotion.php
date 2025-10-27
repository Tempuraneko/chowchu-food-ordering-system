<!DOCTYPE html>
<html lang="en">
<?php
include '../component/connect.php';
include '../component/header.php';

$studentName = $_POST["student_name"] ?? null;
$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentID = $_SESSION['studentID'] ?? null;
    $gpa = floatval($_POST["gpa"] ?? 0);
    $promoID = 1;

    // Validate student is logged in
    if (!$studentID) {
        echo "<script>
            alert('You must be logged in to claim a voucher.');
            window.location.href = '../member/login.php';
        </script>";
        exit;
    }

    // Check if student has claimed this voucher within last 6 months
    try {
        $checkStmt = $pdo->prepare("
            SELECT collectDate 
            FROM voucher 
            WHERE studentID = :studentID 
            AND promoID = :promoID 
            AND collectDate >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ORDER BY collectDate DESC 
            LIMIT 1
        ");

        $checkStmt->execute([
            ':studentID' => $studentID,
            ':promoID' => $promoID
        ]);

        $lastClaim = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($lastClaim) {
            $lastClaimDate = date('d M Y', strtotime($lastClaim['collectDate']));
            $errorMessage = "You have already claimed this voucher within the last 6 months (on $lastClaimDate).";
        }
    } catch (PDOException $e) {
        $errorMessage = 'Error checking voucher history. Please try again.';
    }


    if (empty($errorMessage)) {

        if ($gpa < 3.75) {
            $errorMessage = 'GPA must be 3.75 or higher to claim this voucher.';
        } elseif (isset($_FILES["result_image"])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileName = basename($_FILES["result_image"]["name"]);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));


            $check = getimagesize($_FILES["result_image"]["tmp_name"]);
            if ($check === false) {
                $errorMessage = 'File is not an image.';
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $errorMessage = 'Only JPG, JPEG, PNG & GIF files are allowed.';
            } elseif (move_uploaded_file($_FILES["result_image"]["tmp_name"], $targetFile)) {
                try {

                    $stmt = $pdo->prepare("INSERT INTO voucher (promoID, studentID, isUsed, collectDate) 
                                          VALUES (:promoID, :studentID, 'No', NOW())");

                    $stmt->execute([
                        ':promoID' => $promoID,
                        ':studentID' => $studentID
                    ]);

                    $successMessage = "Voucher Claimed Successfully!\\nStudent Name: $studentName\\nGPA: $gpa\\n";
                } catch (PDOException $e) {
                    $errorMessage = 'Error saving your voucher. Please try again.';
                }
            } else {
                $errorMessage = 'Error uploading file.';
            }
        }
    }
}


if (!empty($successMessage)) {
    echo "<script>
        alert('$successMessage');
        window.location.href = 'specialOffers.php';
    </script>";
    exit;
} elseif (!empty($errorMessage)) {
    echo "<script>
        alert('$errorMessage');
        window.history.back();
    </script>";
    exit;
}
?>


<head>
    <meta charset="UTF-8">
    <title>Special Offer</title>
    <link rel="stylesheet" href="../css/promo.css">
</head>

<body>
    <div class="container">
        <main class="content">

            <section class="offer-box">

                <div class="back-button-container">
                    <button class="back-button" onclick="window.location.href='specialOffers.php';">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>

                <h2 class="page-title">🎓 GPA Rewards: RM 15 Offer</h2>
                <p class="subtitle">
                    Upload your result image ( GPA ≥ <strong>3.75</strong>) and claim a <strong>RM 15 discount voucher</strong> as a reward for your academic excellence!
                </p>

                <form action="resultPromotion.php" method="POST" enctype="multipart/form-data" class="offer-form">
                    <div class="form-group">
                        <label for="result_image">Upload Result Image</label>
                        <input type="file" name="result_image" id="result_image" required>
                    </div>

                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" name="student_name" id="student_name" value="<?= $studentName ?>" placeholder="Enter your name" required>
                    </div>

                    <div class="form-group">
                        <label for="gpa">GPA (0.0 - 4.0)</label>
                        <input type="number" step="0.01" name="gpa" id="gpa" min="0" max="4" placeholder="Enter your GPA" required>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="cancel-btn" onclick="window.location.reload()">Cancel</button>
                    </div>
                </form>

                <div class="terms">
                    <p>By submitting the form, you agree to the following terms and conditions:</p>
                    <strong>Terms and Conditions:</strong>
                    <ol>
                        <li>Voucher is valid for a one-time use only.</li>
                        <li>This voucher can be redeemed every semester (every six month).</li>
                        <li>Cannot be combined with other vouchers, discounts, or promotions.</li>
                        <li>Fraudulent submissions may disqualify you from future promotions.</li>
                    </ol>
                </div>

                <?php
                /*
                // Read Terms and conditions from the database                
                $promoID = 1;
                if ($promoID) {
                    try {
                        $stmt = $pdo->prepare("SELECT description FROM promotion WHERE promoID = ?");
                        $stmt->execute([$promoID]);
                        $promo = $stmt->fetch(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        // Handle error
                        $promo = null;
                    }
                }
                ?>

                <div class="terms">
                    <p>By submitting your result, you agree to the following terms and conditions:</p>
                    <strong>Terms and Conditions:</strong>
                    <?php if (!empty($promo['description'])): ?>
                        <?= nl2br(htmlspecialchars($promo['description'])) ?>
                    <?php else: ?>
                        <ol>
                            <li>Voucher is valid for a one-time use only.</li>
                            <li>This voucher can be redeemed every semester.</li>
                            <li>Cannot be combined with other vouchers, discounts, or promotions.</li>
                            <li>Fraudulent submissions may disqualify you from future promotions.</li>
                        </ol>
                    <?php endif; */ ?>
    </div>
    </section>
    </main>
    </div>
    <?php include '../component/footer.php'; ?>
</body>


</html>