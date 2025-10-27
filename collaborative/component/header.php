<?php
session_start();
ob_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION['studentID']); 
$studentID = $loggedIn ? $_SESSION['studentID'] : null;

if ($loggedIn) {
    $sql = "SELECT * FROM student WHERE studentID = :studentID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':studentID', $studentID, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        $userImage = $student['profileImage'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartData = file_get_contents('php://input'); 
    $cart = json_decode($cartData, true); 

    if (is_array($cart)) {
        $_SESSION['cart'] = $cart; 
    }
}

// Retrieve the cart data from the session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$uniqueProductCount = 0;
$total = 0;

// Calculate the total number of unique products and the total price
foreach ($cartItems as $item) {
    // Make sure 'price' and 'quantity' exist before using them
    $price = isset($item['price']) ? $item['price'] : 0;
    $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
    $uniqueProductCount++;  
    $total += $price * $quantity; 
}

$subtotal = $total;  
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chow Chu Shop</title>

    <link rel="shortcut icon" href="../image/chowChu.png">

    <!-- External Stylesheets -->
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/order.css">
    <link rel="stylesheet" href="../css/trackOrder.css">
    <link rel="stylesheet" href="../css/order_detail.css">
    <link rel="stylesheet" href="../css/account.css">
    <!-- <link rel="stylesheet" href="../css/admin_form.css"> -->
    <link rel="stylesheet" href="../css/popularFood.css">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Add Bootstrap CSS (Include this in your <head>) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add Bootstrap JavaScript (Include this before </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/order.js" defer></script>

    <script type="text/javascript" src="../js/darkmode.js" defer></script>

</head>

<body>

    <header>
        <div class="header-container">
        <h1>
            <a href="../index.php">
                <img src="../image/chowChu.png" alt="Mellow Mood Logo" class="logo">
                Chow Chu
            </a>
        </h1>

        
        <div class="nav-bar">
            <a href="../index.php" class="nav-item home-btn" id="homeBtn">Home</a>
            <a href="../member/menu.php" class="nav-item" id="browseMenuBtn">Browse Menu</a>
            <a href="../member/specialOffers.php" class="nav-item" id="specialOffersBtn">Special Offers</a>
            <a href="../member/trackOrder.php" class="nav-item" id="trackOrderBtn">Track Order</a>
        </div>

            <div class="header-icons">
                <a href="#" id="cartButton">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="item_count"><?php echo isset($uniqueProductCount) ? $uniqueProductCount : 0; ?></span> 
                </a>

                <!-- Cart Modal -->
                <div id="cartModal" class="cart-modal">
                    <div class="cart-modal-content">
                        <h2>Shopping Cart</h2>
                        <div id="cart-items" class="cart-items"></div>

                        <!-- Cart Summary -->
                        <div class="cart-summary">
                            <p>Subtotal: RM <span id="subtotal">0.00</span></p>
                            <p>Total: RM <span id="total">0.00</span></p>
                            <button id="proceedToCheckout" class="btn checkout-btn">Proceed to Checkout</button>
                        </div>

                        <button id="closeCartModal" class="btn close-cart-btn">Close</button>
                    </div>
                </div>

                <!-- User dropdown -->
                <div class="user-dropdown">
                    <span id="user-text">
                        <?php if (isset($_SESSION['studentID']) && !empty($_SESSION['studentID'])): ?>
                            <!-- If user is logged in, show the profile image with a dropdown -->
                            <a href="" class="logout-btn" id="profile-btn">
                            <?php
                                $userImagePath = isset($userImage) && !empty($userImage) ? "../" . $userImage : "../images/default-image.jpg";
                            ?>
                            <img src="<?php echo htmlspecialchars($userImagePath); ?>" alt="Profile Image" class="profile-img">
                            </a>
                        <?php else: ?>
                            <!-- If user is NOT logged in, show Login text with icon -->
                            <a href="../member/login.php" class="login-signup-btn">
                                <i class="fa fa-user"></i> Login/Signup
                            </a>
                        <?php endif; ?>
                    </span>

                    <!-- Dropdown menu  -->
                    <div class="dropdown-menu" id="dropdown-menu">
                        <?php if (isset($_SESSION['studentID']) && !empty($_SESSION['studentID'])): ?>
                            <ul>
                                <li><a href="../member/orderHistory.php">Order History</a></li>
                                <li><a href="../member/moodBasedSuggest.php" class="none">Mood Suggest</a></li>
                                <li><a href="../member/account.php">Profile</a></li>
                                <li><a href="../member/logout.php">Logout</a></li> 
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>


                <button id="theme-switch" class="darkmode">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Z"/></svg>
                </button>
                
            </div>
        </div>
    </header>

</body>

<script>
    // Toggle dropdown visibility on profile icon click
    document.getElementById('profile-btn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default anchor behavior (like page scroll)
        var dropdownMenu = document.getElementById('dropdown-menu');
        dropdownMenu.style.display = (dropdownMenu.style.display === 'block' ? 'none' : 'block');
    });

    // Optional: Hide dropdown if the user clicks outside of the dropdown menu
    document.addEventListener('click', function(event) {
        var dropdownMenu = document.getElementById('dropdown-menu');
        if (!dropdownMenu.contains(event.target) && !document.getElementById('profile-btn').contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
    
</script>

<script>
    // Get the current page URL
    const currentPage = window.location.pathname.split('/').pop();

    // Remove the 'active' class from all links
    document.querySelectorAll('.nav-item').forEach(link => {
        link.classList.remove('actives');
    });

    // Add the 'active' class to the current page link
    if (currentPage === 'index.php') {
        document.getElementById('homeBtn').classList.add('actives');
    } else if (currentPage === 'menu.php') {
        document.getElementById('browseMenuBtn').classList.add('actives');
    } else if (currentPage === 'specialOffers.php') {
        document.getElementById('specialOffersBtn').classList.add('actives');
    } else if (currentPage === 'trackOrder.php') {
        document.getElementById('trackOrderBtn').classList.add('actives');
    }
</script>

</html>
