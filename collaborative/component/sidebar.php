<?php
// sidebar.php
include 'connect.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$studentID = $_SESSION['studentID'] ?? null;
$profileImage = 'user.png'; 

if ($studentID) {
    try {
        $stmt = $pdo->prepare("SELECT profileImage FROM student WHERE studentID = :studentID");
        $stmt->execute(['studentID' => $studentID]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($student && !empty($student['profileImage'])) {
            $profileImage = $student['profileImage'];
        }
    } catch (PDOException $e) {
    }
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="profile-pic">
            <img src="../<?= htmlspecialchars($profileImage) ?>" alt="Profile Picture">
        </div>
        <div class="username"><?= htmlspecialchars($_SESSION['name'] ?? 'Username') ?></div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item <?= $currentPage === 'myVoucher.php' ? 'active' : '' ?>">
            <a href="myVoucher.php"><i class="fas fa-ticket-alt"></i> My Voucher</a>
        </li>
        <li class="menu-item <?= $currentPage === 'usedVoucher.php' ? 'active' : '' ?>">
            <a href="usedVoucher.php"><i class="fas fa-receipt"></i> Used Voucher</a>
        </li>
        <li class="menu-item <?= $currentPage === 'specialOffers.php' ? 'active' : '' ?>">
            <a href="specialOffers.php"><i class="fas fa-gift"></i> Special Offers</a>
        </li>
    </ul>
</div>


<style>
    /* sidebar.css */
    .sidebar {
        width: 250px;
        background-color: #fff;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 50px 20px 20px 20px;
        /* height: 100vh; */
        transition: width 0.3s ease;
        display: block;
        
    }

    .sidebar-header {
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .profile-pic img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 15px;
    }

    .username {
        font-weight: bold;
        font-size: 1.2rem;
        color: #333;
        flex-grow: 1;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        margin-top: 20px;
    }

    .menu-item {
        margin-bottom: 15px;
    }

    .menu-item a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #555;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .menu-item a:hover {
        background-color: #f8f8f8;
        color: #f28500;
        transform: translateX(5px);
        /* Adds slight hover effect */
    }

    .menu-item.active a {
        background-color: #f28500;
        color: white;
        font-weight: bold;
    }

    .menu-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* Responsive Design for Mobile */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
        padding: 15px;
    }

    .menu-item a {
        font-size: 0.9rem;
    }

    .profile-pic img {
        width: 35px;
        height: 35px;
    }

    /* Adjust for content */
    .main-content {
        margin-left: 200px; /* Adjust for smaller sidebar width */
    }
}

@media (max-width: 480px) {
    .sidebar {
        padding: 15px;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1000;
    }

    .main-content {
        margin-left: 0; /* Full width for content */
        padding-top: 20px;
    }
}
</style>