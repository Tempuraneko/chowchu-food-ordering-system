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
    $lesson = trim($_POST["lesson"] ?? '');
    $promoID = 3;

    // Validate student is logged in
    if (!$studentID) {
        echo "<script>
            alert('You must be logged in to claim a voucher.');
            window.location.href = '../member/login.php';
        </script>";
        exit;
    }

    // Check word count in lesson textarea
    $wordCount = str_word_count($lesson);
    if ($wordCount < 50) {
        echo "<script>
            alert('Your group study description must contain at least 50 words. Current count: $wordCount');
            window.history.back();
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

        if ($claimCount > 5) {
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
            $errorMessage = "You have reached the maximum 5 redemptions for this voucher in 6 months. Next available claim after $nextAvailableDate.";
        }
    } catch (PDOException $e) {
        $errorMessage = 'Error checking voucher history. Please try again.';
    }

    if (empty($errorMessage)) {
        if (isset($_FILES["study_image"])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileName = basename($_FILES["study_image"]["name"]);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["study_image"]["tmp_name"]);
            if ($check === false) {
                $errorMessage = 'File is not an image.';
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $errorMessage = 'Only JPG, JPEG, PNG & GIF files are allowed.';
            } elseif (move_uploaded_file($_FILES["study_image"]["tmp_name"], $targetFile)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO voucher (promoID, studentID, isUsed, collectDate) 
                                        VALUES (:promoID, :studentID, 'No', NOW())");

                    $stmt->execute([
                        ':promoID' => $promoID,
                        ':studentID' => $studentID
                    ]);

                    $successMessage = "Voucher Claimed Successfully!\\nStudent Name: $studentName\\nLessons: $lesson\\n";
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

                <h2 class="page-title">🎓 Group Study Rewards: RM 5 Offer</h2>
                <p class="subtitle">
                    Upload your group study image and write at least <strong>50 words</strong> to claim your <strong>RM 5 discount voucher</strong>!
                </p>

                <form action="groupStudyPromotion.php" method="POST" enctype="multipart/form-data" class="offer-form">
                    <div class="form-group">
                        <label for="study_image">Upload Group Study Image</label>
                        <input type="file" name="study_image" id="study_image" required>
                    </div>

                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" name="student_name" id="student_name" value="<?= htmlspecialchars($studentName) ?>" placeholder="Enter your name" required>
                    </div>

                    <div class="form-group">
                        <label for="lesson">Group Study Description</label>
                        <textarea name="lesson" id="lesson" rows="5" placeholder="Describe the topic discussed in the group study and what you have learned (minimum 50 words)" required></textarea>
                        <div id="wordCounter" class="word-count">0 words</div>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="cancel-btn" onclick="window.location.href='specialOffers.php'">Cancel</button>
                    </div>
                </form>

                <script>
                    // Word count validation
                    document.getElementById('lesson').addEventListener('input', function() {
                        const text = this.value.trim();
                        const wordCount = text ? text.split(/\s+/).length : 0;
                        const counter = document.getElementById('wordCounter');

                        counter.textContent = wordCount + ' words';
                        counter.className = wordCount >= 50 ? 'word-count' : 'word-count warning';
                    });
                </script>

                <div class="terms">
                    <p>By submitting this form, you agree to the following terms and conditions:</p>
                    <strong>Terms and Conditions:</strong>
                    <ol>
                        <li>Voucher is valid for a one-time use only.</li>
                        <li>This voucher can be redeemed at most 5 times every semester.</li>
                        <li>Cannot be combined with other vouchers, discounts, or promotions.</li>
                        <li>Fraudulent submissions may disqualify you from future promotions.</li>
                    </ol>
                </div>
            </section>
        </main>
    </div>
    <?php include '../component/footer.php'; ?>
</body>

</html>