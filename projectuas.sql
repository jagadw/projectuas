-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 06, 2026 at 06:41 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projectuas`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`) VALUES
(6, 1),
(7, 6);

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

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `game_id`, `quantity`) VALUES
(37, 7, 59, 1),
(38, 6, 27, 1);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `game_id`) VALUES
(6, 6, 11);

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
(11, 'Grand Theft Auto V', 'Experience entertainment blockbusters Grand Theft Auto V and Grand Theft Auto Online — now upgraded for a new generation with stunning visuals, faster loading, 3D audio, and more, plus exclusive content for GTA Online players.', 439000.00, 'game_1783242379_2569.avif', 'Multiplatform', '2026-06-21 14:25:18'),
(12, 'Minecraft', 'Game sandbox untuk membangun apapun yang kamu mau.', 300000.00, 'game_1783302150_5185.avif', 'Multiplatform', '2026-06-21 14:25:18'),
(14, 'Red Dead Redemption 2', 'Arthur Morgan dan Van der Linde Gang adalah pelanggar hukum yang kabur. Diikuti agen federal dan pemburu hadiah, mereka harus merampok, mencuri, dan bertarung di sepanjang jalan daratan liar guna bertahan hidup.', 879000.00, 'game_1783243729_5074.jpg', 'Multiplatform', '2026-06-21 14:25:18'),
(18, 'Ghost of Tsushima 2', 'Badai akan datang. Jelajahi Ghost of Tsushima lengkap di PC tempa jalanmu sendiri melalui petualangan aksi dunia terbuka ini dan temukan keajaiban tersembunyinya. Dipersembahkan oleh Sucker Punch Productions, Nixxes Software, dan PlayStation Studios.', 700000.00, 'game_1783301759_2673.webp', 'PC', '2026-06-21 14:25:18'),
(21, 'God of War Ragnarok', 'Kratos dan Atreus memulai perjalanan mitologis untuk mencari jawaban sebelum Ragnarök tiba.', 1029000.00, 'game_1783302100_4876.avif', 'Playstation', '2026-06-22 04:42:08'),
(23, 'Call of Duty: Modern Warfare II', 'Call of Duty®: Modern Warfare® II membawa pemain ke dalam konflik global yang belum pernah terjadi sebelumnya yang menampilkan Operator ikonik dari Task Force 141.', 1040000.00, 'cod mw2.jpg', 'Multiplatform', '2026-07-05 00:05:26'),
(25, 'Split Fiction', 'Game indie unik dengan teka-teki mind-bending.', 150000.00, 'splitf.avif', 'PC', '2026-07-05 01:01:08'),
(26, 'Hot Wheels Unleashed 2: Turbocharged', 'Lomba mobil tercanggih telah kembali dan membawa keseruan ke level selanjutnya! Lebih dari 130 mobil dengan montir baru dan tipe kendaraan lebih banyak! Bangun jalurmu dengan lingkungan baru yang menakjubkan dan mode berbeda untuk bergembira secara daring dan luring!', 699000.00, 'hotw 2.avif', 'Multiplatform', '2026-07-05 01:30:08'),
(27, 'Mecha Chameleon', 'Warnai dirimu agar menyatu dengan lingkungan! \"Meccha Chameleon\" adalah permainan petak umpet sensasional baru di mana kamu mewarnai tubuh putihmu untuk meniru panggung. Menemukan, berpose, dan \"keterampilan artistik\" adalah kunci untuk bertahan hidup. Kelabui para Pencari dengan teknik yang membuat bunglon malu! Mendukung pertandingan publik dan streaming.', 120000.00, 'mechachameleon.webp', 'PC', '2026-07-05 01:55:08'),
(28, 'Tekken 7', 'Temukan kesimpulan epik dari peperangan antar klan yang telah berlangsung lama di antara anggota keluarga Mishima. Didukung oleh Unreal Engine 4, franchise game pertarungan legendaris ini kembali dengan pertempuran sinematik yang menakjubkan dan duel intens yang dapat dinikmati bersama teman dan rival.', 350000.00, 'tekken7.jpg', 'Multiplatform', '2026-07-05 02:07:30'),
(29, 'Battlefield 4', 'Perang modern berskala besar dengan kehancuran dinamis.', 250000.00, 'bf 4.avif', 'PC', '2026-07-05 02:14:08'),
(30, 'Hot Wheels Unleashed', 'Balapan mobil Hot Wheels di trek epik.', 300000.00, 'hotw.webp', 'Multiplatform', '2026-07-05 02:21:18'),
(31, 'Forza Horizon 5', 'Jelajahi lanskap dunia terbuka Meksiko yang semarak dengan aksi berkendara yang seru dan tanpa batas menggunakan mobil-mobil terbaik di dunia.', 750000.00, 'forza6.avif', 'Xbox', '2026-07-05 02:36:55'),
(32, 'Like a Dragon: Pirate Yakuza in Hawaii', 'Petualangan yakuza gila di Hawaii dengan tema bajak laut.', 900000.00, 'pirate.avif', 'Playstation', '2026-07-05 02:52:55'),
(33, 'Baldur\'s Gate 3', 'Baldur’s Gate 3 adalah RPG berbasis kelompok yang kaya akan cerita, berlatar di alam semesta Dungeons & Dragons, di mana pilihan Anda membentuk kisah persahabatan dan pengkhianatan, bertahan hidup dan pengorbanan, serta daya tarik kekuasaan absolut.', 699999.00, 'bdg.avif', 'Multiplatform', '2026-07-05 03:08:29'),
(34, 'It Takes Two', 'Game co-op pemenang penghargaan yang harus dimainkan berdua.', 400000.00, 'it takes 2.webp', 'Multiplatform', '2026-07-05 03:40:19'),
(35, 'EA Sports FC 26', 'Simulasi sepak bola terbaik tahun ini.', 950000.00, 'fc26.avif', 'Multiplatform', '2026-07-05 04:01:55'),
(36, 'Atomic Heart', 'FPS action di realitas alternatif Uni Soviet.', 550000.00, 'AH .avif', 'PC', '2026-07-05 04:34:23'),
(37, 'Palworld', 'Bertarung, bertani, membangun, dan bekerja bersama makhluk misterius yang disebut \"Pals\" dalam game bertahan hidup dan membuat barang multipemain dunia terbuka yang benar-benar baru ini!', 250000.00, 'palworld.avif', 'PC', '2026-07-05 05:03:57'),
(38, 'The Last of Us Part I', 'Kisah emosional Joel dan Ellie dalam grafis remake.', 850000.00, 'tlou1.webp', 'Playstation', '2026-07-05 05:45:37'),
(39, 'The Witcher 3: Wild Hunt', 'RPG open world mahakarya tentang pemburu monster.', 350000.00, 'tw.avif', 'PC', '2026-07-05 06:22:43'),
(40, 'Watch Dogs 2', 'Petualangan hacker di San Francisco.', 400000.00, 'wd2.avif', 'PC', '2026-07-05 06:36:37'),
(41, 'Assassin\'s Creed Valhalla', 'Jadilah Viking legendaris di tanah Inggris.', 650000.00, 'valhalla.avif', 'Multiplatform', '2026-07-05 06:44:33'),
(42, 'Call of Duty: Vanguard', 'Perang Dunia II dari berbagai front pertempuran.', 750000.00, 'vanguard.jpg', 'Multiplatform', '2026-07-05 07:03:56'),
(44, 'No Man\'s Sky', 'Eksplorasi luar angkasa dengan miliaran planet prosedural.', 450000.00, 'no man sky.avif', 'PC', '2026-07-05 07:32:59'),
(45, 'Cyberpunk 2077', 'RPG futuristik di Night City yang penuh obsesi pada modifikasi tubuh.', 600000.00, 'cpunk.avif', 'PC', '2026-07-05 07:39:53'),
(46, 'Batman: Arkham Knight', 'Babak akhir dari seri Arkham dengan Batmobile.', 250000.00, 'arkham.avif', 'PC', '2026-07-05 07:54:45'),
(47, 'Coral Island', 'Coral Island adalah gim pertanian yang dinamis nan santai yang hadir dengan fitur multiplayer! Rasakan kehidupan pulau penuh pesona sesuai keinginan kamu: bertani dengan teman, merawat hewan, membangun hubungan, bergaul dengan beragam karakter, dan menyelami Kerajaan Duyung yang ajaib.', 245999.00, 'coral island.avif', 'Multiplatform', '2026-07-05 08:18:51'),
(48, 'Hogwarts Legacy', 'Hogwarts Legacy is an immersive, open-world action RPG. Now you can take control of the action and be at the center of your own adventure in the wizarding world.', 799000.00, 'hogwhart.avif', 'Multiplatform', '2026-07-05 08:30:09'),
(49, 'Call of Duty: Modern Warfare', 'Experience a visceral Campaign or assemble your team in the ultimate online playground with multiple Special Ops challenges and a mix of Multiplayer maps and modes.', 891000.00, 'cod mw1.webp', 'Multiplatform', '2026-07-05 08:50:02'),
(50, 'Stardew Valley', 'Anda mewarisi lahan pertanian tua kakek Anda di Stardew Valley. Berbekal peralatan warisan dan beberapa koin, Anda memulai kehidupan baru. Dapatkah Anda belajar hidup dari hasil bumi dan mengubah ladang yang ditumbuhi semak belukar ini menjadi rumah yang makmur?', 199999.00, 'stardew valley.avif', 'Multiplatform', '2026-07-05 09:12:16'),
(51, 'Dead Space', 'Game klasik bergenre survival-horror fiksi ilmiah ini kembali hadir, sepenuhnya dibangun ulang untuk menawarkan pengalaman yang lebih mendalam termasuk peningkatan visual, audio, dan gameplay sambil tetap setia pada visi mendebarkan dari game aslinya.', 659000.00, 'deadspace.webp', 'Xbox', '2026-07-05 09:28:46'),
(52, 'Astro Bot', 'Platformer seru yang memaksimalkan fitur controller PlayStation.', 950000.00, 'astrobot.avif', 'Playstation', '2026-07-05 10:06:22'),
(53, 'Battlefield 2042', 'Battlefield™ 2042 is a first-person shooter that marks the return to the iconic all-out warfare of the franchise.', 659000.00, 'bf6.avif', 'Multiplatform', '2026-07-05 10:51:10'),
(54, 'Pragmata', 'Pragmata adalah gim aksi-petualangan fiksi ilmiah yang unik dari Capcom. Ikuti Hugh, anggota tim investigasi yang bernasib buruk, dan Diana, seorang android muda, saat mereka menjelajahi fasilitas bulan yang dikuasai oleh AI jahat untuk mencari jalan kembali ke Bumi.', 779000.00, 'pragmata.avif', 'Multiplatform', '2026-07-05 11:35:51'),
(55, 'Sea of Thieves', 'Sea of ​​Thieves adalah gim petualangan bajak laut yang sangat populer, menawarkan pengalaman bajak laut sejati berupa menjarah harta karun yang hilang, pertempuran sengit, menaklukkan monster laut, dan banyak lagi. Selami keseruan dengan edisi revisi gim ini, yang mencakup akses ke media bonus digital.', 449000.00, 'sot.jpeg', 'Xbox', '2026-07-05 12:10:20'),
(56, 'A Space for the Unbound', 'A magical adventure about two high school sweethearts set at the end of their school days - and the end of the world. Explore a crumbling town and help friends face their inner demons, which could be the key to stopping reality itself disintegrating. And don’t forget to pet the cats.', 99900.00, 'aspace.avif', 'Multiplatform', '2026-07-05 12:45:21'),
(57, 'Watch Dogs: Legion', 'Bangun perlawanan saat Anda berjuang untuk merebut kembali London di masa depan yang sedang menuju kehancurannya. Selamat datang di Perlawanan.', 619000.00, 'wd legion.avif', 'Multiplatform', '2026-07-05 13:00:29'),
(58, 'Limbo', 'Karena tidak yakin dengan nasib saudara perempuannya, seorang anak laki-laki memasuki LIMBO.', 100000.00, 'limbo.jpg', 'Multiplatform', '2026-07-05 13:05:53'),
(59, 'Far Cry 3 Classic Edition', 'Discover the dark secrets of a lawless island ruled by violence and take the fight to the enemy as you try to escape. You’ll need more than luck to escape alive!', 205000.00, 'fc 3.avif', 'Multiplatform', '2026-07-05 13:43:14'),
(60, 'Human: Fall Flat', 'Human Fall Flat adalah game platformer yang lucu dan ringan berlatar lanskap mimpi yang mengambang, yang dapat dimainkan sendirian atau hingga 8 pemain secara online. Level baru gratis terus memberikan penghargaan kepada komunitasnya yang dinamis.', 125999.00, 'hff.avif', 'Xbox', '2026-07-05 14:12:43'),
(61, 'Black Myth: Wukong', 'Black Myth: Wukong is an action RPG rooted in Chinese mythology. You shall set out as the Destined One to venture into the challenges and marvels ahead, to uncover the obscured truth beneath the veil of a glorious legend from the past.', 699999.00, 'wukong.avif', 'Multiplatform', '2026-07-05 14:40:00'),
(62, 'The Last of Us Part II', 'Kelanjutan perjalanan Ellie yang penuh dengan dendam dan darah.', 850000.00, 'tlou2.avif', 'Playstation', '2026-07-05 15:24:45'),
(63, 'Overcooked! 2', 'Overcooked kembali hadir dengan sajian aksi memasak yang kacau dan serba baru! Kembali ke Onion Kingdom dan kumpulkan tim koki Anda untuk bermain bersama secara lokal (couch co-op) atau daring, yang mendukung hingga empat pemain. Siapkan celemek Anda... saatnya menyelamatkan dunia lagi!', 199000.00, 'overcooked2.avif', 'PC', '2026-07-05 15:50:33'),
(64, 'Stray', 'Tersesat, sendirian, dan terpisah dari keluarga, seekor kucing liar harus mengungkap misteri kuno untuk melarikan diri dari kota siber yang telah lama terlupakan dan menemukan jalan pulang.', 239000.00, 'stray.avif', 'Playstation', '2026-07-05 16:25:25'),
(65, 'Battlefield V', 'Inilah pengalaman Battlefield V terbaik. Masuki konflik terbesar umat manusia dengan persenjataan, kendaraan, dan gadget terlengkap, ditambah konten kustomisasi terbaik dari Tahun 1 dan 2.', 569000.00, 'bf5.avif', 'Multiplatform', '2026-07-05 16:35:59'),
(66, 'A Way Out', 'A Way Out adalah petualangan kooperatif eksklusif di mana Anda berperan sebagai salah satu dari dua narapidana yang melakukan pelarian berani dari penjara.', 379000.00, 'awayout.avif', 'Multiplatform', '2026-07-05 16:59:55'),
(67, 'R.E.P.O.', 'Sebuah game horor kooperatif online hingga 6 pemain. Temukan objek berharga yang sepenuhnya berbasis fisika dan tangani dengan hati-hati saat Anda mengambil dan mengekstraknya untuk memenuhi keinginan pencipta Anda.', 84500.00, 'repo.webp', 'PC', '2026-07-05 17:41:41'),
(68, 'Clair Obscur: Expedition 33', 'Pimpin para anggota Ekspedisi 33 dalam perjalanan mereka untuk mengalahkan Sang Pelukis - hingga dia tak bisa melukis kematian lagi. Arungi dunia yang terinspirasi oleh Belle Époque Prancis dalam RPG berbasis giliran dengan mekanisme real-time.', 499000.00, 'game_1783258000_2866.jpg', 'Multiplatform', '2026-07-05 13:26:40'),
(69, 'ARK: Survival Ascended', 'ARK dirancang ulang dari awal dengan teknologi video game generasi berikutnya menggunakan Unreal Engine 5! Bentuk suku, jinakkan & kembangbiakkan ratusan dinosaurus unik dan makhluk purba, jelajahi, buat, bangun, dan berjuanglah menuju puncak rantai makanan. Dunia barumu menanti!', 715000.00, 'game_1783301907_7645.avif', 'Multiplatform', '2026-07-06 01:38:27'),
(70, 'The Elder Scrolls Online', 'Setiap legenda bermula dari suatu tempat, dan di The Elder Scrolls Online, semuanya dimulai dari Anda. Bergabunglah dengan jutaan pemain dalam game RPG fantasi online yang berlatar dunia Elder Scrolls yang luas dan hidup.', 266000.00, 'game_1783302773_6044.avif', 'Multiplatform', '2026-07-06 01:52:53');

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
(28, 25, 11),
(29, 25, 10),
(34, 29, 7),
(35, 29, 6),
(36, 30, 8),
(39, 32, 6),
(40, 32, 1),
(42, 34, 10),
(43, 34, 11),
(44, 35, 13),
(45, 36, 7),
(46, 36, 6),
(49, 38, 6),
(50, 38, 12),
(51, 39, 1),
(52, 39, 2),
(53, 40, 6),
(54, 40, 2),
(55, 41, 6),
(56, 41, 1),
(57, 42, 7),
(60, 44, 2),
(61, 44, 16),
(62, 45, 1),
(63, 45, 2),
(64, 46, 6),
(65, 46, 10),
(75, 52, 9),
(93, 62, 6),
(94, 62, 12),
(104, 27, 6),
(105, 27, 7),
(106, 66, 6),
(107, 66, 10),
(108, 66, 5),
(109, 66, 11),
(110, 67, 12),
(111, 67, 17),
(112, 65, 6),
(113, 65, 7),
(114, 64, 10),
(115, 64, 9),
(116, 68, 10),
(117, 68, 15),
(118, 68, 17),
(119, 68, 9),
(120, 68, 1),
(121, 63, 17),
(122, 63, 5),
(123, 63, 14),
(124, 61, 6),
(125, 61, 15),
(126, 61, 1),
(127, 26, 5),
(128, 26, 8),
(132, 59, 6),
(133, 59, 7),
(134, 59, 2),
(135, 59, 1),
(136, 58, 17),
(137, 58, 9),
(138, 58, 11),
(139, 57, 6),
(140, 57, 2),
(141, 57, 11),
(142, 57, 1),
(143, 56, 10),
(144, 56, 17),
(145, 56, 11),
(149, 54, 6),
(150, 54, 10),
(151, 53, 6),
(152, 53, 7),
(153, 53, 5),
(157, 50, 5),
(158, 50, 1),
(159, 50, 14),
(160, 49, 7),
(161, 49, 5),
(162, 48, 6),
(163, 48, 1),
(164, 47, 5),
(165, 47, 1),
(166, 47, 14),
(167, 11, 6),
(168, 11, 10),
(169, 11, 5),
(170, 11, 2),
(174, 14, 6),
(175, 14, 10),
(176, 14, 2),
(177, 14, 1),
(178, 23, 6),
(179, 23, 7),
(180, 23, 5),
(181, 33, 1),
(185, 37, 5),
(186, 37, 2),
(187, 37, 16),
(188, 18, 6),
(189, 18, 10),
(190, 18, 2),
(195, 69, 6),
(196, 69, 10),
(197, 69, 5),
(198, 69, 16),
(202, 21, 6),
(203, 21, 10),
(204, 21, 1),
(205, 12, 5),
(206, 12, 4),
(207, 12, 16),
(208, 28, 6),
(209, 28, 15),
(210, 28, 5),
(211, 70, 6),
(212, 70, 10),
(213, 70, 3),
(214, 60, 5),
(215, 60, 9),
(216, 60, 11),
(217, 31, 2),
(218, 31, 8),
(219, 31, 13),
(220, 55, 10),
(221, 55, 5),
(222, 55, 2),
(223, 51, 6),
(224, 51, 12),
(225, 51, 1);

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
(48, 11, 'GTAV-ROCK-KEY1-2026', 'sold'),
(49, 11, 'GTAV-ROCK-KEY2-2026', 'available'),
(50, 12, 'MINE-CRAF-KEY1-2026', 'sold'),
(51, 12, 'MINE-CRAF-KEY2-2026', 'available'),
(52, 14, 'RDR2-ROCK-KEY1-2026', 'available'),
(53, 14, 'RDR2-ROCK-KEY2-2026', 'available'),
(60, 18, 'GOTS-SONY-KEY1-2026', 'sold'),
(61, 18, 'GOTS-SONY-KEY2-2026', 'available'),
(67, 21, 'avds', 'sold'),
(70, 56, 'Q9F2K-M7P4X-C5W8R', 'available'),
(71, 61, 'V3T6B-N1L9J-D8R2M', 'available'),
(72, 69, 'H5K8C-W2Q9X-P4M7F', 'available'),
(73, 42, 'Z7R3T-N8J2D-B5L9M', 'available'),
(74, 29, 'C4F9W-Q1P6K-X8M3H', 'available'),
(75, 33, 'K8L4M-X1C7F-Q2P9W', 'available'),
(76, 64, 'W3Q6X-M9P2K-F8C5R', 'available'),
(77, 49, 'T7J1D-R4N9B-L6M2V', 'available'),
(78, 67, 'P5M8F-K3C9X-Q1W4R', 'available'),
(79, 56, 'D2R6M-N7J1T-B9L4V', 'available'),
(80, 66, 'X9C3H-P6K1W-M8F5R', 'available'),
(81, 41, 'L4M9V-B2T8N-J5D1R', 'available'),
(82, 52, 'M1P7Q-W5F3K-C8X2R', 'available'),
(83, 36, 'R9N4B-J2D7T-M6L1V', 'available'),
(84, 49, 'F3C8X-K9P1M-W4Q7R', 'available'),
(85, 53, 'C7X1R-P8K4F-M3W9Q', 'available'),
(86, 57, 'V9T5N-L2J8B-D4R1M', 'available'),
(87, 45, 'Q4W7P-M1K6C-F9X2R', 'available'),
(88, 14, 'T1J6R-N8B3D-L5M9V', 'available'),
(89, 28, 'M6L3V-B9T4N-J1D8R', 'available'),
(90, 46, 'K4P8C-W7F2M-X9Q1R', 'available'),
(91, 68, 'D8R2T-N5J9B-L1M4V', 'available'),
(92, 40, 'X8C2F-P5K1M-W9Q4R', 'available'),
(93, 39, 'L5M1V-B3T8N-J6D2R', 'available'),
(94, 62, 'F7C5X-K9P4M-W1Q8R', 'available'),
(95, 38, 'J2D6N-B8T3R-M4L9V', 'available'),
(96, 70, 'W1Q4P-M6K9C-F7X2R', 'available'),
(97, 50, 'N6B1V-T4J9R-D2M5P', 'available'),
(98, 25, 'P9M3W-K4C8X-F5Q1R', 'available'),
(99, 29, 'R4N8B-J5D1T-M7L2V', 'available'),
(100, 55, 'C3X6R-P9K2F-M8W1Q', 'available'),
(101, 40, 'V8T1N-L7J4B-D9R5M', 'available'),
(102, 32, 'Q2W8P-M3K5C-F6X9R', 'available'),
(103, 26, 'T5J2R-N9B4D-L8M1V', 'available'),
(104, 37, 'M1L7V-B4T9N-J2D5R', 'available'),
(105, 60, 'K6P5C-W2F7M-X4Q8R', 'available'),
(106, 28, 'D9R4T-N6J1B-L3M7V', 'available'),
(107, 11, 'X4C9F-P1K6M-W8Q2R', 'available'),
(108, 54, 'L3M8V-B1T5N-J9D4R', 'available'),
(109, 59, 'F5C2X-K7P1M-W4Q9R', 'available'),
(110, 47, 'J1D7N-B3T6R-M8L2V', 'available'),
(112, 47, 'W9Q5P-M2K8C-F4X1R', 'available'),
(114, 51, 'N4B8V-T1J5R-D7M3P', 'available'),
(116, 21, 'P6M1W-K8C3X-F2Q9R', 'available'),
(117, 68, 'R2N5B-J9D4T-M1L6V', 'available'),
(118, 61, 'C8X4R-P5K9F-M2W7Q', 'available'),
(119, 50, 'V4T9N-L1J6B-D3R8M', 'available'),
(120, 29, 'Q7W2P-M8K1C-F5X4R', 'available'),
(121, 66, 'T8J5R-N2B7D-L4M9V', 'available'),
(123, 65, 'A3N8V-C6J1M-Y4K9P', 'available'),
(124, 66, 'R5D7X-B2H8Q-L9F3T', 'available'),
(125, 56, 'K4W1Z-P8M6N-X2R7C', 'available'),
(126, 46, 'T9L3A-Q5V8J-H1Y6M', 'available'),
(127, 61, 'Y6H1T-M3Q8D-K7V2N', 'available'),
(128, 33, 'J8R5M-L2C9X-P4T7A', 'available'),
(129, 45, 'N1K6W-F8Y3Q-R5H9C', 'available');

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
(6, 'ACTION'),
(10, 'ADVENTURE'),
(15, 'FIGHTING'),
(7, 'FPS'),
(12, 'HORROR'),
(17, 'INDIE'),
(3, 'MMORPG'),
(5, 'MULTIPLAYER'),
(2, 'OPEN WORLD'),
(9, 'PLATFORMER'),
(11, 'PUZZLE'),
(8, 'RACING'),
(1, 'RPG'),
(4, 'SANDBOX'),
(14, 'SIMULATION'),
(13, 'SPORTS'),
(16, 'SURVIVAL');

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
  `sender_role` enum('user','admin','chatbot') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sender_id` int DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `game_genres`
--
ALTER TABLE `game_genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT for table `game_keys`
--
ALTER TABLE `game_keys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
