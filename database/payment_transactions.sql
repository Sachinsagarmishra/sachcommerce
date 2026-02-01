-- Payment Transactions Table for Razorpay
-- This table logs all payment transactions for complete tracking

CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL COMMENT 'Razorpay Payment ID or Refund ID',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('initiated','pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'card, netbanking, upi, wallet, etc.',
  `notes` text DEFAULT NULL,
  `error_code` varchar(100) DEFAULT NULL,
  `error_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_transactions_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index on orders table for faster webhook lookups
ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_razorpay_order_id` (`razorpay_order_id`);
ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_razorpay_payment_id` (`razorpay_payment_id`);
