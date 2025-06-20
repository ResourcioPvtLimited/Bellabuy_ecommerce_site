-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 15, 2025 at 11:05 PM
-- Server version: 8.0.37
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alokito2_ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int NOT NULL,
  `big` varchar(200) NOT NULL,
  `t1` varchar(200) NOT NULL,
  `t2` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `big`, `t1`, `t2`, `link`) VALUES
(9, 'banner.png', 'SUMMER SALE', 'UP TO 50% OFF', 'https://www.facebook.com/'),
(10, 'banner2.png', 'Best Sell', 'UP TO 50% OFF', 'https://www.facebook.com/'),
(11, 'banner.png', 'Go For it', 'UP TO 50% OFF', 'https://www.facebook.com/');

-- --------------------------------------------------------

--
-- Table structure for table `big_deals`
--

CREATE TABLE `big_deals` (
  `id` int NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `link1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `link2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `link3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `link4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `big_deals`
--

INSERT INTO `big_deals` (`id`, `image1`, `link1`, `image2`, `link2`, `image3`, `link3`, `image4`, `link4`) VALUES
(1, 'bigdeal2.png', 'https://www.youtube.com/', 'bigdel3.png', 'https://example.com/deal2', 'bigdeal2.png', 'https://example.com/deal3', 'bigdeal.png\n', 'https://example.com/deal4');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `person` enum('men','women','both') DEFAULT 'both',
  `tag` enum('normal','top_brands') DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo`, `person`, `tag`) VALUES
(1, 'Nike', 'https://logo.clearbit.com/nike.com', 'men', 'top_brands'),
(2, 'Adidas', 'https://logo.clearbit.com/adidas.com', 'men', 'top_brands'),
(3, 'Puma', 'https://logo.clearbit.com/puma.com', 'women', 'top_brands'),
(4, 'Zara', 'https://logo.clearbit.com/zara.com', 'both', 'top_brands'),
(5, 'H&M', 'https://logo.clearbit.com/hm.com', 'women', 'normal'),
(6, 'Uniqlo', 'https://logo.clearbit.com/uniqlo.com', 'both', 'top_brands'),
(7, 'Levi\'s', 'https://logo.clearbit.com/levis.com', 'men', 'top_brands'),
(8, 'Forever 21', 'https://logo.clearbit.com/forever21.com', 'women', 'normal'),
(9, 'Gucci', 'https://logo.clearbit.com/gucci.com', 'both', 'top_brands'),
(10, 'Louis Vuitton', 'https://logo.clearbit.com/louisvuitton.com', 'women', 'top_brands'),
(11, 'Burberry', 'https://logo.clearbit.com/burberry.com', 'men', 'normal'),
(12, 'Zalando', 'https://logo.clearbit.com/zalando.com', 'both', 'top_brands'),
(13, 'Bata', 'https://logo.clearbit.com/bata.com', 'both', 'normal'),
(14, 'Aarong', 'https://logo.clearbit.com/aarong.com', 'women', 'top_brands'),
(15, 'Yellow', 'https://logo.clearbit.com/beximcogroup.com', 'men', 'top_brands');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `u_id` varchar(200) DEFAULT NULL,
  `p_id` varchar(200) DEFAULT NULL,
  `qty` varchar(200) DEFAULT '1',
  `size` varchar(200) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `u_id`, `p_id`, `qty`, `size`, `color`) VALUES
(81, '10', '3', '1', NULL, NULL),
(82, '10', '4', '1', NULL, NULL),
(83, '10', '4', '1', NULL, NULL),
(84, '10', '4', '1', NULL, NULL),
(85, '10', '5', '1', NULL, NULL),
(86, '10', '1', '1', NULL, NULL),
(87, '10', '4', '1', NULL, NULL),
(88, '10', '4', '1', NULL, NULL),
(89, '10', '9', '1', NULL, NULL),
(90, '10', '5', '1', NULL, NULL),
(92, '1', '3', '1', NULL, NULL),
(93, '1', '17', '1', NULL, NULL),
(94, '1', '1', '1', NULL, NULL),
(95, '1', '16', '1', NULL, NULL),
(96, '1', '2', '1', NULL, NULL),
(126, '14', '7', '2', NULL, NULL),
(133, '13', '10', '1', NULL, NULL),
(134, '13', '4', '1', NULL, NULL),
(135, '13', '2', '1', NULL, NULL),
(140, '16', '8', '1', NULL, NULL),
(141, '16', '3', '1', NULL, NULL),
(143, '2', '1', '1', '-', NULL),
(144, '13', '7', '1', NULL, NULL),
(145, '13', '1', '1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cat`
--

CREATE TABLE `cat` (
  `id` int NOT NULL,
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person` enum('men','women','both') COLLATE utf8mb4_general_ci DEFAULT 'both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cat`
--

INSERT INTO `cat` (`id`, `logo`, `cat`, `person`) VALUES
(4, 'https://cdn-icons-png.flaticon.com/512/892/892458.png', 'Shirt', 'men'),
(5, 'https://img.icons8.com/color/48/jeans.png', 'Jeans', 'both'),
(6, 'https://img.icons8.com/color/48/skirt.png', 'Skirt', 'women'),
(7, 'https://img.icons8.com/color/48/jacket.png', 'Jacket', 'both'),
(8, 'https://cdn-icons-png.flaticon.com/512/892/892456.png', 'Slippers', 'both'),
(9, 'https://cdn-icons-png.flaticon.com/512/892/892464.png', 'Eyewear', 'both'),
(10, 'https://cdn-icons-png.flaticon.com/512/892/892461.png', 'Watch', 'men'),
(11, 'https://cdn-icons-png.flaticon.com/512/892/892465.png', 'Backpack', 'both'),
(12, 'https://cdn-icons-png.flaticon.com/512/892/892459.png', 'Kurta', 'men'),
(13, 'https://cdn-icons-png.flaticon.com/512/892/892462.png', 'Saree', 'women');

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE `coupon` (
  `id` int NOT NULL,
  `code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `discount` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `max_use` int DEFAULT NULL,
  `used_yet` int DEFAULT '0',
  `expired` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '0',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `des` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cond` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `max_cart` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon`
--

INSERT INTO `coupon` (`id`, `code`, `discount`, `type`, `max_use`, `used_yet`, `expired`, `date`, `des`, `cond`, `max_cart`) VALUES
(1, 'GET79', '79', 'flat', 100, 0, '0', '2025-06-11 09:20:23', 'Flat BDT 79 off on any order', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cust`
--

CREATE TABLE `cust` (
  `id` int NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `ban` varchar(10) DEFAULT '0',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cust`
--

INSERT INTO `cust` (`id`, `email`, `name`, `lname`, `company`, `phone`, `state`, `city`, `address1`, `address2`, `pincode`, `landmark`, `ban`, `password`) VALUES
(1, 'wedounity@gmail.com', 'New User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', ''),
(3, 'mononhasan0@gmail.com', 'New User', 'm', '', '019', 'Mizoram', ' Demagiri ', 'gg', 'gg', '5666', '66', '0', ''),
(13, NULL, 'Shahriyar Mahmoud', NULL, NULL, '01604337358', NULL, NULL, NULL, NULL, NULL, NULL, '0', '$2y$10$3/fhT0muruUDAUqw51A4ROPaiZk09XwAe96FT2QJtLw5d4yeJcVBu'),
(14, NULL, 'Monon', NULL, NULL, '01916914990', NULL, NULL, NULL, NULL, NULL, NULL, '0', '$2y$10$zJ/we5yipJdbmpnbGY/hX.HgZzmBO/4GbP8MTrpuVMEkRMgA3o6.u'),
(15, NULL, 'Mahin', NULL, NULL, '01759278787', NULL, NULL, NULL, NULL, NULL, NULL, '0', '$2y$10$b6OhumZB.BPA/41aI87bfuNRkS5WiPd03eoav.EzwwxWoOFQU7L/q'),
(16, 'mahinhasnat41@gmail.com', 'Mahin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '$2y$10$3/fhT0muruUDAUqw51A4ROPaiZk09XwAe96FT2QJtLw5d4yeJcVBu');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int NOT NULL,
  `p_id` int NOT NULL,
  `u_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `p_id`, `u_id`) VALUES
(1, 2, 3),
(2, 1, 3),
(3, 4, 3),
(4, 4, 2),
(5, 3, 2),
(6, 2, 2),
(7, 1, 2),
(8, 5, 2),
(9, 6, 2),
(10, 8, 2),
(11, 10, 2),
(12, 11, 2),
(13, 16, 2),
(14, 17, 2);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `stock` varchar(200) DEFAULT '1',
  `num` varchar(200) DEFAULT '0',
  `price` varchar(200) DEFAULT NULL,
  `cat` varchar(200) DEFAULT NULL,
  `subcat` varchar(200) DEFAULT NULL,
  `shop` varchar(200) DEFAULT NULL,
  `img1` varchar(200) DEFAULT NULL,
  `reviews` varchar(200) DEFAULT '0',
  `star` varchar(200) DEFAULT '0',
  `discount` varchar(200) DEFAULT NULL,
  `shop_id` varchar(200) DEFAULT NULL,
  `des_short` text,
  `img2` varchar(200) DEFAULT NULL,
  `img3` varchar(200) DEFAULT NULL,
  `img4` varchar(200) DEFAULT NULL,
  `max_price` varchar(200) DEFAULT NULL,
  `disable` varchar(200) DEFAULT '1',
  `size` varchar(200) DEFAULT NULL,
  `specs` text,
  `state` varchar(200) NOT NULL,
  `colour` varchar(50) DEFAULT NULL,
  `brands_id` int DEFAULT NULL,
  `status` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'on request',
  `person` enum('men','women','both') DEFAULT 'men'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `name`, `stock`, `num`, `price`, `cat`, `subcat`, `shop`, `img1`, `reviews`, `star`, `discount`, `shop_id`, `des_short`, `img2`, `img3`, `img4`, `max_price`, `disable`, `size`, `specs`, `state`, `colour`, `brands_id`, `status`, `person`) VALUES
(1, 'HeadSets For Fashion', '1', '100', '90', '1', '0', 'SSt', '17480872905234140.png', '0', '0', '10', '1', 'dfghj', '17480872909725498.png', '1748087290411725.png', '174808729012705821.png', '100', '0', 'XL,M', 'dfghjk', 'Goa', NULL, 15, 'accepted', 'women'),
(2, 'New Designe ', '1', '47', '90', '1', '0', 'SSt', '1748115178slider-3.png', '0', '0', '10', '1', 'fgjdklsa;cd', '1748114557411725.png', '1748105263411725.png', '17481052639725498.png', '100', '0', 'XL,M', 'VBCM,', 'Assam', NULL, 8, 'accepted', 'men'),
(3, 'Stylest Fashion', '1', '0', '97', '1', '0', 'SSt', '1748115127slider-1-(1).png', '0', '0', '3', '1', 'Accessorize with this bohemian fringe handbag, featuring intricate details and enough space for your essentials. The perfect boho-chic addition to your collection.', '1748115127slider-1.png', '174811512712705821.png', '1748115127411725.png', '100', '0', 'XL,M', 'Accessorize with this bohemian fringe handbag, featuring intricate details and enough space for your essentials. The perfect boho-chic addition to your collection.', 'Andhra Pradesh', NULL, 12, 'accepted', 'men'),
(4, 'ddd', '1', '97', '999', '1', '0', 'hhh', '1748116336Screenshot-2025-04-25-162446.png', '1', '0', '0', '2', 'dfef', '1748116336Screenshot-2025-04-25-162438.png', '1748116336Screenshot-2025-04-25-162454.png', '1748116336Screenshot-2025-04-25-162511.png', '999', '0', '', 'effe', 'Andaman and Nicobar', NULL, 5, 'accepted', 'men'),
(5, 'Bracelet', '1', '0', '100', '2', '0', 'Weed', '1748118189download.jpeg', '0', '0', '0', '4', 'Bracelet for boys', '1748118189download.jpeg', '1748118189download.jpeg', '1748118189download.jpeg', '100', '0', '', 'ajfhioefhi', '--SELECT STATE--', NULL, 1, 'accepted', 'men'),
(6, 'my fashion', '1', '96', '999', '2', '0', 'hhh', '1748116336Screenshot-2025-04-25-162446.png', '1', '0', '10', '2', 'dfef', '1748116336Screenshot-2025-04-25-162438.png', '1748116336Screenshot-2025-04-25-162454.png', '1748116336Screenshot-2025-04-25-162511.png', '554', '0', '', 'effe', 'Andaman and Nicobar', NULL, 2, 'accepted', 'men'),
(7, 'Red Dress', '1', '0', '1200', 'Fashion', 'Dress', NULL, 'img1.jpg', '0', '0', NULL, NULL, 'Beautiful red wedding dress', NULL, NULL, NULL, NULL, '0', NULL, NULL, '1', NULL, 2, 'accepted', 'men'),
(8, 'Silver Earrings', '1', '0', '450', 'Accessories', 'Earrings', NULL, 'img2.jpg', '0', '0', NULL, NULL, 'Elegant silver earrings for women', NULL, NULL, NULL, NULL, '0', NULL, NULL, '1', NULL, 5, 'accepted', 'men'),
(9, 'Smart Watch', '1', '0', '2200', 'Electronics', 'Wearable', NULL, 'img3.jpg', '0', '0', NULL, NULL, 'Smart watch with health tracking', NULL, NULL, NULL, NULL, '0', NULL, NULL, '1', NULL, 4, 'accepted', 'men'),
(10, 'Tet', '1', '0', '100', '1', '0', 'hhhh', '17486277018839_screencapture-alokitoscouts-shop-shop-index-php-2025-05-30-23_12_30.png', '0', '0', '10', '2', 'ceawjbew', '17486277015539_screencapture-alokitoscouts-shop-shop-index-php-2025-05-30-23_12_17.png', '', '', NULL, '0', 'XS,M,L', 'nckwehaidh', 'Arunachal Pradesh', 'Black:#000000,Red:#f00f0f,Unmellow Yellow:#3200e6', 8, 'accepted', 'men'),
(11, 'Stylest Fashion', '20', '0', '90.00', '1', '0', 'hhhh', '17486285635854_screencapture-alokitoscouts-shop-shop-index-php-2025-05-30-23_12_17.png', '0', '0', '10', '2', 'fdtrkj', '', '', '', NULL, '0', 'M', 'fnaerifj', 'Dadra and Nagar Haveli', 'Black:#000000,Red:#ff0000', 1, 'accepted', 'men'),
(12, 'Stylest Fashion', '51', '0', '99.90', '2', '1', 'hhhh', '17486292097613_screencapture-alokitoscouts-shop-shop-index-php-2025-05-30-23_12_17.png', '0', '0', '10', '2', 'dckcuhf,dk', '17486292092023_screencapture-alokitoscouts-shop-shop-index-php-2025-05-30-23_12_30.png', '', '', NULL, '0', 'XS,S,XL', 'dzfdfd', 'Goa', 'Black:#000000,Red:#e60000', 7, 'accepted', 'men'),
(13, 'HeadSets For Fashion', '1', '0', '180.00', '1', '0', 'hhhh', '17486613834903_5e0e77a1-54c9-4fd4-9ed2-bb71a51e4498.jpg', '0', '0', '10', '2', 'jtdtj', '', '', '', NULL, '0', 'S,L', 'kjhn\r\n\r\n\r\neg65', 'Andhra Pradesh', 'Black:#000000,Blue:#0000FF,Red:#ff0000', 9, 'accepted', 'men'),
(14, 'Best HeadSets For Fashion', '1', '0', '180.00', '1', '0', 'hhhh', '17486624795600_c1f184c3-8f16-493f-b1df-6899ddcd8b75.jpg', '0', '0', '10', '2', 'dfgtk', '', '', '', '200', '0', 'M,L,XL', 'sdfgkl', 'Chhattisgarh', 'Red:#FF0000,Au Chico:#975353', 6, 'accepted', 'men'),
(15, 'BD Stylest Fashion', '1', '0', '196.00', '2', '0', 'hhhh', '17486630005214_72f5681485c229d09ab2fd5c6dacf125.jpg', '0', '0', '2', '2', 'bvcxv', '', '', '', '200', '0', 'M,XL', 'nmm', 'Arunachal Pradesh', 'Red:#FF0000,Opium:#856666', 3, 'accepted', 'men'),
(16, 'red', '1', '51', '450.00', '2', '1', 'hhhh', '17486634268556_ChatGPT Image May 26, 2025, 10_52_30 AM.png', '0', '0', '10', '2', 't-shirt', '', '', '', '500', '0', 'S,M,XL', 'sdfghjjgfd', 'Delhi', 'Black:#000000,Alizarin Crimson:#e81717', 12, 'accepted', 'men'),
(17, 'Watch', '1', '5', '900.00', '', '', 'hhhh', '17486640626798_411725.png', '0', '0', '10', '2', 'rtgyhujmlrtgyhujmlrtgyhujmlrtgyhujmlrtgyhujml', '17486640621261_72f5681485c229d09ab2fd5c6dacf125.jpg', '', '', '1000', '0', 'XS,S,L,XXL', 'ewrtyu', '', 'Pink:#FFC0CB,Guardsman Red:#d60000', 10, 'accepted', '');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `user` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `u_id` int DEFAULT NULL,
  `p_id` int DEFAULT NULL,
  `shop_id` varchar(200) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `price` varchar(200) DEFAULT '0',
  `order_id` varchar(200) DEFAULT NULL,
  `status` varchar(200) DEFAULT 'ordered',
  `order_time` varchar(200) DEFAULT NULL,
  `pickup_time` varchar(200) DEFAULT NULL,
  `del_time` varchar(200) DEFAULT NULL,
  `t_id` varchar(200) DEFAULT NULL,
  `coupon` varchar(200) DEFAULT NULL,
  `discount` varchar(200) DEFAULT NULL,
  `size` varchar(200) DEFAULT NULL,
  `paid` varchar(10) NOT NULL,
  `colour` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `u_id`, `p_id`, `shop_id`, `qty`, `price`, `order_id`, `status`, `order_time`, `pickup_time`, `del_time`, `t_id`, `coupon`, `discount`, `size`, `paid`, `colour`) VALUES
(1, 2, 2, '1', 1, '90', 'ID7360224847', 'delivered', '24-05-2025', '24-05-2025', '24-05-2025', '', '', '', '-', 'COD', ''),
(2, 3, 2, '1', 1, '90', 'ID7777014524', 'delivered', '25-05-2025', '25-05-2025', '28-05-2025', 'dfgl,aelwmfih34yr', '', '', '0', 'COD', ''),
(3, 3, 4, '2', 1, '999', 'ID7300015338', 'delivered', '25-05-2025', NULL, '25-05-2025', NULL, '', '', '', 'COD', ''),
(4, 2, 2, '1', 1, '90', 'ID3587021205', 'delivered', '25-05-2025', '28-05-2025', '28-05-2025', '', '', '', '-', 'COD', ''),
(5, 2, 2, '1', 1, '90', 'ID8551113052', 'delivered', '25-05-2025', NULL, '25-05-2025', NULL, '', '', '-', 'COD', ''),
(6, 2, 4, '2', 1, '999', 'ID8551113052', 'delivered', '25-05-2025', NULL, '25-05-2025', NULL, '', '', '', 'COD', ''),
(7, 2, 2, '1', 1, '90', 'ID8669194532', 'delivered', '25-05-2025', NULL, '28-05-2025', NULL, '', '', 'M', 'COD', ''),
(8, 2, 4, '2', 1, '999', 'ID5151164044', 'delivered', '28-05-2025', NULL, '28-05-2025', NULL, '', '', '-', 'COD', ''),
(9, 2, 5, '4', 1, '100', 'ID6062172829', 'delivered', '28-05-2025', NULL, '28-05-2025', NULL, '', '', '', 'COD', ''),
(10, 2, 6, '2', 1, '999', 'ID4299173651', 'ordered', '28-05-2025', NULL, NULL, NULL, '', '', '', 'COD', '');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `u_id` varchar(200) DEFAULT NULL,
  `review` varchar(2000) DEFAULT NULL,
  `date` varchar(200) DEFAULT NULL,
  `star` varchar(200) DEFAULT NULL,
  `short_rev` varchar(50) DEFAULT NULL,
  `p_id` varchar(200) DEFAULT NULL,
  `abuse` varchar(200) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `u_id`, `review`, `date`, `star`, `short_rev`, `p_id`, `abuse`) VALUES
