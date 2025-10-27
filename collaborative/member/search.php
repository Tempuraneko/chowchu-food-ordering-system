<?php
require '../component/connect.php';
require '../component/header.php';

// Get the search query and selected category (if any)
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Build the base SQL query
$sql = "SELECT foodId, foodName, foodDetail, foodImage, price, foodType FROM fooddetail WHERE 1=1";

// Add conditions for search query and category filter
if (!empty($searchQuery)) {
    $sql .= " AND foodName LIKE :searchQuery";
}
if (!empty($selectedCategory)) {
    $sql .= " AND foodType = :foodType";
}

// Prepare the statement
$stmt = $pdo->prepare($sql);

// Bind parameters for search and category
if (!empty($searchQuery)) {
    $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%');
}
if (!empty($selectedCategory)) {
    $stmt->bindValue(':foodType', $selectedCategory);
}

try {
    $stmt->execute();
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Get distinct food types for the category dropdown
try {
    $typeStmt = $pdo->query("SELECT DISTINCT foodType FROM fooddetail WHERE foodType IS NOT NULL AND foodType != ''");
    $foodTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
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

  <section class="product-filters container">
    <h2>All Offers from ChowChu</h2>

    <!-- Category Filter Dropdown -->
    <form method="GET" action="">
        <div class="category-search" style="margin-bottom: 20px;">
            <select id="categoryFilter" name="category" class="menu-search">
                <option value="">All Categories</option>
                <?php foreach ($foodTypes as $type): ?>
                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($selectedCategory === $type) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Search Input -->
        <input type="text" placeholder="Search from menu..." class="menu-search" id="menuSearch" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" />
        <button type="submit" class="btn">Search</button>
    </form>
  </section>

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

  <!-- Product Section -->
  <section class="product-section container">
    <div class="container">
        <h2>Popular Dishes of the Month</h2>
        <p class="lead">Easiest way to order your favourite food among these top 6 dishes</p>
        <div class="product-grid">
            <?php if (!empty($dishes)): ?>
                <?php foreach ($dishes as $r): ?>
                    <div class="product-card" data-category="<?php echo strtolower(htmlspecialchars($r['foodType'])); ?>">
                        <a href="foodDetail.php?foodId=<?php echo htmlspecialchars($r['foodId']); ?>">
                            <div class="product-image">
                                <img src="../uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($r['foodName']); ?>">
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
                                    data-food-image="../uploaded_files/<?php echo htmlspecialchars($r['foodImage'] ?? 'default.jpg'); ?>"
                                    data-food-detail="<?php echo htmlspecialchars($r['foodDetail'] ?? 'No description available'); ?>"
                                >+ </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No dishes found.</p>
            <?php endif; ?>
        </div>
    </div>
  </section>

</main>

<?php include '../component/footer.php'; ?>

</body>
</html>
