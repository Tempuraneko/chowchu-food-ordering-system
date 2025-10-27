<?php
 require '../component/connect.php'; 

// Get mood from URL
$mood = $_GET['mood'] ?? 'normal';

// Mood-based encouragement messages
$moodData = [
    'veryhappy' => [
        'message' => "That's amazing! Let's celebrate this success/achievement 🎊🥳",
        'signature' => '— KRISTIN WILSON, MA, LPC, CCTP, RYT',
        'foodTypes' => ['Pizza']
    ],
    'happy' => [
        'message' => "Keep shining and remember that you are amazing. 😊",
        'signature' => '— Mandy Hale',
        'foodTypes' => ['Drink',]
    ],
    'normal' => [
        'message' => "Balance is beautiful. Stay centered 🌿",
        'signature' => '— By Pro.Kendra Cherry, MSEd',
        'foodTypes' => ['Noodle']
    ],
    'sad' => [
        'message' => "Progress may be slow, but every small step you take brings you closer to your goals. Keep moving forward! 💙",
        'signature' => '— Dr. Haniffah B. Abdul Gafoor (Neurologist)',
        'foodTypes' => ['Veggie']
    ],
    'upset' => [
        'message' => "Feelings of sadness or upset are often temporary. Allow yourself to feel, but also remind yourself that brighter days are ahead🔥❤️😊😉",
        'signature' => '— Ms. Sakshi Dhankhar (psychologist)',
        'foodTypes' => ['Sidedish','Salad']
    ]
];

$currentMood = $moodData[$mood] ?? $moodData['normal'];
$message = $currentMood['message'];
$signature = $currentMood['signature'];

// Get recommended foods based on mood
try {
    $placeholders = implode(',', array_fill(0, count($currentMood['foodTypes']), '?'));
    $sql = "SELECT * FROM fooddetail WHERE foodType IN ($placeholders) LIMIT 6";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($currentMood['foodTypes']);
    $recommendedFoods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Get all dishes for fallback
try {
    $stmt = $pdo->query("SELECT * FROM fooddetail LIMIT 12");
    $allDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <link rel="stylesheet" href="../css/moodSuggest.css">
  <link rel="stylesheet" href="../css/food-cards.css">
</head>
<body>
<?php include '../component/header.php';?>

  <div class="container">
    <div class="message-box">
      <h2><?php echo htmlspecialchars($message); ?></h2>
      <p class="signature"><?php echo htmlspecialchars($signature); ?></p>
    </div>

    <!-- Recommended Foods Section -->
    <section class="product-section">
      <h2>Recommended For Your Mood</h2>
      <p class="lead">These dishes match your current feeling</p>

      <div class="product-grid">
        <?php if (!empty($recommendedFoods)): ?>
          <?php foreach ($recommendedFoods as $dish): ?>
            <div class="product-card">
                <a href="foodDetail.php?foodId=<?php echo htmlspecialchars($dish['foodId']); ?>">
                    <div class="product-image">
                        <img src="../uploaded_files/<?php echo htmlspecialchars($dish['foodImage'] ?? 'default.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($dish['foodName']); ?>">
                    </div>
                </a>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($dish['foodName']); ?></h3>
                    <p class="product-category"><?php echo htmlspecialchars($dish['foodType'] ?? 'No category'); ?></p>
                    <div class="price-btn-block">
                        <span class="product-price">RM <?php echo number_format($dish['price'] ?? 0, 2); ?></span>
                        <button class="btn order-btn"
                            data-food-id="<?php echo htmlspecialchars($dish['foodId']); ?>"
                            data-food-name="<?php echo htmlspecialchars($dish['foodName']); ?>"
                            data-food-price="<?php echo number_format($dish['price'] ?? 0, 2); ?>"
                            data-food-image="../uploaded_files/<?php echo htmlspecialchars($dish['foodImage'] ?? 'default.jpg'); ?>">
                            + 
                        </button>
                    </div>
                </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No specific recommendations found. Here are some popular options:</p>
          <?php foreach ($allDishes as $dish): ?>
            <!-- Same product card structure as above -->
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <?php include '../component/footer.php'; ?>


  
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
                    <h5 type="hidden" id="productId"></h5>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const orderBtns = document.querySelectorAll('.order-btn');
      const modal = document.getElementById('orderModal1');
      const closeModal = document.getElementById('closeModal');
      const productImage = document.getElementById('productImage');
      const productName = document.getElementById('productName');
      const productDescription = document.getElementById('productDescription');
      const productPrice = document.getElementById('productPrice');
      const finalPrice = document.getElementById('finalPrice');
      const quantityDisplay = document.getElementById('quantityDisplay');
      const increaseBtn = document.getElementById('increaseQuantity');
      const decreaseBtn = document.getElementById('decreaseQuantity');
      const specialInstructions = document.getElementById('specialInstructions');
      const formFoodId = document.getElementById('formFoodId');
      const formQuantity = document.getElementById('formQuantity');
      const addToCartForm = document.getElementById('addToCartForm');

      let currentQuantity = 1;
      let currentPrice = 0;

      // Open modal when order button is clicked
      orderBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const foodId = this.getAttribute('data-food-id');
          const foodName = this.getAttribute('data-food-name');
          const foodPrice = this.getAttribute('data-food-price');
          const foodImage = this.getAttribute('data-food-image');
          const foodDetail = this.getAttribute('data-food-detail');

          productImage.src = foodImage;
          productName.textContent = foodName;
          productDescription.textContent = foodDetail;
          productPrice.textContent = 'RM ' + foodPrice;
          finalPrice.textContent = foodPrice;
          formFoodId.value = foodId;
          
          currentPrice = parseFloat(foodPrice);
          currentQuantity = 1;
          quantityDisplay.textContent = currentQuantity;
          formQuantity.value = currentQuantity;
          specialInstructions.value = '';
          
          modal.style.display = 'block';
        });
      });

      // Close modal
      closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
      });

      // Quantity controls
      increaseBtn.addEventListener('click', function() {
        currentQuantity++;
        quantityDisplay.textContent = currentQuantity;
        formQuantity.value = currentQuantity;
        updateFinalPrice();
      });

      decreaseBtn.addEventListener('click', function() {
        if (currentQuantity > 1) {
          currentQuantity--;
          quantityDisplay.textContent = currentQuantity;
          formQuantity.value = currentQuantity;
          updateFinalPrice();
        }
      });

      function updateFinalPrice() {
        const total = currentPrice * currentQuantity;
        finalPrice.textContent = total.toFixed(2);
      }

      // Form submission
      addToCartForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
          const response = await fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
          });

          const result = await response.json();
          if (result.status === 'success') {
            alert("Item added to cart successfully!");
            modal.style.display = 'none';
            // Optionally refresh the cart count or page
          } else {
            alert(result.message || "Error adding to cart");
          }
        } catch (error) {
          alert("Network error: " + error.message);
        }
      });

      // Close modal when clicking outside
      window.addEventListener('click', function(event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>
