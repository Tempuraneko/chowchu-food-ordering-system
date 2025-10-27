<?php
include '../component/connect.php';
include '../component/header.php';

$studentID = $_SESSION['studentID'] ?? null;
if (!$studentID) {
    header('Location: ../member/login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT v.voucherID, v.collectDate, v.usedDate, p.promoID, p.type, p.discountAmount, p.expiryDate, p.description
        FROM voucher v
        JOIN promotion p ON v.promoID = p.promoID
        WHERE v.studentID = :studentID 
        AND v.isUsed = 'Yes'
        ORDER BY v.usedDate DESC
    ");
    $stmt->execute([':studentID' => $studentID]);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching used vouchers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/myVoucher.css">
    <title>Used Vouchers</title>
</head>

<body>
    <div class="page-wrapper">
        <div class="sidebar-container">
            <?php include '../component/sidebar.php'; ?>
        </div>
        <div class="vouchers-container">
            <h1 class="page-title">📜 Used Vouchers</h1>

            <?php if (count($vouchers) > 0): ?>
                <div class="vouchers-grid">
                    <?php foreach ($vouchers as $voucher): ?>
                        <div class="voucher-card used">
                            <div class="voucher-header">
                                <h3 class="voucher-title"><?= htmlspecialchars($voucher['type']) ?> (Used)</h3>
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
                                    <span class="detail-label">Used On:</span>
                                    <span class="detail-value"><?= date('d M Y', strtotime($voucher['usedDate'])) ?></span>
                                </div>

                                <div class="voucher-detail">
                                    <span class="detail-label">Expired On:</span>
                                    <span class="detail-value"><?= date('d M Y', strtotime($voucher['expiryDate'])) ?></span>
                                </div>

                                <div class="terms-toggle" onclick="toggleTerms(this)">▼ View Terms & Conditions</div>
                                <div class="terms-content"><?= htmlspecialchars($voucher['description']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-vouchers">
                    <p>You have not used any vouchers yet.</p>
                    <p>Use your <a href="myVoucher.php">available vouchers</a> before they expire!</p>
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
    </script>

    <?php include '../component/footer.php'; ?>
</body>

</html>