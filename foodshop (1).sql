-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2025 at 02:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `fooddetail`
--

CREATE TABLE `fooddetail` (
  `id` int(11) NOT NULL,
  `foodId` varchar(255) DEFAULT NULL,
  `foodName` varchar(255) DEFAULT NULL,
  `foodImage` varchar(255) DEFAULT NULL,
  `foodDetail` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `foodType` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fooddetail`
--

INSERT INTO `fooddetail` (`id`, `foodId`, `foodName`, `foodImage`, `foodDetail`, `price`, `status`, `foodType`) VALUES
(1, 'asd', 'Perfect Pizza Dough', '67d64f0c403352.89186139.webp', 'Served with tomato sauce, chicken pepperoni, and mozzarella cheese.', 20, 'New', 'Pizza'),
(2, 'F0001', 'Personal Veggie Lover', '67d65079e2076.jpg', 'Topped with tomato sauce, mushrooms, pineapples, tomatoes, capsicums, onions, and mozzarella cheese. (Contains garlic, onions and cheese which may not be suitable for vegetarians).\r\n', 10, 'Most Popular', 'Veggie'),
(3, 'F0002', 'Personal Super Supreme', '67d65293d084f.webp', 'Topped with tomato sauce, ground beef, beef pepperoni, beef cabanossi, chicken loaf, mushrooms, pineapples, capsicums, olives, onions, mozzarella cheese.', 10, 'Out of Stock', 'Pizza'),
(4, 'F0003', 'Cheesy Carbonara Spaghetti', '67d65a5e51d1f.jpg', 'Spaghetti with Creamy Carbonara sauce, chicken rolls, mushrooms and herbs with cheesy sauce.', 10, 'N/A', 'Noodle'),
(5, 'F0004', 'Krispy Curly Fries', '67d66242e070c.jpeg', 'Golden fried curly potato fries.', 10, 'N/A', 'Sidedish'),
(6, 'F0005', 'Super Moist Chocolate Cupcakes', '67d6631dc05e9.jpg', 'Use natural cocoa powder and buttermilk. These chocolate cupcakes taste completely over-the-top with chocolate buttercream!', 5, 'N/A', 'Cake'),
(9, 'F0006', 'Italian Chopped Salad', '6822011730e95.jpg', 'Served with a mix of crisp romaine, tomatoes, cucumbers, and pepperoncini, tossed in zesty Italian vinaigrette, and topped with salami, provolone, and olives.\r\n\r\n', 10, 'N/A', 'Salad'),
(10, 'F0007', 'Homemade Caesar Salad', '682201774da84.jpg', 'Served with fresh romaine, house-made croutons, and creamy Caesar dressing, finished with shaved Parmesan and cracked black pepper.\r\n\r\n', 9, 'N/A', 'Salad'),
(11, 'F0008', 'Arugula Salad', '682201a15d336.jpg', 'Served with peppery arugula, sweet cherry tomatoes, and shaved Parmesan, drizzled with a light lemon vinaigrette.', 5, 'N/A', 'Salad'),
(12, 'F0009', 'Pomegranate juice', '682201d1745d8.jpg', 'Served chilled, this vibrant juice is bursting with antioxidants and offers a sweet-tart ', 2, 'N/A', 'Drink'),
(13, 'F0010', 'Apple juice', '682201f719001.jpeg', 'Served cold, this crisp and refreshing juice is made from freshly pressed apples, delivering a naturally sweet taste that’s perfect for any occasion.', 3, 'N/A', 'Drink'),
(14, 'F0011', 'Beet juice', '68220215ef6cb.jpg', 'Served chilled, this earthy and nutrient-rich juice is packed with vitamins and has a slightly sweet flavor, making it a healthy and invigorating choice.', 4, 'New', 'Drink');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderId` int(11) NOT NULL,
  `studentId` varchar(255) DEFAULT NULL,
  `voucherId` int(11) DEFAULT NULL,
  `orderStatus` varchar(255) DEFAULT NULL,
  `orderDate` datetime DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `discountAmount` decimal(10,2) DEFAULT NULL,
  `orderCreatedAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderId`, `studentId`, `voucherId`, `orderStatus`, `orderDate`, `subtotal`, `totalAmount`, `discountAmount`, `orderCreatedAt`) VALUES
