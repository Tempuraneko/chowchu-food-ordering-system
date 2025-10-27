<?php
session_start();
ob_start();

include '../component/connect.php';
include '../component/_base2.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Dashboard</title>

   <!-- Font Awesome for Icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Boxicons for Additional Icons -->
   <link href="https://unpkg.com/boxicons/css/boxicons.min.css" rel="stylesheet">

   <!-- Error Message -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin.css">
   <link rel="stylesheet" href="../css/admin_form.css">
   <link rel="stylesheet" href="../css/admin_product.css">
   <link rel="stylesheet" href="../css/dashboard.css">

   <!-- Custom jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="../js/admin.js"></script>
   <script src="../js/adminForm.js"></script>
   <script src="../js/foodpage.js"></script>
   <script type="text/javascript" src="../js/sidebar.js" defer></script>

</head>

<body>

   <header class="header">

      <section class="flex">
         <a href="dashboard.php" class="logo">Admin Dashboard</a>

         <!-- Icons -->
         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <!-- <div id="search-btn" class="fas fa-search"></div> -->
            <div id="user-btn" class="fas fa-user"></div>
            <div id="dropdown-menu" class="dropdown-menu">
               <div class="user-info">
                  <img src="../image/<?= htmlspecialchars($_user['profilePicture'] ?? 'userLogo.png') ?>" alt="Profile Picture" class="profile-pic">
                  <span class="user-name">
                     <?= htmlspecialchars($_user['name'] ?? 'Staff') ?>
                  </span>
                  <span class="user-name">
                     <?= htmlspecialchars($_user['role'] ?? 'Staff') ?>
                  </span>
               </div>
               <ul>
                  <li><a href="../admin/adminProfile.php">Profile</a></li>
                  <li><a href="../logout.php">Logout</a></li>
               </ul>
            </div>
         </div>

         <!-- Profile Dropdown -->
         <div class="profile">
            <?php if (isset($_user['name'])): ?>
               <img src="../uploaded_files/<?= htmlspecialchars($_user['profilePicture']); ?>" alt="Profile Picture">
               <div class="name-and-role">
                  <h3><?= htmlspecialchars($_user['name']); ?></h3>
                  <span><?= htmlspecialchars($_user['role']); ?></span>
               </div>
               <a href="../admin/adminProfile.php" class="droplist-btn">View Profile</a>
               <a href="../admin/adminLogout.php" class="droplist-btn">Logout</a>
            <?php else: ?>
               <h3>Please login your account</h3>
               <div class="flex-btn">
                  <a href="../staffLogin.php" class="droplist-btn">Login</a>
               </div>
            <?php endif; ?>
         </div>

      </section>
   </header>

   <div class="side-bar">
      <div class="close-side-bar">
         <i class="fas fa-times"></i>
      </div>

      <div class="profile">
         <?php if (isset($_user['name'])): ?>
            <img src="../uploaded_files/<?= htmlspecialchars($_user['profilePicture']); ?>" alt="Profile Picture">
            <div class="name-and-role">
               <h3><?= htmlspecialchars($_user['name']); ?></h3>
               <span><?= htmlspecialchars($_user['role']); ?></span>
            </div>
            <a href="../admin/adminProfile.php" class="droplist-btn">View Profile</a>
            <a href="../admin/adminLogout.php" class="droplist-btn">Logout</a>
         <?php else: ?>
            <h3>Please login your account</h3>
            <div class="flex-btn">
               <a href="../staffLogin.php" class="droplist-btn">Login</a>
            </div>
         <?php endif; ?>
      </div>

      <!-- Sidebar Navigation -->
      <nav class="navbar">
         <ul class="side-menu top">
            <!-- <li class="active">
               <a href="dashboard.php">
                  <i class='bx bxs-dashboard'></i>
                  <span>Dashboard</span>
               </a>
            </li> -->
            <li>
               <a href="adminFood.php">
                  <i class='bx bxs-shopping-bag-alt'></i>
                  <span>Food</span>
               </a>
            </li>
            <!-- <li>
               <a href="adminFoodM.php">
                  <i class='bx bxs-shopping-bag-alt'></i>
                  <span>Product Image</span>
               </a>
            </li> -->
            <li>
               <a href="../admin/adminOrder.php">
                  <i class='bx bxs-doughnut-chart'></i>
                  <span>Order</span>
               </a>
            </li>
            <!-- <li>
               <a href="../admin/adminPromotion.php">
                  <i class='bx bxs-megaphone'></i>
                  <span>Voucher</span>
               </a>
            </li>
            <li>
               <a href="../admin/adminRating_Review.php">
                  <i class='bx bxs-message-dots'></i>
                  <span>Rating & Review</span>
               </a>
            </li>
            <li>
               <a href="adminStaff.php">
                  <i class='bx bxs-group'></i>
                  <span>Staff</span>
               </a>
            </li>
            <li>
               <a href="../admin/adminMember.php">
                  <i class='bx bxs-user'></i>
                  <span>Customer</span>
               </a>
            </li>
            <li>
               <a href="report.php">
                  <i class='bx bx-bar-chart'></i>
                  <span>Report</span>
               </a>
            </li> -->
         </ul>
         <ul class="side-menu">
            <li>
               <a href="../admin/adminLogout.php" class="logout">
                  <i class='bx bxs-log-out-circle'></i>
                  <span>Logout</span>
               </a>
            </li>
         </ul>
      </nav>
   </div>
   <script src="../jQuery/admin.js"></script>

</body>

</html>