(1, '2', 'dsnmnvera', '26-05-2025', '4', 'dfkn', '4', '1');

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `address` text,
  `password` varchar(200) DEFAULT NULL,
  `pending` varchar(200) DEFAULT '1',
  `lat` varchar(200) DEFAULT NULL,
  `lon` varchar(200) DEFAULT NULL,
  `ban` varchar(20) DEFAULT '0',
  `percentage` decimal(5,2) DEFAULT NULL,
  `total_earning` decimal(10,2) DEFAULT '0.00',
  `withdrawal_balance` decimal(10,2) DEFAULT '0.00',
  `total_balance` decimal(10,2) DEFAULT '0.00',
  `vendor_name` varchar(255) DEFAULT NULL,
  `nid_front` varchar(255) DEFAULT NULL,
  `nid_back` varchar(255) DEFAULT NULL,
  `shop_logo` varchar(255) DEFAULT 'logo.png',
  `shop_banner` varchar(255) DEFAULT 'shop_banner.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`id`, `name`, `email`, `phone`, `address`, `password`, `pending`, `lat`, `lon`, `ban`, `percentage`, `total_earning`, `withdrawal_balance`, `total_balance`, `vendor_name`, `nid_front`, `nid_back`, `shop_logo`, `shop_banner`) VALUES
