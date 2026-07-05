-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 05, 2026 at 06:43 AM
-- Server version: 8.0.46-0ubuntu0.24.04.3
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_projectuas`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `cart_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `quantity` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `user_id`, `game_id`, `rating`, `comment`, `created_at`) VALUES
(1, 6, 18, 5, 'keren', '2026-07-05 06:11:55'),
(2, 6, 12, 4, 'gacor kink', '2026-07-05 06:12:04'),
(3, 1, 21, 1, 'game burik', '2026-07-05 06:31:58'),
(4, 1, 11, 5, 'GTA 6 nya kapan wok?', '2026-07-05 06:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `platform` enum('PC','Multiplatform','Playstation','Xbox') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `description`, `price`, `image`, `platform`, `created_at`) VALUES
(10, 'Palworld', 'Game open world survival bareng Pals yang lucu.', 245000.00, 'palworld.jpg', 'PC', '2026-06-21 14:25:18'),
(11, 'Grand Theft Auto V', 'Game open world paling populer dari Rockstar Games.', 150000.00, 'gta.jpg', 'PC', '2026-06-21 14:25:18'),
(12, 'Minecraft', 'Game sandbox untuk membangun apapun yang kamu mau.', 300000.00, 'minecraft.jpg', 'Multiplatform', '2026-06-21 14:25:18'),
(13, 'Valorant', 'Game FPS tactical 5v5 yang seru banget.', 0.00, 'game_1782729790_1978.png', 'PC', '2026-06-21 14:25:18'),
(14, 'Red Dead Redemption 2', 'Game aksi petualangan epik berlatar di dunia koboi Amerika.', 600000.00, 'rdr2.jpg', 'PC', '2026-06-21 14:25:18'),
(15, 'The Last of Us Part I', 'Perjalanan emosional Ellie dan Joel di dunia pasca kiamat.', 850000.00, 'tlou.jpg', 'Playstation', '2026-06-21 14:25:18'),
(16, 'Forza Horizon 5', 'Eksplorasi open world yang indah di Meksiko dengan mobil impian.', 750000.00, 'forza5.jpg', 'Xbox', '2026-06-21 14:25:18'),
(17, 'The Witcher 3: Wild Hunt', 'Ikuti petualangan Geralt of Rivia dalam mencari anak angkatnya.', 250000.00, 'witcher3.jpg', 'PC', '2026-06-21 14:25:18'),
(18, 'Ghost of Tsushima 2', 'Menjadi samurai atau Ghost untuk menyelamatkan pulau Tsushima.', 700000.00, 'got.jpg', 'Playstation', '2026-06-21 14:25:18'),
(20, 'tes', 'testtt', 10000.00, 'game_1782101893_8538.png', 'Playstation', '2026-06-22 02:07:45'),
(21, 'Ratione elit sunt', 'Reiciendis et non re', 871.00, 'game_1782103328_8614.jpg', 'Xbox', '2026-06-22 04:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `game_genres`
--

CREATE TABLE `game_genres` (
  `id` int NOT NULL,
  `game_id` int DEFAULT NULL,
  `genre_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_genres`
--

INSERT INTO `game_genres` (`id`, `game_id`, `genre_id`) VALUES
(17, 20, 3),
(18, 20, 1),
(19, 21, 3),
(20, 21, 1),
(21, 13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `game_keys`
--

CREATE TABLE `game_keys` (
  `id` int NOT NULL,
  `game_id` int DEFAULT NULL,
  `key_code` varchar(255) NOT NULL,
  `status` enum('available','sold') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_keys`
--

INSERT INTO `game_keys` (`id`, `game_id`, `key_code`, `status`) VALUES
(46, 10, 'PALW-ORLD-KEY1-2026', 'available'),
(47, 10, 'PALW-ORLD-KEY2-2026', 'available'),
(48, 11, 'GTAV-ROCK-KEY1-2026', 'sold'),
(49, 11, 'GTAV-ROCK-KEY2-2026', 'available'),
(50, 12, 'MINE-CRAF-KEY1-2026', 'sold'),
(51, 12, 'MINE-CRAF-KEY2-2026', 'available'),
(52, 14, 'RDR2-ROCK-KEY1-2026', 'available'),
(53, 14, 'RDR2-ROCK-KEY2-2026', 'available'),
(54, 15, 'TLOU-SONY-KEY1-2026', 'available'),
(55, 15, 'TLOU-SONY-KEY2-2026', 'available'),
(56, 16, 'FRZ5-XBOX-KEY1-2026', 'available'),
(57, 16, 'FRZ5-XBOX-KEY2-2026', 'available'),
(58, 17, 'WITC-CDR3-KEY1-2026', 'available'),
(59, 17, 'WITC-CDR3-KEY2-2026', 'available'),
(60, 18, 'GOTS-SONY-KEY1-2026', 'sold'),
(61, 18, 'GOTS-SONY-KEY2-2026', 'available'),
(67, 21, 'avds', 'sold');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(3, 'MMORPG'),
(2, 'OPEN WORLD'),
(1, 'RPG'),
(4, 'SANDBOX');

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `discount_percentage`, `max_discount`, `valid_until`) VALUES
(1, 'NYOCOT', 10.00, 10000.00, '2027-05-10 02:00:00'),
(2, 'SEEDEM10', 10.00, 50000.00, '2026-07-29 10:28:40'),
(3, 'SEEDEM20', 20.00, 100000.00, '2026-07-29 10:28:40'),
(4, 'NEWUSER', 15.00, 75000.00, '2026-08-28 10:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `category` enum('general','payment') DEFAULT 'general',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','resolved') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `category`, `subject`, `message`, `attachment`, `status`, `created_at`) VALUES
(1, 1, 'general', 'Suscipit qui suscipi', 'Consequatur eum rep', NULL, 'resolved', '2026-07-05 06:01:55'),
(2, 1, 'payment', 'Sed necessitatibus v', 'Et voluptate vitae c', NULL, 'resolved', '2026-07-05 06:03:21'),
(3, 6, 'general', 'Vitae consequatur q', 'Esse iure facilis v', NULL, 'resolved', '2026-07-05 06:09:45'),
(4, 1, 'payment', 'Doloremque distincti', 'Voluptatem debitis r', NULL, 'pending', '2026-07-05 06:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `sender_role` enum('user','admin','chatbot') COLLATE utf8mb4_general_ci NOT NULL,
  `sender_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_replies`
--

INSERT INTO `ticket_replies` (`id`, `ticket_id`, `sender_role`, `sender_id`, `message`, `created_at`) VALUES
(1, 1, 'chatbot', NULL, 'Halo! Selamat datang di Layanan Bantuan See<b>dem</b>. Ada yang bisa saya bantu hari ini?\n\nAnda bisa bertanya kepada saya seputar:\n- <b>Cara Beli Game</b> (Ketik \'cara beli\')\n- <b>Metode Pembayaran</b> (Ketik \'pembayaran\')\n- <b>Cara Aktivasi (Redeem Code)</b> (Ketik \'cara redeem\')\n- <b>Promo & Diskon</b> (Ketik \'promo\')\n- <b>Daftar Game</b> (Ketik \'daftar game\')\n\nSilakan ketikkan pertanyaan Anda!', '2026-07-05 06:01:55'),
(2, 1, 'user', 1, 'promo', '2026-07-05 06:02:19'),
(3, 1, 'chatbot', NULL, 'Berikut adalah beberapa <b>Promo Code</b> yang aktif saat ini:\n- Kode: <b>NYOCOT</b> (Potongan 10%)\n- Kode: <b>SEEDEM10</b> (Potongan 10%)\n- Kode: <b>SEEDEM20</b> (Potongan 20%)\n- Kode: <b>NEWUSER</b> (Potongan 15%)\n\nDi See<b>dem</b>, promo tidak berlaku per game, melainkan berupa **Promo Code** (diskon total belanja) saat checkout.\n\nCara menggunakan:\n1. Masukkan game pilihan Anda ke Keranjang Belanja.\n2. Masukkan kode promo di atas ke kolom <b>\'Promo Code\'</b> di halaman Keranjang.\n3. Klik tombol <b>Gunakan</b> untuk memotong total harga belanjaan Anda.', '2026-07-05 06:02:19'),
(4, 1, 'user', 1, 'cara beli', '2026-07-05 06:02:41'),
(5, 1, 'chatbot', NULL, 'Untuk membeli redeem code di SEDEEM, silakan ikuti langkah mudah ini:\n\n1. Pilih game yang Anda inginkan di halaman utama.\n2. Klik tombol <b>Beli</b> atau masukkan ke <b>Keranjang Belanja</b>.\n3. Masuk ke halaman Keranjang, masukkan kode promo (jika ada), lalu klik <b>Checkout</b>.\n4. Lakukan pembayaran melalui gerbang pembayaran Midtrans.\n5. Setelah pembayaran berhasil, redeem code Anda akan otomatis muncul di menu <b>Library Key</b> Anda.', '2026-07-05 06:02:41'),
(6, 1, 'user', 1, 'apa', '2026-07-05 06:02:49'),
(7, 1, 'chatbot', NULL, 'Halo! Selamat datang di Layanan Bantuan See<b>dem</b>. Ada yang bisa saya bantu hari ini?\n\nAnda bisa bertanya kepada saya seputar:\n- <b>Cara Beli Game</b> (Ketik \'cara beli\')\n- <b>Metode Pembayaran</b> (Ketik \'pembayaran\')\n- <b>Cara Aktivasi (Redeem Code)</b> (Ketik \'cara redeem\')\n- <b>Promo & Diskon</b> (Ketik \'promo\')\n- <b>Daftar Game</b> (Ketik \'daftar game\')\n\nSilakan ketikkan pertanyaan Anda!', '2026-07-05 06:02:49'),
(8, 2, 'chatbot', NULL, 'Halo! Terima kasih telah mengirimkan laporan kendala transaksi. Bukti transfer/pembayaran Anda sudah masuk ke sistem kami. Mohon tunggu beberapa saat selagi Admin memverifikasi dan merespon laporan Anda.', '2026-07-05 06:03:21'),
(9, 2, 'admin', 1, 'woi', '2026-07-05 06:03:38'),
(10, 2, 'user', 1, 'apa', '2026-07-05 06:04:06'),
(11, 3, 'chatbot', NULL, 'Terima kasih telah menghubungi kami. Saya adalah Chatbot See<b>dem</b>.\n\nPertanyaan Anda belum berhasil saya pahami secara otomatis.\n\nJika ini merupakan pertanyaan umum (seperti cara beli, cara redeem, daftar game), silakan ketik kata kunci singkat seperti \'cara beli\', \'cara redeem\', \'daftar game\', atau \'pembayaran\'.\n\nJika Anda mengalami kendala pembayaran gagal, silakan buat tiket baru dengan kategori \'Kendala Transaksi / Pembayaran\' serta lampirkan screenshot bukti kendala Anda agar bisa langsung ditangani oleh Admin.', '2026-07-05 06:09:45'),
(12, 3, 'user', 6, 'cara redeem', '2026-07-05 06:10:01'),
(13, 3, 'chatbot', NULL, 'Cara menggunakan/mengaktifkan redeem code game Anda:\n\n1. Buka menu <b>Library Key</b> di bar navigasi atas.\n2. Salin (copy) kode redeem game yang sudah Anda beli.\n3. Buka platform game terkait (misalnya Steam, Epic Games Launcher, atau PlayStation Store).\n4. Pilih opsi <b>\'Activate a Product\'</b> atau <b>\'Redeem Code\'</b>, lalu tempelkan (paste) kode tersebut.\n5. Game akan otomatis masuk ke pustaka (library) akun Anda dan siap untuk di-download.', '2026-07-05 06:10:01'),
(14, 4, 'chatbot', NULL, 'Halo! Terima kasih telah mengirimkan laporan kendala transaksi. Bukti transfer/pembayaran Anda sudah masuk ke sistem kami. Mohon tunggu beberapa saat selagi Admin memverifikasi dan merespon laporan Anda.', '2026-07-05 06:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `promo_id` int DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `snap_token` varchar(512) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','settlement','expire','cancel','deny') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `promo_id`, `midtrans_transaction_id`, `snap_token`, `payment_type`, `total_amount`, `payment_status`, `created_at`) VALUES
(48, 6, NULL, 'SEEDEM-1783231831-6', '38254a00-9fa9-451b-8e58-96a29c36d6c0', 'bca', 1000000.00, 'settlement', '2026-07-05 06:10:31'),
(49, 1, 4, 'SEEDEM-1783233004-1', '25ce8803-2b27-416a-a13b-bbabeb23b00b', 'qris', 128240.35, 'settlement', '2026-07-05 06:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int NOT NULL,
  `transaction_id` int DEFAULT NULL,
  `game_key_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `game_key_id`, `price`) VALUES
(28, 48, 50, 300000.00),
(29, 48, 60, 700000.00),
(30, 49, 48, 150000.00),
(31, 49, 67, 871.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'test', 'test@gmail.com', '$2y$10$OmuXW01Js.jBbjtHpbHG6.1RZN78AEiVwM.FrM7QiP0mEZYh89RCi', 'admin', '2026-06-21 14:12:12'),
(2, 'admin', 'admin@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-06-21 14:22:22'),
(3, 'mahasiswa1', 'mhs1@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2026-06-21 14:22:22'),
(4, 'budi', 'budi@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2026-06-21 14:22:22'),
(6, 'udin', 'udin@gmail.com', '$2y$10$bxEsdYtgBQPbzojwUl4vmO1FsTP8qDDFzZWwAxh2wNVljkJbJblSe', 'user', '2026-07-05 06:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_libraries`
--

CREATE TABLE `user_libraries` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `game_key_id` int DEFAULT NULL,
  `acquired_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_genres`
--
ALTER TABLE `game_genres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `game_keys`
--
ALTER TABLE `game_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_code` (`key_code`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `midtrans_transaction_id` (`midtrans_transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `game_key_id` (`game_key_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_libraries`
--
ALTER TABLE `user_libraries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `game_key_id` (`game_key_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `game_genres`
--
ALTER TABLE `game_genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `game_keys`
--
ALTER TABLE `game_keys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_libraries`
--
ALTER TABLE `user_libraries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_genres`
--
ALTER TABLE `game_genres`
  ADD CONSTRAINT `game_genres_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_keys`
--
ALTER TABLE `game_keys`
  ADD CONSTRAINT `game_keys_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD CONSTRAINT `ticket_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_replies_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`promo_id`) REFERENCES `promo_codes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`game_key_id`) REFERENCES `game_keys` (`id`);

--
-- Constraints for table `user_libraries`
--
ALTER TABLE `user_libraries`
  ADD CONSTRAINT `user_libraries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_libraries_ibfk_2` FOREIGN KEY (`game_key_id`) REFERENCES `game_keys` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