(239, 'STU003', NULL, 'Completed', '2025-04-28 15:59:55', 20, 21.2, NULL, '2025-04-28 14:00:14'),
(240, 'STU003', NULL, 'Preparing', '2025-04-28 16:08:52', 20, 21.2, NULL, '2025-04-28 14:09:08'),
(241, 'STU003', NULL, 'Preparing', '2025-04-28 16:22:15', 20, 21.2, NULL, '2025-04-28 14:22:31'),
(242, 'STU003', NULL, 'Completed', '2025-04-28 16:31:38', 20, 21.2, NULL, '2025-04-28 14:31:55'),
(243, 'STU003', NULL, 'Preparing', '2025-04-28 16:35:22', 20, 21.2, NULL, '2025-04-28 14:35:39'),
(244, 'STU003', NULL, 'Cancelled', '2025-04-28 16:36:35', 20, 21.2, NULL, '2025-04-28 14:36:50'),
(245, 'STU003', NULL, 'Completed', '2025-04-28 16:38:42', 20, 21.2, NULL, '2025-04-28 14:39:01'),
(246, 'STU003', NULL, 'Completed', '2025-04-28 16:40:30', 20, 21.2, NULL, '2025-04-28 14:40:42'),
(247, 'STU003', NULL, 'Completed', '2025-04-29 09:24:37', 20, 21.2, NULL, '2025-04-29 07:24:52'),
(250, 'STU003', 4, 'Completed', '2025-05-01 12:04:05', 18, 16.08, NULL, '2025-05-01 10:04:21'),
(251, 'STU003', 4, 'Completed', '2025-05-01 12:12:34', 20, 18.2, NULL, '2025-05-01 10:12:46'),
(252, 'STU003', NULL, 'Preparing', '2025-05-03 04:13:43', 80, 84.8, NULL, '2025-05-03 02:13:53'),
(253, 'STU003', NULL, 'Completed', '2025-05-03 15:17:10', 60, 63.6, NULL, '2025-05-03 13:17:24'),
(254, 'STU003', NULL, 'Completed', '2025-05-06 07:14:42', 20, 21.2, NULL, '2025-05-06 05:16:29'),
(255, 'STU003', NULL, 'Completed', '2025-05-06 07:18:37', 20, 21.2, NULL, '2025-05-06 05:19:02'),
(256, 'STU003', NULL, 'Completed', '2025-05-06 12:36:32', 20, 21.2, NULL, '2025-05-06 10:36:42'),
(257, 'STU003', NULL, 'Completed', '2025-05-06 15:01:56', 20, 21.2, NULL, '2025-05-06 13:04:42'),
(258, 'STU003', NULL, 'Preparing', '2025-05-06 15:04:53', 20, 21.2, NULL, '2025-05-06 13:25:26'),
(259, 'STU003', NULL, 'Completed', '2025-05-06 15:30:16', 5, 5.3, NULL, '2025-05-06 13:30:29'),
(260, 'STU003', NULL, 'Completed', '2025-05-06 16:56:44', 40, 42.4, NULL, '2025-05-06 14:56:56'),
(261, 'STU003', NULL, 'Completed', '2025-05-06 17:57:36', 80, 84.8, NULL, '2025-05-06 16:06:08'),
(262, 'STU003', 3, 'Completed', '2025-05-06 19:45:29', 45, 31.8, NULL, '2025-05-06 17:45:40'),
(263, 'STU003', NULL, 'Cancelled', '2025-05-07 13:41:01', 10, 10.6, NULL, '2025-05-07 11:41:15'),
(264, 'STU003', NULL, 'Completed', '2025-05-07 13:49:40', 20, 21.2, NULL, '2025-05-07 11:49:55'),
(265, 'STU003', NULL, 'Cancelled', '2025-05-08 16:09:09', 10, 10.6, NULL, '2025-05-08 14:09:22'),
(266, 'STU003', NULL, 'Cancelled', '2025-05-08 16:21:10', 10, 10.6, NULL, '2025-05-08 14:21:21'),
(267, 'STU003', NULL, 'Completed', '2025-05-08 20:07:40', 10, 10.6, NULL, '2025-05-08 18:07:50'),
(268, 'STU003', NULL, 'Cancelled', '2025-05-11 08:17:46', 40, 42.4, NULL, '2025-05-11 06:18:00'),
(269, 'STU003', NULL, 'Completed', '2025-05-11 08:43:41', 10, 10.6, NULL, '2025-05-11 06:43:56'),
(270, 'STU003', NULL, 'Completed', '2025-05-11 09:40:45', 20, 21.2, NULL, '2025-05-11 07:40:57'),
(271, 'STU003', NULL, 'Completed', '2025-05-11 10:24:44', 10, 10.6, NULL, '2025-05-11 08:25:16'),
(276, 'STU003', NULL, 'Preparing', '2025-05-12 18:09:23', 10, 10.6, NULL, '2025-05-12 16:13:29'),
(277, 'STU003', NULL, 'Preparing', '2025-05-13 09:21:20', 10, 10.6, NULL, '2025-05-13 07:21:34'),
(278, 'STU007', NULL, 'Preparing', '2025-05-13 19:06:35', 20, 21.2, NULL, '2025-05-13 17:07:04'),
(279, 'STU007', NULL, 'Completed', '2025-05-15 04:51:55', 10, 10.6, NULL, '2025-05-15 02:52:13'),
(280, 'STU007', NULL, 'Completed', '2025-05-15 05:24:22', 10, 10.6, NULL, '2025-05-15 03:24:34'),
(281, 'STU007', NULL, 'Pending', '2025-05-15 06:52:27', 10, 10.6, NULL, '2025-05-15 04:52:41'),
(282, 'STU007', 7, 'Pending', '2025-05-15 06:53:49', 10, 0.6, NULL, '2025-05-15 04:54:05'),
(283, 'STU007', 8, 'Completed', '2025-05-15 06:55:05', 30, 28.8, NULL, '2025-05-15 04:56:42'),
(285, 'STU007', 9, 'Pending', '2025-05-15 11:37:14', 10, 7.6, NULL, '2025-05-15 09:37:58'),
(286, 'STU007', 10, 'Cancelled', '2025-05-15 11:43:29', 20, 18.2, NULL, '2025-05-15 09:43:44'),
(287, 'STU007', NULL, 'Pending', '2025-05-15 11:47:43', 20, 21.2, 0.00, '2025-05-15 09:50:13'),
(288, 'STU007', 11, 'Pending', '2025-05-15 11:52:18', 20, 18.2, 3.00, '2025-05-15 09:52:32'),
(289, 'STU007', 12, 'Pending', '2025-05-15 11:53:42', 20, 18.2, 3.00, '2025-05-15 09:53:56'),
(290, 'STU007', 13, 'Preparing', '2025-05-15 11:55:46', 20, 18.2, 3.00, '2025-05-15 09:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `orderItemId` int(11) NOT NULL,
  `orderId` int(11) DEFAULT NULL,
  `foodId` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`orderItemId`, `orderId`, `foodId`, `quantity`, `price`, `totalAmount`) VALUES
