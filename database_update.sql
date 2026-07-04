-- UPDATE DATABASE FOR SEDEEM TICKETING SYSTEM
-- Jalankan file SQL ini pada database `projectuas` Anda di phpMyAdmin atau MySQL Client.

-- 1. Mengubah status ENUM pada tabel tickets dan menambah kolom category serta attachment
ALTER TABLE `tickets` MODIFY COLUMN `status` ENUM('pending', 'processing', 'resolved') DEFAULT 'pending';
ALTER TABLE `tickets` ADD COLUMN `category` ENUM('general', 'payment') DEFAULT 'general' AFTER `user_id`;
ALTER TABLE `tickets` ADD COLUMN `attachment` VARCHAR(255) DEFAULT NULL AFTER `message`;

-- 2. Membuat tabel ticket_replies untuk mencatat obrolan (user, admin, dan chatbot)
CREATE TABLE IF NOT EXISTS `ticket_replies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ticket_id` INT NOT NULL,
  `sender_role` ENUM('user', 'admin', 'chatbot') NOT NULL,
  `sender_id` INT DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
