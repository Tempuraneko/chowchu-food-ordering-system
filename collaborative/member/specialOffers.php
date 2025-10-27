<?php
require '../component/connect.php';
require '../component/header.php';
?>

<?php

// After user clicked "Claim" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promoID'])) {
    if (!isset($_SESSION['studentID'])) {
        $error = "You must log in to claim a voucher.";
    }
    else {$promoID = (int)$_POST['promoID'];

    if ($promoID >= 1 && $promoID <= 3) {
        // Store in session for use on the next page
        $_SESSION['claimed_voucher'] = $promoID;

        // Redirect to appropriate page based on voucher ID
        switch ($promoID) {
            case 1:
                header('Location: resultPromotion.php');
                exit;
            case 2:
                header('Location: attendancePromotion.php');
                exit;
            case 3:
                header('Location: groupStudyPromotion.php');
                exit;
        }
    } else {
        $error = "Invalid voucher selected";
    }
}
}

try {
    $sql = "SELECT * FROM promotion";
    $stmt = $pdo->query($sql);
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching promotions: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/specialOffers.css">
    <title>Special Offers</title>
</head>

<body>

    <div class="page-wrapper">
        <div class="sidebar-container">
            <?php
            include '../component/sidebar.php'
            ?>
        </div>

        <div class="special-offers-container">
            <h2 class="page-title">🎁 Special Offers for You</h2>

            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="voucher-container">
                <?php if (count($promotions) > 0): ?>
                    <?php foreach ($promotions as $row): ?>
                        <div class="voucher-card">
                            <div class="voucher-content">
                                <h3 class="voucher-title"><?= htmlspecialchars($row["type"]) ?></h3>

                                <p class="voucher-details">🎯 No minimum spend required</p>

                                <p class="voucher-expiry">⏰ Expires on: <?= date("d-m-Y", strtotime($row["expiryDate"])) ?></p>

                                <form class="claim-form" method="POST">
                                    <input type="hidden" name="promoID" value="<?= $row["promoID"] ?>">
                                    <button type="submit" class="claim-btn">Claim Now</button>
                                </form>
                            </div>

                            <div class="voucher-bg">🎫</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No promotions available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
<?php require '../component/footer.php'; ?>

</html>