(250, 239, 2, 1, 20, 20),
(251, 240, 2, 1, 20, 20),
(252, 241, 2, 1, 20, 20),
(253, 242, 2, 1, 20, 20),
(254, 243, 2, 1, 20, 20),
(255, 244, 2, 1, 20, 20),
(256, 245, 3, 1, 20, 20),
(257, 246, 2, 1, 20, 20),
(258, 247, 2, 1, 20, 20),
(260, 250, 4, 1, 18, 18),
(261, 251, 3, 1, 20, 20),
(262, 252, 2, 4, 20, 80),
(263, 253, 3, 3, 20, 60),
(264, 254, 2, 1, 20, 20),
(265, 255, 2, 1, 20, 20),
(266, 256, 2, 1, 20, 20),
(267, 257, 3, 1, 20, 20),
(268, 258, 3, 1, 20, 20),
(269, 259, 6, 1, 5, 5),
(270, 260, 1, 1, 40, 40),
(271, 261, 2, 2, 20, 40),
(272, 261, 1, 1, 40, 40),
(273, 262, 6, 1, 5, 5),
(274, 262, 2, 2, 20, 40),
(275, 263, 2, 1, 10, 10),
(276, 264, 1, 1, 20, 20),
(277, 265, 2, 1, 10, 10),
(278, 266, 3, 1, 10, 10),
(279, 267, 2, 1, 10, 10),
(280, 268, 1, 2, 20, 40),
(281, 269, 2, 1, 10, 10),
(282, 270, 1, 1, 20, 20),
(283, 271, 2, 1, 10, 10),
(284, 276, 3, 1, 10, 10),
(285, 277, 4, 1, 10, 10),
(286, 278, 2, 1, 10, 10),
(287, 278, 3, 1, 10, 10),
(288, 279, 4, 1, 10, 10),
(289, 280, 4, 1, 10, 10),
(290, 281, 4, 1, 10, 10),
(291, 282, 2, 1, 10, 10),
(292, 283, 2, 1, 10, 10),
(293, 283, 1, 1, 20, 20),
(294, 285, 2, 1, 10, 10),
(295, 286, 1, 1, 20, 20),
(296, 287, 1, 1, 20, 20),
(297, 288, 1, 1, 20, 20),
(298, 289, 1, 1, 20, 20),
(299, 290, 1, 1, 20, 20);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `paymentId` varchar(255) NOT NULL,
  `orderId` int(11) NOT NULL,
  `studentID` varchar(255) NOT NULL,
  `paymentMethod` varchar(50) DEFAULT NULL,
  `paymentStatus` varchar(255) DEFAULT NULL,
  `paymentDate` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transactionReference` varchar(100) DEFAULT NULL,
  `voucherCode` int(11) DEFAULT NULL,
  `discountAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `paymentId`, `orderId`, `studentID`, `paymentMethod`, `paymentStatus`, `paymentDate`, `amount`, `transactionReference`, `voucherCode`, `discountAmount`) VALUES
