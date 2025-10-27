<?php
// Fetch the distinct food types from the database
try {
    $stmt = $pdo->query("SELECT DISTINCT foodType FROM fooddetail ORDER BY foodType ASC");
    $foodTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $foodTypes = [];
}

// Get the current food type from the URL, if set
$currentFoodType = isset($_GET['foodType']) ? $_GET['foodType'] : '';
?>
<footer class="footer">
    <div class="footer">
        <div class="container2">
            <div class="row1">
                <div class="footer-col">
                    <h4>Mellow Mood</h4>
                    <ul>
                        <p>
                            Relax, unwind, and savor the moment at Mellow Mood.
                        </p>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="../member/aboutus.php">About Us</a></li>
                        <li>
                            <a href="https://maps.app.goo.gl/xri1rcJNK8wz97QN7">
                                Our Location</a>
                        </li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="../index.php">Home Page</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Menu</h4>
                    <ul>
                        <?php foreach ($foodTypes as $type): ?>
                            <li>
                                <a href="/member/member_product.php?foodType=<?= urlencode($type) ?>"
                                   class="<?= ($currentFoodType === $type) ? 'active' : '' ?>">
                                    <?= ucfirst(htmlspecialchars($type)) ?> 
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/18QuzM8Z2o/?mibextid=wwXIfr" target="_blank">
                            <i class="fab fa-facebook-f"></i> 
                        </a>
                        <a href="https://x.com/knight29243229" target="_blank">
                            <i class="fab fa-twitter"></i> 
                        </a>
                        <a href="https://www.instagram.com/lilynt99?igsh=MXRmOTdkemZmMWVnMA==" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/in/wan-lee-teh-6b48b8284" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer2">
            Developed by <b>ChowChu</b> &middot;
            Copyrighted &copy; <?= date('Y') ?>
        </div>
    </footer>

</body>
</html>