(1, NULL, NULL, NULL, NULL, '1', '0', 'asedrty', 'awert', '0', 10.00, 405.00, 220.00, 185.00, NULL, '', '', 'logo.png', 'shop_banner.png'),
(2, 'hhhh', 'mononhasan172@gmail.com', '000', 'nfksdlamd', 'Monon', '0', 'fgsdg', 'gsdg', '0', 10.00, 2697.30, 500.00, 2197.30, 'cmnrvtnn', NULL, NULL, '1748623659slider-1 (1).png', '1748623590494463205_1221531949430269_4615576329336716631_n.jpg'),
(4, 'Weed', 'sadiuldhrubo@gmail.com', '01869791211', 'Shibpur', '12212141', '0', 'Shibpur', '', '0', NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'logo.png', 'shop_banner.png'),
(7, 'Alokito', '123@gmail.com', '1', 'fmnvnskrn', '1', '0', NULL, NULL, '0', 10.00, 0.00, 0.00, 0.00, 'cmnrvtncc', 'Screenshot 2025-03-24 134204.png', 'Screenshot 2025-03-24 134204.png', 'logo.png', 'shop_banner.png'),
(8, 'Monon', 'mononhasan0@gmail.com', '01916914990', 'Monon', 'monon', '1', NULL, NULL, '0', NULL, 0.00, 0.00, 0.00, 'Monon', 'IMG_0967.jpeg', 'IMG_0969.png', 'logo.png', 'shop_banner.png');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `u_id` varchar(200) DEFAULT NULL,
  `p_id` varchar(200) DEFAULT NULL,
  `qty` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `u_id`, `p_id`, `qty`, `size`, `color`) VALUES
