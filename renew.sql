CREATE TABLE `big_deals` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image1` VARCHAR(255) DEFAULT NULL,
  `link1` VARCHAR(255) DEFAULT NULL,
  `image2` VARCHAR(255) DEFAULT NULL,
  `link2` VARCHAR(255) DEFAULT NULL,
  `image3` VARCHAR(255) DEFAULT NULL,
  `link3` VARCHAR(255) DEFAULT NULL,
  `image4` VARCHAR(255) DEFAULT NULL,
  `link4` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `big_deals` (`id`, `image1`, `link1`, `image2`, `link2`, `image3`, `link3`, `image4`, `link4`) VALUES
(1, 'bigdeal2.png', 'https://www.youtube.com/', 'bigdel3.png', 'https://example.com/deal2', 'bigdeal2.png', 'https://example.com/deal3', 'bigdeal.png\n', 'https://example.com/deal4');
CREATE TABLE `brands` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `person` ENUM('men', 'women', 'both') DEFAULT 'both',
  `tag` ENUM('normal', 'top_brands') DEFAULT 'normal',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
CREATE TABLE `banner` (
  `id` INT NOT NULL,
  `big` VARCHAR(200) NOT NULL,
  `t1` VARCHAR(200) NOT NULL,
  `t2` VARCHAR(200) NOT NULL,
  `link` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `banner` (`id`, `big`, `t1`, `t2`, `link`) VALUES
(9, 'banner.png', 'SUMMER SALE', 'UP TO 50% OFF', 'https://www.facebook.com/'),
(10, 'banner2.png', 'Best Sell', 'UP TO 50% OFF', 'https://www.facebook.com/'),
(11, 'banner.png', 'Go For it', 'UP TO 50% OFF', 'https://www.facebook.com/');