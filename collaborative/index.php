<?php
include 'component/connect.php';
try {
    $stmt = $pdo->query("
        SELECT f.id AS foodId, f.foodName, f.foodDetail, f.foodImage, f.price, COUNT(od.foodId) AS orderCount
        FROM order_detail od
        JOIN fooddetail f ON od.foodId = f.id  
        GROUP BY od.foodId
        ORDER BY orderCount DESC
        LIMIT 6
    ");
    $popularDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

?>

<?php
            require 'component/header.php';
        ?>

<html>

    <body>
    <div class="home" id="home">
        <div class="container">
            <div class="home-wrapper d-grid">
                <div class="col-left">
                    <h3>Welcome TO</h3>
                    <h1>Chow CHU</h1>

                    <p>Welcome to Chow Chu, where delicious flavors meet convenience! We 
                    offer a wide variety of freshly prepared meals, from hearty classics to quick 
                    bites, all made with high-quality ingredients. Whether you're craving a savory 
                    meal or a sweet treat, we’ve got something for everyone. Order online and enjoy 
                    fast, hassle-free delivery straight to your doorstep!</p>
                    <a href="../member/menu.php" class="btn">Order Now</a>
                </div>
                <div class="col-right">
                    <img data-tilt src="image/hero-img.png" alt="Home image" class="hero-img">
                </div>

            </div>
        </div>
    </div>

    <div class="popular">
    <div class="container">
        <h2>Popular Dishes of the Month</h2>
        <p class="lead">Easiest way to order your favourite food among these top 6 dishes</p>
        <div class="product-grid">
            <?php if (!empty($popularDishes)): ?>
                <?php foreach ($popularDishes as $r): ?>
                    <div class="product-card">
                        <a href="<?php echo htmlspecialchars($r['foodId']); ?>">
                            <div class="product-image">
                                <img src="uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($r['foodName']); ?>">
                            </div>
                        </a>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($r['foodName']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($r['foodDetail'] ?? 'No description available'); ?></p>
                            <div class="price-btn-block">
                                <span class="product-price">RM <?php echo number_format(htmlspecialchars($r['price'] ?? 0), 2); ?></span>
                                <button class="btn order-btn"
                                    data-food-id="<?php echo htmlspecialchars($r['foodId']); ?>"
                                    data-food-name="<?php echo htmlspecialchars($r['foodName']); ?>"
                                    data-food-price="<?php echo number_format(htmlspecialchars($r['price'] ?? 0), 2); ?>"
                                    data-food-image="uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>"
                                    data-food-detail="<?php echo htmlspecialchars($r['foodDetail'] ?? 'No description available'); ?>"
                                >+ </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No popular dishes found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Modal Structure -->
    <div id="orderModal1" class="modal1">
        <div class="modal-content1">
            <div class="product-info1">
                <!-- Product image on the left side -->
                <div class="product-image">
                    <img id="productImage" src="" alt="Product Image" />
                </div>
                
                <!-- Product details (name, description, price) beside the image -->
                <div class="product-details">
                    <input type="hidden" id="productId"></input>
                    <h4 id="productName"></h4>
                    <p id="productDescription"></p>
                    <p id="productPrice"></p>
                </div>
            </div>

            <!-- Order options (quantity, special instructions) -->
            <div class="order-options">
                <!-- Special instructions (Note) -->
                <div class="form-group">
                    <label for="specialInstructions">Special Instructions:</label>
                    <textarea id="specialInstructions" placeholder="E.g. No onions please"></textarea>
                </div>
                
                <!-- Add to Basket and Quantity buttons -->
                <div class="modal-footer">
                    <button id="closeModal" class="btn">Close</button>
                    <div class="quantity-controls">
                        <button id="decreaseQuantity" class="btn">-</button>
                        <span id="quantityDisplay">1</span>
                        <button id="increaseQuantity" class="btn">+</button>
                    </div>
                    <button id="addToCart" class="btn">Add to Basket - RM <span id="finalPrice">0.00</span></button>
                </div>
            </div>
        </div>
    </div>

    <div id="faqSection" class="container-box">
    <div class="header">
        <h1>Know more about us!</h1>
        <div class="tabs">
            <button onclick="scrollToFAQs()">Frequent Questions</button>
            <a href="https://wa.link/j487po" target="_blank">
                <button>Help & Support</button>
            </a>
        </div>
    </div>

    <div class="faq-section">
        <!-- FAQ 1 -->
        <div class="faq-box">
            <i class="fa fa-question-circle"></i>
            <h3>How does Chow Chu work?</h3>
            <p>Chow Chu simplifies the food ordering process. Browse through our menu and place your order.</p>
        </div>

        <!-- FAQ 2 -->
        <div class="faq-box">
            <i class="fa fa-credit-card"></i>
            <h3>What payment methods are accepted?</h3>
            <p>We accept all major credit cards, debit cards, and mobile wallet payments.</p>
        </div>

        <!-- FAQ 3 -->
        <div class="faq-box">
            <i class="fa fa-truck"></i>
            <h3>Can I track my order in real-time?</h3>
            <p>Yes, you can track your order’s progress and delivery time directly through our website or app.</p>
        </div>

        <!-- FAQ 4 -->
        <div class="faq-box">
            <i class="fa fa-tags"></i>
            <h3>Are there any special discounts or promotions available?</h3>
            <p>Yes! We offer regular discounts and promotions for our loyal customers and new users.</p>
        </div>
    </div>
</div>

<script>
function scrollToFAQs() {
    document.getElementById('faqSection').scrollIntoView({ behavior: 'smooth' });
}
</script>
<?php
    require 'component/footer.php';
?>
    </body>
    </html>