(3, '2', '2', NULL, NULL, NULL),
(4, '2', '17', NULL, NULL, NULL),
(5, '1', '3', '1', NULL, NULL),
(6, '1', '17', '1', NULL, NULL),
(7, '1', '16', '1', NULL, NULL),
(8, '1', '2', '1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` int NOT NULL,
  `shop_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','success','cancel') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `withdraw_requests`
--

INSERT INTO `withdraw_requests` (`id`, `shop_id`, `name`, `number`, `email`, `amount`, `status`, `requested_at`) VALUES
(1, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 10.00, 'success', '2025-05-25 03:14:27'),
(2, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 20.00, 'success', '2025-05-25 03:15:07'),
(3, 2, 'hhh', '0508209823', 'mononhasan0@gmail.com', 500.00, 'success', '2025-05-25 03:22:33'),
(4, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 600.00, 'success', '2025-05-25 05:31:57'),
(5, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 600.00, 'cancel', '2025-05-25 05:32:43'),
(6, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 98.00, 'cancel', '2025-05-25 05:39:28'),
(7, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 30.00, 'cancel', '2025-05-25 05:42:45'),
(8, 2, 'hhh', '0508209823', 'mononhasan0@gmail.com', 10.00, 'cancel', '2025-05-25 05:43:01'),
(9, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 30.00, 'cancel', '2025-05-25 05:45:25'),
(10, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 30.00, 'cancel', '2025-05-25 05:46:02'),
(11, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 500.00, 'cancel', '2025-05-25 05:57:50'),
(12, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 500.00, 'cancel', '2025-05-25 05:58:22'),
(13, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 500.00, 'cancel', '2025-05-25 05:58:56'),
(14, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 500.00, 'cancel', '2025-05-25 05:59:07'),
(15, 2, 'hhh', '01604337358', 'mononhasan0@gmail.com', 500.00, 'success', '2025-05-25 06:01:15'),
(16, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 20.00, 'cancel', '2025-05-25 10:07:34'),
(17, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 10.00, 'cancel', '2025-05-25 12:20:50'),
(18, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 1.00, 'cancel', '2025-05-25 12:21:41'),
(19, 1, 'SSt', '0508209823', 'wedounity@gmail.com', 1.00, 'cancel', '2025-05-25 12:27:15'),
(20, 1, 'SSt', '0508209823', 'wedounity@gmail.com', 5.00, 'cancel', '2025-05-25 12:29:07'),
(21, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 20.00, 'cancel', '2025-05-25 12:33:20'),
(22, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 20.00, 'cancel', '2025-05-25 12:33:22'),
(23, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 1.00, 'cancel', '2025-05-25 12:37:47'),
(24, 1, 'SSt', '01301493667', 'wedounity@gmail.com', 1.00, 'cancel', '2025-05-25 12:40:09'),
(25, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 100.00, 'success', '2025-05-29 18:59:09'),
(26, 1, 'SSt', '01604337358', 'wedounity@gmail.com', 20.00, 'cancel', '2025-05-29 19:01:59'),
(27, 2, 'hhhh', '0168958254', 'mononhasan172@gmail.com', 100.00, 'pending', '2025-06-12 11:05:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `big_deals`
--
ALTER TABLE `big_deals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat`
--
ALTER TABLE `cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cust`
--
ALTER TABLE `cust`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `big_deals`
--
ALTER TABLE `big_deals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `cat`
--
ALTER TABLE `cat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cust`
--
ALTER TABLE `cust`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `cat` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
