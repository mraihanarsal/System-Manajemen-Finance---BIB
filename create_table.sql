DROP TABLE IF EXISTS `tiktok_transactions`;

CREATE TABLE `tiktok_transactions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_toko` VARCHAR(50) DEFAULT NULL,
  `nama_barang` VARCHAR(255) DEFAULT NULL,
  `kategori` VARCHAR(100) DEFAULT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `periode_start` DATE DEFAULT NULL,
  `periode_end` DATE DEFAULT NULL,
  `tanggal` DATE DEFAULT NULL,
  `order_id` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `settled_at` VARCHAR(50) DEFAULT NULL,
  `currency` VARCHAR(10) DEFAULT 'IDR',
  `settlement` DOUBLE DEFAULT 0,
  `fees` DOUBLE DEFAULT 0,
  `harga_modal` DOUBLE DEFAULT 0,
  `profit` DOUBLE DEFAULT 0,
  `created_input` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