(52, '', 239, 'STU003', 'card', 'paid', '2025-04-28 16:00:14', 21.20, 'pi_3RIsEBFWhBwjflp00R8cqr6u', NULL, 0.00),
(53, '', 240, 'STU003', 'card', 'paid', '2025-04-28 16:09:08', 21.20, 'pi_3RIsMnFWhBwjflp001DHErnE', NULL, 0.00),
(54, '', 241, 'STU003', 'card', 'paid', '2025-04-28 16:22:31', 21.20, 'pi_3RIsZkFWhBwjflp02ZgU8rJj', NULL, 0.00),
(55, '', 242, 'STU003', 'card', 'paid', '2025-04-28 16:31:55', 21.20, 'pi_3RIsiqFWhBwjflp01tatOrq4', NULL, 0.00),
(56, '', 243, 'STU003', 'card', 'paid', '2025-04-28 16:35:39', 21.20, 'pi_3RIsmSFWhBwjflp01S9goZCS', NULL, 0.00),
(57, '', 244, 'STU003', 'card', 'paid', '2025-04-28 16:36:50', 21.20, 'pi_3RIsnbFWhBwjflp01htOKtqi', NULL, 0.00),
(58, '', 245, 'STU003', 'card', 'paid', '2025-04-28 16:39:01', 21.20, 'pi_3RIspiFWhBwjflp02L2iuxo7', NULL, 0.00),
(59, '', 246, 'STU003', 'card', 'paid', '2025-04-28 16:40:42', 21.20, 'pi_3RIsrMFWhBwjflp0111kjymW', NULL, 0.00),
(60, '', 247, 'STU003', 'card', 'paid', '2025-04-29 09:24:52', 21.20, 'pi_3RJ8XDFWhBwjflp01S1IgcEx', NULL, 0.00),
(62, '', 250, 'STU003', 'card', 'paid', '2025-05-01 12:04:21', 16.08, 'pi_3RJtyhFWhBwjflp00EnKA8Yr', 4, 0.00),
(63, '', 251, 'STU003', 'card', 'paid', '2025-05-01 12:12:46', 18.20, 'pi_3RJu6qFWhBwjflp0172XS2fv', 4, 0.00),
(64, '', 252, 'STU003', 'card', 'paid', '2025-05-03 04:13:53', 84.80, 'pi_3RKVacFWhBwjflp00lkoDl8o', NULL, 0.00),
(65, '', 253, 'STU003', 'card', 'paid', '2025-05-03 15:17:24', 63.60, 'pi_3RKfwUFWhBwjflp017HUwFm5', NULL, 0.00),
(66, '', 254, 'STU003', 'card', 'paid', '2025-05-06 07:16:29', 21.20, 'pi_3RLdruFWhBwjflp01Qo8uel9', NULL, 0.00),
(67, '', 255, 'STU003', 'card', 'paid', '2025-05-06 07:19:02', 21.20, 'pi_3RLduOFWhBwjflp02EufRH9H', NULL, 0.00),
(68, '', 256, 'STU003', 'card', 'paid', '2025-05-06 12:36:42', 21.20, 'pi_3RLirqFWhBwjflp01xrMS5Vj', NULL, 0.00),
(69, '', 257, 'STU003', 'card', 'paid', '2025-05-06 15:04:42', 21.20, 'pi_3RLlB4FWhBwjflp00HcuzGsL', NULL, 0.00),
(70, '', 258, 'STU003', 'card', 'paid', '2025-05-06 15:25:26', 21.20, 'pi_3RLlV1FWhBwjflp01LPIxsaA', NULL, 0.00),
(71, '', 259, 'STU003', 'card', 'paid', '2025-05-06 15:30:29', 5.30, 'pi_3RLla1FWhBwjflp02x4Qh5tI', NULL, 0.00),
(72, '', 260, 'STU003', 'card', 'paid', '2025-05-06 16:56:56', 42.40, 'pi_3RLmvhFWhBwjflp01QfSatNd', NULL, 0.00),
(73, '', 261, 'STU003', 'card', 'paid', '2025-05-06 18:06:08', 84.80, 'pi_3RLo0eFWhBwjflp009SbNUNU', NULL, 0.00),
(74, '', 262, 'STU003', 'card', 'paid', '2025-05-06 19:45:40', 31.80, 'pi_3RLpYzFWhBwjflp01CptSrfE', 3, 0.00),
(75, '', 263, 'STU003', 'card', 'paid', '2025-05-07 13:41:15', 10.60, 'pi_3RM6LcFWhBwjflp01uwPF2nJ', NULL, 0.00),
(76, '', 264, 'STU003', 'card', 'paid', '2025-05-07 13:49:55', 21.20, 'pi_3RM6TyFWhBwjflp00Jf3sxe6', NULL, 0.00),
(77, '', 265, 'STU003', 'card', 'paid', '2025-05-08 16:09:22', 10.60, 'pi_3RMV8bFWhBwjflp00b9XkS4j', NULL, 0.00),
(78, '', 266, 'STU003', 'card', 'paid', '2025-05-08 16:21:21', 10.60, 'pi_3RMVKCFWhBwjflp02xLKjVUE', NULL, 0.00),
(79, '', 267, 'STU003', 'card', 'paid', '2025-05-08 20:07:50', 10.60, 'pi_3RMYrNFWhBwjflp02ETAgfNJ', NULL, 0.00),
(80, '', 268, 'STU003', 'card', 'paid', '2025-05-11 08:18:00', 42.40, 'pi_3RNTD7FWhBwjflp00D42d4IU', NULL, 0.00),
(81, '', 269, 'STU003', 'card', 'paid', '2025-05-11 08:43:56', 10.60, 'pi_3RNTcCFWhBwjflp00pIcevHD', NULL, 0.00),
(82, '', 270, 'STU003', 'card', 'paid', '2025-05-11 09:40:57', 21.20, 'pi_3RNUVNFWhBwjflp00dQFGuWk', NULL, 0.00),
(83, '', 271, 'STU003', 'card', 'paid', '2025-05-11 10:25:16', 10.60, 'pi_3RNVCHFWhBwjflp00glQdn9s', NULL, 0.00),
(84, '', 276, 'STU003', 'card', 'paid', '2025-05-12 18:13:29', 10.60, 'pi_3RNyv3FWhBwjflp02MbQHNmr', NULL, 0.00),
(85, '', 277, 'STU003', 'card', 'paid', '2025-05-13 09:21:34', 10.60, 'pi_3ROD9hFWhBwjflp01ttWjjTR', NULL, 0.00),
(86, '', 278, 'STU007', 'card', 'paid', '2025-05-13 19:07:04', 21.20, 'pi_3ROMIKFWhBwjflp02dVSJzbU', NULL, 0.00),
(87, '', 279, 'STU007', 'card', 'paid', '2025-05-15 04:52:13', 10.60, 'pi_3ROruFFWhBwjflp00aarNTHj', NULL, 0.00),
(88, '', 280, 'STU007', 'card', 'paid', '2025-05-15 05:24:34', 10.60, 'pi_3ROsPYFWhBwjflp014tcQ7dp', NULL, 0.00),
(89, '', 281, 'STU007', 'card', 'paid', '2025-05-15 06:52:41', 10.60, 'pi_3ROtmpFWhBwjflp00thsUXP9', NULL, 0.00),
(90, '', 282, 'STU007', 'card', 'paid', '2025-05-15 06:54:05', 0.00, NULL, 7, 0.00),
(91, '', 283, 'STU007', 'card', 'paid', '2025-05-15 06:56:42', 28.80, 'pi_3ROtqiFWhBwjflp01PVt0OVs', 8, 0.00),
(92, '', 285, 'STU007', 'card', 'paid', '2025-05-15 11:37:58', 7.60, 'pi_3ROyEQFWhBwjflp02XT84N9X', 9, 0.00),
(93, '', 286, 'STU007', 'card', 'paid', '2025-05-15 11:43:44', 18.20, 'pi_3ROyKUFWhBwjflp02MSukl2a', 10, 3.00),
(94, '', 287, 'STU007', 'card', 'paid', '2025-05-15 11:50:13', 21.20, 'pi_3ROyOYFWhBwjflp01zsRzhBJ', NULL, 0.00),
(95, '', 288, 'STU007', 'card', 'paid', '2025-05-15 11:52:32', 18.20, 'pi_3ROyT0FWhBwjflp00Nxrg0lI', 11, NULL),
(96, '', 289, 'STU007', 'card', 'paid', '2025-05-15 11:53:56', 18.20, 'pi_3ROyULFWhBwjflp01Wife9VF', 12, NULL),
(97, '', 290, 'STU007', 'card', 'paid', '2025-05-15 11:55:58', 18.20, 'pi_3ROyWKFWhBwjflp02Y7gz6mn', 13, 3.00);

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `promoID` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `discountAmount` decimal(5,2) DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `imagePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`promoID`, `description`, `type`, `discountAmount`, `expiryDate`, `imagePath`) VALUES
('1', 'Terms and Condition : \n1. Voucher is valid for a one-time use only.\n2. This voucher can be redeemed every semester.\n3. Redemption : Cannot be combined with other vouchers, discounts, or promotions.\n4. Misuse of the voucher or fraudulent GPA submissions may result in disqualification from current and future promotions.', 'GPA 3.75 Rewards : RM 15 Offer ', 15.00, '2025-12-31', NULL),
('2', 'Terms and Condition : \r\n1. Voucher is valid for a one-time use only.\r\n2. This voucher can be redeemed at most 10 times every semester.\r\n3. Redemption : Cannot be combined with other vouchers, discounts, or promotions.\r\n4. Misuse of the voucher or fraudulent attendance submissions may result in disqualification from current and future promotions.', 'Attendance Rewards : RM 3 Offer', 3.00, '2025-12-31', NULL),
('3', 'Terms and Condition : \r\n1. Voucher is valid for a one-time use only.\r\n2. This voucher can be redeemed at most 5 times every semester.\r\n3. Redemption : Cannot be combined with other vouchers, discounts, or promotions.\r\n4. Misuse of the voucher or fraudulent group study proof submissions may result in disqualification from current and future promotions.', 'Group Study Rewards : RM 5 Offer', 5.00, '2025-12-31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reset_tokens`
--

