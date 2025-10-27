<?php
require '../component/connect.php';
require '../component/header.php';
?>

<?php

if (isset($_SESSION['order_error'])) {
    $errorMessage = $_SESSION['order_error'];
    unset($_SESSION['order_error']); // Clear the message after showing it
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Order Error',
            text: '$errorMessage'
        });
    </script>";
}
try {
    $typeStmt = $pdo->query("SELECT DISTINCT foodType FROM fooddetail WHERE foodType IS NOT NULL AND foodType != ''");
    $foodTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}


// Fetch all dishes or filter by food type if selected
$currentFoodType = isset($_GET['foodType']) ? $_GET['foodType'] : '';
$query = "SELECT foodId, foodName, foodDetail, foodImage, price, foodType FROM fooddetail";
if ($currentFoodType !== '') {
    $query .= " WHERE foodType = :foodType";
}

try {
    $stmt = $pdo->prepare($query);
    if ($currentFoodType !== '') {
        $stmt->bindParam(':foodType', $currentFoodType);
    }
    $stmt->execute();
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

$reviewsPerPage = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $reviewsPerPage;

// Query to fetch reviews with pagination
try {
    $stmt = $pdo->query("
        SELECT r.reviewId, r.rating, r.comment, r.reviewDate, r.reviewMedia, s.name, f.foodName
        FROM review r
        JOIN fooddetail f ON r.foodId = f.id
        JOIN student s ON r.studentId = s.studentId
        ORDER BY r.reviewDate DESC
        LIMIT $start, $reviewsPerPage
    ");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to get total reviews for pagination
    $totalReviewsStmt = $pdo->query("SELECT COUNT(*) FROM review");
    $totalReviews = $totalReviewsStmt->fetchColumn();

    // Calculate total number of pages
    $totalPages = ceil($totalReviews / $reviewsPerPage);

} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Page - ChowChu</title>
  <link rel="stylesheet" href="../css/product.css" />
</head>
<body>
<style>
/* Container for the reviews */
.reviews {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Title of the review section */
h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
}

/* Review list styling */
.review-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Adjust column width */
    gap: 20px;
    justify-content: center;
}

/* Individual review box styling */
.review-box {
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

/* Hover effect on review box */
.review-box:hover {
    transform: translateY(-5px); /* Lifts the review box slightly */
}

/* Rating stars styling */
.stars {
    color: gold;
    font-size: 1.2rem;
}

/* Reviewer name styling */
.reviewer {
    font-weight: bold;
    font-size: 1rem;
    margin: 5px 0;
}

/* Comment text styling */
.comment {
    font-size: 1rem;
    color: #333;
    margin-bottom: 10px;
}

/* Styling for the review media (image) */
.review-media {
    margin-top: 10px;
    text-align: center;
}

.review-img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

/* Pagination styling */
.review-pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.review-pagination a {
    margin: 0 10px;
    padding: 10px;
    text-decoration: none;
    color: #007bff;
    border: 1px solid #007bff;
    border-radius: 5px;
}

.review-pagination a:hover {
    background-color: #007bff;
    color: white;
}

</style>
<main class="product-page">

<section class="hero-banner" id="heroBanner">
  <div class="hero-overlay">
    <div class="hero-left">
      <p class="tagline" id="tagline">I'm lovin' it!</p>
      <h1 class="brand-name">CHOWCHU</h1>
      <p class="open-time" id="openTime"><i class="fa fa-clock"></i> Open until 6:00 AM</p>
    </div>
    <div class="hero-right">
      <div class="image-box">
        <img src="../images/burger.png" id="heroImage" alt="Hero Product" />
      </div>
      <div class="rating-box" id="ratingBox">
        <p>3.4</p>
        <small>1,360 reviews</small>
      </div>
    </div>
  </div>
</section>
<h2>All Offers from ChowChu</h2>
  <!-- Promotions Section -->
  <section class="promotions container">
    <div class="promo-card">
      <img src="../images/promo1.png" alt="Promo" />
      <div class="promo-text">
        <h4>Mellow Mood</h4>
        <p>First Order Discount</p>
      </div>
    </div>
    <div class="promo-card">
      <img src="../images/promo2.png" alt="Promo" />
      <div class="promo-text">
        <h4>Mellow Mood</h4>
        <p>Cookies Discount</p>
      </div>
    </div>
    <div class="promo-card">
      <img src="../images/promo3.png" alt="Promo" />
      <div class="promo-text">
        <h4>Mellow Mood</h4>
        <p>Free Ice Cream Offer</p>
      </div>
    </div>
  </section>
<!-- search bar -->
  <section class="product-filters container">
    

    <div class="category-search" style="margin-bottom: 20px;">
        <select id="categoryFilter" class="menu-search">
            <option value="">All Categories</option>
            <?php foreach ($foodTypes as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (isset($_GET['foodType']) && $_GET['foodType'] == $type) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>


     <input type="text" placeholder="Search from menu..." class="menu-search" id="menuSearch"/>
  </section>
  
  <section class="product-section container">
  <div class="container">
        
        <div class="product-grid">
            <?php if (!empty($dishes)): ?>
              <?php foreach ($dishes as $r): ?>
    <div class="product-card" data-category="<?php echo strtolower(htmlspecialchars($r['foodType'])); ?>">
       
            <div class="product-image">
                <img src="../uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($r['foodName']); ?>">
            </div>

        <div class="product-info">
            <h3 class="product-name"><?php echo htmlspecialchars($r['foodName']); ?></h3>
            <p class="product-category"><?php echo htmlspecialchars($r['foodDetail'] ?? 'No description available'); ?></p>
            <div class="price-btn-block">
                <span class="product-price">RM <?php echo number_format(htmlspecialchars($r['price'] ?? 0), 2); ?></span>
                <button class="btn order-btn"
                    data-food-id="<?php echo htmlspecialchars($r['foodId']); ?>"
                    data-food-name="<?php echo htmlspecialchars($r['foodName']); ?>"
                    data-food-price="<?php echo number_format(htmlspecialchars($r['price'] ?? 0), 2); ?>"
                    data-food-image="../uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>"
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

  <!-- Reviews -->
 <section class="reviews" id="reviews">
    <h2>Customer Reviews Paradise</h2>
    <div class="review-list">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
              <a href="reviewPage.php?reviewId=<?php echo $r['reviewId']; ?>" class="comment-link">
                <div class="review-box">
                    <p class="stars"><?php echo str_repeat('★', $r['rating']); ?></p>
                    <p class="reviewer"><?php echo htmlspecialchars($r['name']); ?></p>
                        <p class="comment"><?php echo htmlspecialchars($r['comment']); ?></p>
                    
                    <!-- <?php if (!empty($r['reviewMedia'])): ?>
                        <div class="review-media">
                            <img src="<?php echo htmlspecialchars($r['reviewMedia']); ?>" alt="Review media" class="review-img" />
                        </div>
                    <?php endif; ?> -->
                </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews available.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="review-pagination">
        <!-- Previous Button -->
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>#reviews" class="prev">← Previous</a>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>#reviews" class="next">Next →</a>
        <?php endif; ?>
    </div>
</section>

<script>
// Save scroll position before page unload
window.addEventListener('beforeunload', function () {
    localStorage.setItem('scrollPosition', window.scrollY);
});

// Restore scroll position after page load
window.addEventListener('load', function () {
    const savedPosition = localStorage.getItem('scrollPosition');
    if (savedPosition) {
        window.scrollTo(0, savedPosition);  // Restore the scroll position
        localStorage.removeItem('scrollPosition');  // Remove the stored value after restoring
    }
});
</script>


</main>

<?php include '../component/footer.php'; ?>
    
    <script>
        const heroBanner = document.getElementById('heroBanner');
  const heroImage = document.getElementById('heroImage');
  const ratingBox = document.getElementById('ratingBox');
  const tagline = document.getElementById('tagline');
  const openTime = document.getElementById('openTime');

  const slides = [
    {
      bg: "../images/burger.png",
      img: "../images/burger.png",
      rating: "3.4",
      reviews: "1,360 reviews",
      tagline: "I'm lovin' it!",
      time: "Open until 6:00 AM"
    },
    {
      bg: "../images/cake1.png",
      img: "../images/cake1.png",
      rating: "4.7",
      reviews: "980 reviews",
      tagline: "Baked Fresh Daily",
      time: "Open until 4:00 PM"
    },
    {
      bg: "../images/promo3.png",
      img: "../images/promo3.png",
      rating: "4.9",
      reviews: "2,000 reviews",
      tagline: "Cool Down with Our Drinks",
      time: "Open until Midnight"
    }
  ];

  let index = 0;

  function rotateHero() {
    const current = slides[index];
    heroBanner.style.backgroundImage = `url('${current.bg}')`;
    heroImage.src = current.img;
    ratingBox.innerHTML = `<p>${current.rating}</p><small>${current.reviews}</small>`;
    tagline.innerText = current.tagline;
    openTime.innerHTML = `<i class="fa fa-clock"></i> ${current.time}`;

    index = (index + 1) % slides.length;
  }

  setInterval(rotateHero, 5000);
  
  const forms = document.querySelectorAll('.add-to-cart-form');

  forms.forEach(form => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      const response = await fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      if (result.status === 'success') {
  alert("Item added to cart successfully!");
  location.reload();
} else if (result.status === 'error') {
  alert(result.message); 
}
    });
  });

  document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value;
    const urlParams = new URLSearchParams(window.location.search);
    if (selectedCategory) {
        urlParams.set('foodType', selectedCategory);
    } else {
        urlParams.delete('foodType');
    }
    window.location.search = urlParams.toString();
});


// Search by Name
document.getElementById('menuSearch').addEventListener('keyup', function() {
    var searchText = this.value.toLowerCase();
    var productCards = document.querySelectorAll('.product-card');

    productCards.forEach(function(card) {
        var name = card.querySelector('.product-name').innerText.toLowerCase();
        if (name.includes(searchText)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
});

</script>

</body>
</html>