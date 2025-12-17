-- =====================================================
-- INVENTORY SYSTEM DATABASE UNTUK LARAGON PHPMYADMIN
-- Sistem Inventory ATK Fakultas Ilmu Komputer
-- dengan fitur Peminjaman & Ticketing
-- =====================================================

-- 1. BUAT DATABASE
CREATE DATABASE IF NOT EXISTS `inventory_atk_fasilkom`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `inventory_atk_fasilkom`;

-- 2. TABEL CATEGORIES (Kategori Produk ATK)
CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`),
  KEY `idx_categories_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. TABEL PRODUCTS (Produk ATK)
CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `cost_price` decimal(15,2) DEFAULT 0.00,
  `min_stock` int(11) DEFAULT 0,
  `current_stock` int(11) DEFAULT 0,
  `unit` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_sku` (`sku`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_stock` (`current_stock`),
  KEY `idx_products_min_stock` (`min_stock`),
  CONSTRAINT `fk_products_category` 
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. TABEL STOCK_MOVEMENTS (Pergerakan Stok)
CREATE TABLE `stock_movements` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) UNSIGNED NOT NULL,
  `type` enum('IN','OUT','ADJUSTMENT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `reference_no` varchar(40) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stock_movements_product` (`product_id`),
  KEY `idx_stock_movements_type` (`type`),
  KEY `idx_stock_movements_created_at` (`created_at`),
  CONSTRAINT `fk_stock_movements_product` 
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. TABEL TICKETS (Ticketing System)
CREATE TABLE `tickets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) NOT NULL,
  `type` enum('loan-request','restock','issue','other') NOT NULL DEFAULT 'loan-request',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `requester_name` varchar(150) NOT NULL,
  `requester_contact` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `related_loan_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tickets_type` (`type`),
  KEY `idx_tickets_status` (`status`),
  KEY `idx_tickets_priority` (`priority`),
  KEY `idx_tickets_status_priority` (`status`, `priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. TABEL LOANS (Peminjaman ATK)
CREATE TABLE `loans` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `borrower_name` varchar(150) NOT NULL,
  `borrower_identifier` varchar(100) DEFAULT NULL,
  `borrower_unit` varchar(150) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `loan_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('requested','approved','borrowed','returned','overdue','cancelled') NOT NULL DEFAULT 'requested',
  `notes` text DEFAULT NULL,
  `ticket_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_loans_status` (`status`),
  KEY `idx_loans_loan_date` (`loan_date`),
  KEY `idx_loans_due_date` (`due_date`),
  KEY `idx_loans_status_due` (`status`, `due_date`),
  CONSTRAINT `fk_loans_ticket` 
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) 
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. TABEL LOAN_ITEMS (Item Peminjaman)
CREATE TABLE `loan_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_loan_items_loan` (`loan_id`),
  KEY `idx_loan_items_product` (`product_id`),
  CONSTRAINT `fk_loan_items_loan` 
    FOREIGN KEY (`loan_id`) REFERENCES `loans`(`id`) 
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_loan_items_product` 
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. TABEL TICKET_COMMENTS (Komentar Ticket)
CREATE TABLE `ticket_comments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) UNSIGNED NOT NULL,
  `author_name` varchar(150) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_comments_ticket` (`ticket_id`),
  CONSTRAINT `fk_ticket_comments_ticket` 
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) 
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. UPDATE FOREIGN KEY untuk relasi dua arah (opsional)
ALTER TABLE `tickets` 
ADD CONSTRAINT `fk_tickets_related_loan` 
FOREIGN KEY (`related_loan_id`) REFERENCES `loans`(`id`) 
ON UPDATE CASCADE ON DELETE SET NULL;

-- =====================================================
-- DATA SAMPLE UNTUK TESTING
-- =====================================================

-- Sample Categories
INSERT INTO `categories` (`name`, `description`, `is_active`, `created_at`) VALUES
('Alat Tulis', 'Pensil, pulpen, spidol, dll', 1, NOW()),
('Kertas & Dokumen', 'Kertas A4, amplop, map, dll', 1, NOW()),
('Perlengkapan Kantor', 'Stapler, gunting, lem, dll', 1, NOW()),
('Elektronik Kecil', 'Kalkulator, flashdisk, dll', 1, NOW()),
('Konsumable IT', 'Tinta printer, CD/DVD, dll', 1, NOW());

-- Sample Products
INSERT INTO `products` (`name`, `sku`, `category_id`, `description`, `price`, `cost_price`, `min_stock`, `current_stock`, `unit`, `is_active`, `created_at`) VALUES
('Pulpen Pilot G2', 'ATL001', 1, 'Pulpen gel hitam 0.7mm', 3500.00, 2500.00, 20, 50, 'pcs', 1, NOW()),
('Pensil 2B Faber Castell', 'ATL002', 1, 'Pensil kayu 2B', 2000.00, 1500.00, 30, 45, 'pcs', 1, NOW()),
('Kertas A4 80gsm', 'KRT001', 2, 'Kertas fotocopy putih', 45000.00, 40000.00, 5, 25, 'rim', 1, NOW()),
('Amplop Putih A4', 'KRT002', 2, 'Amplop surat besar', 500.00, 350.00, 50, 100, 'pcs', 1, NOW()),
('Stapler Kenko', 'PKT001', 3, 'Stapler kecil heavy duty', 25000.00, 20000.00, 3, 8, 'pcs', 1, NOW()),
('Isi Stapler No.10', 'PKT002', 3, 'Isi stapler 1000 pcs/box', 5000.00, 3500.00, 10, 20, 'box', 1, NOW()),
('Flashdisk 16GB Kingston', 'ELK001', 4, 'USB 3.0 flashdisk', 85000.00, 70000.00, 2, 5, 'pcs', 1, NOW()),
('Tinta Canon IP2770 Black', 'CIT001', 5, 'Cartridge tinta hitam', 35000.00, 28000.00, 5, 12, 'pcs', 1, NOW());