CREATE TABLE `reset_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reset_tokens`
--

INSERT INTO `reset_tokens` (`id`, `email`, `token`, `expires`) VALUES
(1, 'ngjiawei0201@gmail.com', 'a945492d8be99729920ff492c937f83879d564eba9a7938ab21fa5e87b5743042ccd8e3809c85a1cda267341a54c1eafee89', 1746374681);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `reviewId` int(11) NOT NULL,
  `studentId` varchar(11) NOT NULL,
  `orderId` int(11) DEFAULT NULL,
  `foodId` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `reviewDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reviewMedia` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`reviewId`, `studentId`, `orderId`, `foodId`, `rating`, `comment`, `reviewDate`, `reviewMedia`) VALUES
(28, 'STU003', 271, 2, 5, 'Absolutely delicious! The combination of fresh veggies was perfect!', '2025-05-12 15:51:28', '../uploads/6820c5d82ec92_Screenshot_2024-02-13_113102.png,../uploads/6820c5d830293_webcam.png,../uploads/6820c5d830e72_webcam.png'),
(62, 'STU003', 242, 2, 2, 'Not great, the veggies didn\'t seem fresh.', '2025-05-12 15:51:41', '../uploads/68216382f0061_Screenshot_2024-02-13_113051.png,../uploads/68216382f2422_webcam.png,../uploads/68216382f2ead_webcam.png'),
(63, 'STU003', 262, 2, 4, 'Tasty but could use more seasoning', '2025-05-12 15:51:49', NULL),
(64, 'STU003', 267, 2, 3, 'Very good, but I think it could be improved with a bit more flavor.', '2025-05-12 15:52:06', '../uploads/68216ebc4d903_webcam.png,../uploads/68216ebc4e3ed_webcam.png'),
(65, 'STU003', 264, 1, 5, 'Very nice', '2025-05-12 15:15:50', '../uploads/682210a6b28dd_webcam.png'),
(66, 'STU003', 257, 3, 5, 'hello', '2025-05-13 17:11:22', '../uploads/68237d3a3650b_moist-chocolate-cupcakes-5.jpg,../uploads/68237d3a3b5e1_webcam.png,../uploads/68237d3a3d46c_webcam.png');

