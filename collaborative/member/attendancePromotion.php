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
    $att = $_POST["att"] ?? 0;
    $promoID = 2;

    // Validate student is logged in
    if (!$studentID) {
        echo "<script>
            alert('You must be logged in to claim a voucher.');
            window.location.href = '../member/login.php';
        </script>";
        exit;
    }

    try {
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) as claimCount 
            FROM voucher 
            WHERE studentID = :studentID 
            AND promoID = :promoID 
            AND collectDate >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        ");

        $checkStmt->execute([
            ':studentID' => $studentID,
            ':promoID' => $promoID
        ]);

        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $claimCount = $result['claimCount'] ?? 0;

        if ($claimCount > 10) {
            $dateStmt = $pdo->prepare("
                SELECT collectDate 
                FROM voucher 
                WHERE studentID = :studentID 
                AND promoID = :promoID 
                ORDER BY collectDate ASC 
                LIMIT 1
            ");
            $dateStmt->execute([':studentID' => $studentID, ':promoID' => $promoID]);
            $oldestClaim = $dateStmt->fetch(PDO::FETCH_ASSOC);

            $nextAvailableDate = date('d M Y', strtotime($oldestClaim['collectDate'] . ' +6 months'));
            $errorMessage = "You have reached the maximum 10 redemptions for this voucher in 6 months. Next available claim after $nextAvailableDate.";
        }
    } catch (PDOException $e) {
        $errorMessage = 'Error checking voucher history. Please try again.';
    }

    if (empty($errorMessage)) {
        if ($att < 80) {
            $errorMessage = 'Attendance must be more than 80% to claim this voucher.';
        } elseif (isset($_FILES["att_image"])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileName = basename($_FILES["att_image"]["name"]);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["att_image"]["tmp_name"]);
            if ($check === false) {
                $errorMessage = 'File is not an image.';
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $errorMessage = 'Only JPG, JPEG, PNG & GIF files are allowed.';
            } elseif (move_uploaded_file($_FILES["att_image"]["tmp_name"], $targetFile)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO voucher (promoID, studentID, isUsed, collectDate) 
                                        VALUES (:promoID, :studentID, 'No', NOW())");

                    $stmt->execute([
                        ':promoID' => $promoID,
                        ':studentID' => $studentID
                    ]);

                    $successMessage = "Voucher Claimed Successfully!\\nStudent Name: $studentName\\nAttendance: $att%\\n";
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

                <h2 class="page-title">🎓 Attendance Rewards: RM 3 Offer</h2>
                <p class="subtitle">
                    Upload your attendance image (Rate ≥ <strong>80</strong>) and claim a <strong>RM 3 discount voucher</strong> as a reward for your excellence!
                </p>

                <form action="attendancePromotion.php" method="POST" enctype="multipart/form-data" class="offer-form">
                    <div class="form-group">
                        <label for="att_image">Upload Attendance Image</label>
                        <input type="file" name="att_image" id="att_image" required>
                    </div>

                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" name="student_name" id="student_name" value="<?= $studentName ?>" placeholder="Enter your name" required>
                    </div>

                    <div class="form-group">
                        <label for="att">Attendance Rate (%)</label>
                        <input type="number" step="1" name="att" id="att" min="0" max="100" placeholder="Enter your attendance rate" required>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="cancel-btn" onclick="window.location.reload()">Cancel</button>
                    </div>
                </form>

                <div class="terms">
                    <p>By submitting this form, you agree to the following terms and conditions:</p>
                    <strong>Terms and Conditions:</strong>
                    <ol>
                        <li>Voucher is valid for a one-time use only.</li>
                        <li>This voucher can be redeemed at most 10 times every semester (6 month).</li>
                        <li>Cannot be combined with other vouchers, discounts, or promotions.</li>
                        <li>Fraudulent submissions may disqualify you from future promotions.</li>
                    </ol>
                </div>

    </div>
    </section>
    </main>
    </div>
    <?php include '../component/footer.php'; ?>
</body>


</html>