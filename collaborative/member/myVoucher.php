<?php
include '../component/connect.php';
include '../component/header.php';

$studentID = $_SESSION['studentID'] ?? null;
if (!$studentID) {
    header('Location: ../member/login.php');
    exit;
}

/*
// To retrieve student details from the database
try {
    $sql = "SELECT * FROM student WHERE studentID = :studentID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['studentID' => $studentID]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC); // Only one row expected
} catch (PDOException $e) {
    die("Error fetching student: " . $e->getMessage());
}*/


try {
    $stmt = $pdo->prepare("
        SELECT v.voucherID, v.collectDate, p.promoID, p.type, p.discountAmount, p.expiryDate, p.description
        FROM voucher v
        JOIN promotion p ON v.promoID = p.promoID
        WHERE v.studentID = :studentID 
        AND v.isUsed = 'No'
        ORDER BY v.collectDate DESC
    ");
    $stmt->execute([':studentID' => $studentID]);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching vouchers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/myVoucher.css">
    <title>My Vouchers</title>
</head>

<body>
    <div class="page-wrapper">
        <div class="sidebar-container">
            <?php include '../component/sidebar.php'; ?>
        </div>
        <div class="vouchers-container">
            <h1 class="page-title">🎫 My Vouchers</h1>

            <?php if (count($vouchers) > 0): ?>
                <div class="vouchers-grid">
                    <?php foreach ($vouchers as $voucher): ?>
                        <div class="voucher-card">
                            <div class="voucher-header">
                                <h3 class="voucher-title"><?= htmlspecialchars($voucher['type']) ?></h3>
                            </div>

                            <div class="voucher-body">
                                <div class="voucher-detail">
                                    <span class="detail-label">Discount Value:</span>
                                    <span class="discount-value">RM <?= number_format($voucher['discountAmount'], 2) ?></span>
                                </div>

                                <div class="voucher-detail">
                                    <span class="detail-label">Claimed On:</span>
                                    <span class="detail-value"><?= date('d M Y', strtotime($voucher['collectDate'])) ?></span>
                                </div>

                                <div class="voucher-detail">
                                    <span class="detail-label">Expires On:</span>
                                    <span class="detail-value"><?= date('d M Y', strtotime($voucher['expiryDate'])) ?></span>
                                </div>

                                <div class="terms-toggle" onclick="toggleTerms(this)">▼ View Terms & Conditions</div>
                                <div class="terms-content"><?= htmlspecialchars($voucher['description']) ?></div>
                            </div>

                            <div class="voucher-footer">
                                <button class="use-btn" onclick="useVoucher(<?= $voucher['voucherID'] ?>)">Use This Voucher</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-vouchers">
                    <p>You don't have any unused vouchers yet.</p>
                    <p>Check out our <a href="specialOffers.php">Special Offers</a> to claim vouchers!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function toggleTerms(element) {
            const termsContent = element.nextElementSibling;
            if (termsContent.style.display === 'block') {
                termsContent.style.display = 'none';
                element.textContent = '▼ View Terms & Conditions';
            } else {
                termsContent.style.display = 'block';
                element.textContent = '▲ Hide Terms & Conditions';
            }
        }

        function useVoucher(voucherID) {
            if (confirm('Are you sure you want to use this voucher?\nThis action cannot be undone.')) {
                alert('Voucher applied! Redirecting to checkout...');
                window.location.href = 'checkOut.php?voucherID=' + voucherID;
            }
        }
    </script>

    <?php include '../component/footer.php'; ?>
</body>

</html>