-- --------------------------------------------------------

--
-- Table structure for table `review_comment`
--

CREATE TABLE `review_comment` (
  `commentId` int(11) NOT NULL,
  `reviewId` int(11) DEFAULT NULL,
  `studentId` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `parentCommentId` int(11) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_comment`
--

INSERT INTO `review_comment` (`commentId`, `reviewId`, `studentId`, `comment`, `parentCommentId`, `createdAt`) VALUES
(6, 64, 'STU003', '@Max asd', 1, NULL),
(27, 64, 'STU003', 'asd', NULL, NULL),
(28, 64, 'STU003', '@Rilly asd', 26, NULL),
(29, 64, 'STU003', '@Max asd', 28, NULL),
(30, 64, 'STU003', '@Max sda', 28, NULL),
(31, 64, 'STU003', '@Max asd', 30, NULL),
(35, 64, 'STU007', '@Max fuck', 28, NULL),
(36, 64, 'STU007', '@Max fuck you', 30, NULL),
(38, 64, 'STU007', '@Rilly asd', 33, NULL),
(40, 64, 'STU007', 'lan jiao', NULL, NULL),
(41, 64, 'STU007', '你妈', NULL, NULL),
(42, 64, 'STU007', '@Rilly 你妈', 34, NULL),
(43, 66, 'STU007', 'hey how well the food', NULL, NULL),
(44, 66, 'STU007', '@Rilly is good ?', 43, NULL),
(45, 64, 'STU007', '@Max asd', 32, NULL),
(47, 64, 'STU007', '@Rilly asd', 46, NULL),
(48, 64, 'STU007', '@Rilly asdasd', 40, NULL),
(50, 64, 'STU007', '@Rilly asdasd', 45, NULL),
(51, 66, 'STU003', '@Rilly asd', 44, NULL),
(52, 66, 'STU003', '@Max asd', 51, NULL),
(54, 64, 'STU003', '@Max asd', 53, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` varchar(10) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `cgpa` decimal(3,2) DEFAULT NULL,
  `profileImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `name`, `email`, `password`, `phone`, `cgpa`, `profileImage`) VALUES
('STU001', 'hello', 'hello@gmail.com', '$2y$10$ztzF2jY5ldyNMxBf4jeZJuKaz397sWSEFwPMIZGlphy...', '01126661281', NULL, 'images/cat.png'),
('STU002', 'hai', 'hai@gmail.com', '$2y$10$Qm5Snm3lmWtw1Tu1UWpyAuRYP7OyV7c/NpXFyGIDC...', '01126661281', NULL, 'images/burger.png'),
('STU003', 'Max', 'dkknight2900@gmail.com', '$2y$10$/yD9Gk7gCGfLyPWEbuOhWeuMrnZG9RImQT1JKxr5PZSafJnwqkade', '017-3459900', NULL, 'images/bg.png'),
('STU004', 'sahur', 'sahur123@gmail.com', '$2y$10$2396gqobQzsx6cGslgWgg.QmcppkxtM9iASfGZmB8eXj0DzfGIXsW', '01234567891', NULL, 'images/images.jpeg'),
('STU005', 'Ng', 'ngjiawei0201@gmail.com', '$2y$10$cONVcKpvYdzOP/kl61EzRukR2t6a4Aiqp7ApcSI/Rw5XJM982Adr6', '01126661281', NULL, 'images/bg.png'),
('STU006', 'Sam', 'waw66627@gmail.com', '$2y$10$HW2o5EhdhDXLJVSZdbNlieIqyv7Rm53.ri47VVukCWQzST2.5t64O', '017-3344555', NULL, 'images/bg.png'),
('STU007', 'Rilly', 'hehek0862@gmail.com', '$2y$10$YlEK/rkqhmCkPZsyQcnf3ODUOkMiGbQ9m/7OcY8SZbmV1erCoar26', '0176618992', NULL, 'images/maxresdefault.jpg'),
('STU008', 'teh', 'hehe@gmail.com', '$2y$10$QlQ0byzz5lLF6i/vvx1viuOJWs9sHYPzDPoFrFXiZkUf9zpYQi3Oa', '019-3455544', NULL, 'images/Screenshot 2025-04-29 141622.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phoneNo` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birth` date NOT NULL,
  `profilePicture` varchar(255) NOT NULL,
  `status` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `voucherID` int(11) NOT NULL,
  `promoID` int(11) NOT NULL,
  `studentID` varchar(10) DEFAULT NULL,
  `isUsed` char(5) NOT NULL DEFAULT 'No',
  `collectDate` date NOT NULL,
  `usedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`voucherID`, `promoID`, `studentID`, `isUsed`, `collectDate`, `usedDate`) VALUES