-- Sample Stock Movements (Stok awal)
INSERT INTO `stock_movements` (`product_id`, `type`, `quantity`, `previous_stock`, `current_stock`, `reference_no`, `notes`, `created_by`, `created_at`) VALUES
(1, 'IN', 50, 0, 50, 'IN20251119001', 'Stok awal pulpen', 'Admin', NOW()),
(2, 'IN', 45, 0, 45, 'IN20251119002', 'Stok awal pensil', 'Admin', NOW()),
(3, 'IN', 25, 0, 25, 'IN20251119003', 'Stok awal kertas A4', 'Admin', NOW()),
(4, 'IN', 100, 0, 100, 'IN20251119004', 'Stok awal amplop', 'Admin', NOW()),
(5, 'IN', 8, 0, 8, 'IN20251119005', 'Stok awal stapler', 'Admin', NOW()),
(6, 'IN', 20, 0, 20, 'IN20251119006', 'Stok awal isi stapler', 'Admin', NOW()),
(7, 'IN', 5, 0, 5, 'IN20251119007', 'Stok awal flashdisk', 'Admin', NOW()),
(8, 'IN', 12, 0, 12, 'IN20251119008', 'Stok awal tinta printer', 'Admin', NOW());

-- Sample Tickets
INSERT INTO `tickets` (`subject`, `type`, `priority`, `status`, `requester_name`, `requester_contact`, `description`, `created_at`) VALUES
('Request Peminjaman ATK untuk Event', 'loan-request', 'medium', 'open', 'Budi Santoso', '08123456789', 'Membutuhkan ATK untuk acara seminar mahasiswa', NOW()),
('Kertas A4 Habis', 'restock', 'high', 'open', 'Sari Admin', 'sari@fasilkom.ac.id', 'Stok kertas A4 sudah menipis, perlu restock segera', NOW()),
('Stapler Rusak', 'issue', 'low', 'open', 'Joko Staff TU', '08198765432', 'Stapler di ruang TU tidak bisa digunakan', NOW());

-- Sample Loans
INSERT INTO `loans` (`borrower_name`, `borrower_identifier`, `borrower_unit`, `contact`, `loan_date`, `due_date`, `status`, `notes`, `ticket_id`, `created_at`) VALUES
('Andi Mahasiswa', '220110001', 'Teknik Informatika', '08111222333', '2025-11-19', '2025-11-26', 'requested', 'Untuk keperluan tugas akhir', 1, NOW()),
('Citra Dosen', 'NIK.001', 'Sistem Informasi', 'citra@fasilkom.ac.id', '2025-11-18', '2025-11-25', 'approved', 'Untuk keperluan penelitian', NULL, NOW());

-- Sample Loan Items
INSERT INTO `loan_items` (`loan_id`, `product_id`, `quantity`, `notes`, `created_at`) VALUES
(1, 1, 5, 'Pulpen untuk menulis', NOW()),
(1, 3, 1, 'Kertas untuk print', NOW()),
(2, 1, 3, NULL, NOW()),
(2, 2, 2, NULL, NOW()),
(2, 4, 10, 'Amplop surat', NOW());

-- Sample Ticket Comments  
INSERT INTO `ticket_comments` (`ticket_id`, `author_name`, `comment`, `created_at`) VALUES
(1, 'Admin ATK', 'Permintaan sedang diproses, mohon menunggu persetujuan', NOW()),
(2, 'Manager Fasilkom', 'Sudah disetujui untuk pembelian kertas A4 sebanyak 50 rim', NOW()),
(3, 'Teknisi', 'Stapler sudah dicek, perlu diganti spring-nya', NOW());

-- =====================================================
-- QUERIES UNTUK TESTING & VALIDASI
-- =====================================================

-- Cek jumlah data per tabel
SELECT 'categories' as tabel, COUNT(*) as jumlah FROM categories
UNION ALL
SELECT 'products', COUNT(*) FROM products  
UNION ALL
SELECT 'stock_movements', COUNT(*) FROM stock_movements
UNION ALL
SELECT 'tickets', COUNT(*) FROM tickets
UNION ALL
SELECT 'loans', COUNT(*) FROM loans
UNION ALL
SELECT 'loan_items', COUNT(*) FROM loan_items
UNION ALL  
SELECT 'ticket_comments', COUNT(*) FROM ticket_comments;

-- Cek produk dengan stok rendah
SELECT p.name, p.sku, p.current_stock, p.min_stock, c.name as category
FROM products p
JOIN categories c ON c.id = p.category_id
WHERE p.current_stock <= p.min_stock
ORDER BY p.current_stock ASC;

-- Cek peminjaman aktif
SELECT l.id, l.borrower_name, l.borrower_unit, l.loan_date, l.due_date, l.status,
       COUNT(li.id) as total_items
FROM loans l
LEFT JOIN loan_items li ON li.loan_id = l.id
WHERE l.status IN ('requested', 'approved', 'borrowed')
GROUP BY l.id
ORDER BY l.created_at DESC;

-- Cek ticket yang masih terbuka
SELECT t.id, t.subject, t.type, t.priority, t.status, t.requester_name,
       COUNT(tc.id) as total_comments
FROM tickets t  
LEFT JOIN ticket_comments tc ON tc.ticket_id = t.id
WHERE t.status IN ('open', 'in_progress')
GROUP BY t.id
ORDER BY t.priority DESC, t.created_at ASC;

-- =====================================================
-- SELESAI - DATABASE SIAP DIGUNAKAN
-- =====================================================