(3, 1, 'STU003', 'Yes', '2025-05-07', NULL),
(4, 2, 'STU003', 'Yes', '2025-05-01', NULL),
(5, 1, 'STU004', 'No', '2025-05-01', NULL),
(6, 2, 'STU003', 'No', '2025-05-03', NULL),
(7, 1, 'STU007', 'Yes', '2025-05-15', NULL),
(8, 2, 'STU007', 'Yes', '2025-05-15', NULL),
(9, 2, 'STU007', 'Yes', '2025-05-15', NULL),
(10, 2, 'STU007', 'Yes', '2025-05-15', NULL),
(11, 2, 'STU007', 'Yes', '2025-05-15', NULL),
(12, 2, 'STU007', 'Yes', '2025-05-15', NULL),
(13, 2, 'STU007', 'Yes', '2025-05-15', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fooddetail`
--
ALTER TABLE `fooddetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderId`),
  ADD KEY `studentId` (`studentId`),
  ADD KEY `voucherId` (`voucherId`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`orderItemId`),
  ADD KEY `orderId` (`orderId`),
  ADD KEY `foodId` (`foodId`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordersId` (`orderId`),
  ADD KEY `studentsId` (`studentID`),
  ADD KEY `vouchersCode` (`voucherCode`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promoID`);

--
-- Indexes for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`reviewId`),
  ADD KEY `order` (`orderId`),
  ADD KEY `food` (`foodId`);

--
-- Indexes for table `review_comment`
--
ALTER TABLE `review_comment`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `reviewId` (`reviewId`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`voucherID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fooddetail`
--
ALTER TABLE `fooddetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `orderItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `reviewId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `review_comment`
--
ALTER TABLE `review_comment`
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `voucherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `studentId` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentID`),
  ADD CONSTRAINT `voucherId` FOREIGN KEY (`voucherId`) REFERENCES `voucher` (`voucherID`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `foodId` FOREIGN KEY (`foodId`) REFERENCES `fooddetail` (`id`),
  ADD CONSTRAINT `orderId` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `ordersId` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`),
  ADD CONSTRAINT `studentsId` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`),
  ADD CONSTRAINT `vouchersCode` FOREIGN KEY (`voucherCode`) REFERENCES `voucher` (`voucherID`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `food` FOREIGN KEY (`foodId`) REFERENCES `fooddetail` (`id`),
  ADD CONSTRAINT `order` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`);

--
-- Constraints for table `review_comment`
--
ALTER TABLE `review_comment`
  ADD CONSTRAINT `reviewId` FOREIGN KEY (`reviewId`) REFERENCES `review` (`reviewId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
