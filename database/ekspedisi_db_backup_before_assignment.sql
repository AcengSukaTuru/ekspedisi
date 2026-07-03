-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ekspedisi_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `ekspedisi_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ekspedisi_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `ekspedisi_db`;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Cabang Jakarta Pusat','Jakarta','Jl. Jenderal Sudirman No. 15, Jakarta Pusat','0215551001','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,'Cabang Bandung','Bandung','Jl. Asia Afrika No. 88, Bandung','0225552002','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,'Cabang Surabaya','Surabaya','Jl. Pemuda No. 23, Surabaya','0315553003','2026-04-07 21:56:28','2026-04-07 21:56:28');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  UNIQUE KEY `customers_user_id_unique` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,3,'Customer Ekspedisi','customer@ekspedisi.test','081234567890','Jl. Melati No. 10, Jakarta Selatan','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,NULL,'Toko Sentosa','tokosentosa@ekspedisi.test','082233445566','Jl. Soekarno Hatta No. 25, Bandung','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,4,'Vito','andrianovito738@gmail.com','087882754013','Bojong Gede','2026-04-08 00:08:53','2026-04-08 00:08:53'),(4,5,'Vito','btxninjapro@gmail.com','087882754013','bojong','2026-05-05 18:51:20','2026-05-05 18:51:20'),(5,6,'doni','doni@gmail.com','086756438767','Puri Nirwana','2026-05-05 19:05:01','2026-05-05 19:05:01');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_23_002320_create_customers_table',1),(5,'2026_03_23_002321_create_branches_table',1),(6,'2026_03_23_002322_create_rates_table',1),(7,'2026_03_23_002323_create_vehicles_table',1),(8,'2026_03_23_002324_create_shipments_table',1),(9,'2026_03_23_002325_create_shipment_items_table',1),(10,'2026_03_23_002326_create_payments_table',1),(11,'2026_03_23_002327_create_shipment_trackings_table',1),(12,'2026_04_08_000001_add_role_to_users_table',1),(13,'2026_04_08_000002_add_user_id_to_customers_table',1),(14,'2026_04_08_000003_add_unique_shipment_id_to_payments_table',1),(15,'2026_04_27_000004_add_payment_verification_fields_to_payments_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_shipment_id_unique` (`shipment_id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  CONSTRAINT `payments_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,90000.00,NULL,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,2,110250.00,'2026-04-06','transfer_bank','paid',NULL,NULL,NULL,NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,3,150000.00,NULL,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,4,74800.00,'2026-04-04','cash','paid',NULL,NULL,NULL,NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(5,5,110000.00,'2026-05-06','cash','pending',NULL,NULL,NULL,NULL,'2026-05-05 19:08:16','2026-05-05 19:16:17');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rates`
--

DROP TABLE IF EXISTS `rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `origin_branch_id` bigint(20) unsigned NOT NULL,
  `destination_branch_id` bigint(20) unsigned NOT NULL,
  `price_per_kg` decimal(12,2) NOT NULL,
  `service_type` varchar(255) NOT NULL DEFAULT 'regular',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rates_origin_destination_service_unique` (`origin_branch_id`,`destination_branch_id`,`service_type`),
  KEY `rates_destination_branch_id_foreign` (`destination_branch_id`),
  CONSTRAINT `rates_destination_branch_id_foreign` FOREIGN KEY (`destination_branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `rates_origin_branch_id_foreign` FOREIGN KEY (`origin_branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rates`
--

LOCK TABLES `rates` WRITE;
/*!40000 ALTER TABLE `rates` DISABLE KEYS */;
INSERT INTO `rates` VALUES (1,1,2,12000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,1,2,18000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,2,1,12000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,2,1,18000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28'),(5,1,3,15000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(6,1,3,22000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28'),(7,3,1,15000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(8,3,1,22000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28'),(9,2,3,14000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(10,2,3,21000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28'),(11,3,2,14000.00,'regular','2026-04-07 21:56:28','2026-04-07 21:56:28'),(12,3,2,21000.00,'express','2026-04-07 21:56:28','2026-04-07 21:56:28');
/*!40000 ALTER TABLE `rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('VrtfkCy8MgQuLjdnA8BoR0p90B90JE98EY5CkLTm',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZzJyR3ZRRkN3OUFwT2tFRlpMNnVzdFBDM09aeEI0YnZYZHF1MmluYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zaGlwbWVudHMiO3M6NToicm91dGUiO3M6MjE6ImFkbWluLnNoaXBtZW50cy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1778034755);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipment_items`
--

DROP TABLE IF EXISTS `shipment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipment_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `weight` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipment_items_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `shipment_items_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipment_items`
--

LOCK TABLES `shipment_items` WRITE;
/*!40000 ALTER TABLE `shipment_items` DISABLE KEYS */;
INSERT INTO `shipment_items` VALUES (1,1,'Pakaian dan Dokumen',2,7.50,'Paket customer baru, masih menunggu proses admin.',NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,2,'Aksesoris Elektronik',1,5.25,'Paket express untuk kebutuhan toko.',NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,3,'Peralatan Display Toko',3,10.00,'Menunggu penugasan kendaraan dari admin.',NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,4,'Dokumen dan Sampel Produk',1,3.40,'Shipment express yang sudah selesai dikirim.',NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(5,5,'Pakaian',10,5.00,'Baju','shipment-items/CT29h33VpYoWL52p9hflYyxqdCtNmdDBgWbFpTcB.png','2026-05-05 19:08:16','2026-05-05 19:08:16');
/*!40000 ALTER TABLE `shipment_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipment_trackings`
--

DROP TABLE IF EXISTS `shipment_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipment_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `tracked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipment_trackings_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `shipment_trackings_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipment_trackings`
--

LOCK TABLES `shipment_trackings` WRITE;
/*!40000 ALTER TABLE `shipment_trackings` DISABLE KEYS */;
INSERT INTO `shipment_trackings` VALUES (1,1,'pending','Jakarta','Shipment berhasil dibuat dan menunggu proses admin.','2026-04-06 23:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,2,'pending','Bandung','Shipment berhasil dibuat dan menunggu proses admin.','2026-04-05 22:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,2,'processed','Gudang Bandung','Shipment diverifikasi admin dan siap dijemput kurir.','2026-04-06 00:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,2,'picked_up','Cabang Bandung','Kurir mengambil shipment dari cabang asal.','2026-04-06 22:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(5,2,'in_transit','Tol Cipali','Shipment sedang menuju Surabaya.','2026-04-07 15:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(6,3,'pending','Jakarta','Shipment berhasil dibuat dan menunggu proses admin.','2026-04-06 23:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(7,3,'processed','Gudang Jakarta','Admin sedang menyiapkan shipment untuk keberangkatan.','2026-04-07 11:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(8,4,'pending','Surabaya','Shipment berhasil dibuat dan menunggu proses admin.','2026-04-03 22:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(9,4,'processed','Gudang Surabaya','Shipment selesai diproses admin.','2026-04-03 23:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(10,4,'picked_up','Cabang Surabaya','Kurir mengambil shipment dari cabang Surabaya.','2026-04-04 22:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(11,4,'in_transit','Gerbang Tol Cikampek','Shipment sedang dalam perjalanan ke Jakarta.','2026-04-05 07:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(12,4,'delivered','Jakarta','Shipment telah diterima oleh penerima.','2026-04-06 00:56:28','2026-04-07 21:56:28','2026-04-07 21:56:28'),(13,5,'pending','Jakarta','Shipment berhasil dibuat dan menunggu proses admin.','2026-05-05 19:08:16','2026-05-05 19:08:16','2026-05-05 19:08:16'),(14,5,'processed','Jakarta','Status shipment diperbarui oleh admin.','2026-05-05 19:19:01','2026-05-05 19:19:01','2026-05-05 19:19:01'),(15,5,'processed','Cabang Kebayoran','Gudang kebayoran lama','2026-05-05 19:25:49','2026-05-05 19:25:49','2026-05-05 19:25:49'),(16,5,'in_transit','Transit Surabaya','Surabaya','2026-05-05 19:29:55','2026-05-05 19:29:55','2026-05-05 19:29:55');
/*!40000 ALTER TABLE `shipment_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `origin_branch_id` bigint(20) unsigned NOT NULL,
  `destination_branch_id` bigint(20) unsigned NOT NULL,
  `vehicle_id` bigint(20) unsigned DEFAULT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_phone` varchar(20) NOT NULL,
  `sender_address` text NOT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `receiver_address` text NOT NULL,
  `total_weight` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(12,2) NOT NULL,
  `service_type` varchar(255) NOT NULL DEFAULT 'regular',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `shipment_date` date DEFAULT NULL,
  `estimated_arrival` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipments_tracking_number_unique` (`tracking_number`),
  KEY `shipments_customer_id_foreign` (`customer_id`),
  KEY `shipments_origin_branch_id_foreign` (`origin_branch_id`),
  KEY `shipments_destination_branch_id_foreign` (`destination_branch_id`),
  KEY `shipments_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `shipments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `shipments_destination_branch_id_foreign` FOREIGN KEY (`destination_branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `shipments_origin_branch_id_foreign` FOREIGN KEY (`origin_branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `shipments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES (1,'EXP-260408-IUT1LR',1,1,2,NULL,'Customer Ekspedisi','081234567890','Jl. Melati No. 10, Jakarta Selatan','Budi Santoso','081377788899','Jl. Dago Atas No. 21, Bandung',7.50,90000.00,'regular','pending','2026-04-07','2026-04-10','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,'EXP-260408-JDMWAK',1,2,3,1,'Customer Ekspedisi','081234567890','Jl. Merdeka No. 45, Bandung','Toko Maju Jaya','081122334455','Jl. Raya Darmo No. 19, Surabaya',5.25,110250.00,'express','in_transit','2026-04-06','2026-04-09','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,'EXP-260408-ZPSRZZ',2,1,3,NULL,'Toko Sentosa','082233445566','Jl. Soekarno Hatta No. 25, Bandung','PT Sinar Timur','081255566677','Jl. Ahmad Yani No. 90, Surabaya',10.00,150000.00,'regular','processed','2026-04-07','2026-04-11','2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,'EXP-260408-IZRG7D',2,3,1,3,'Toko Sentosa','082233445566','Jl. Pemuda No. 23, Surabaya','Nadia Prameswari','081388899900','Jl. Tebet Timur No. 11, Jakarta',3.40,74800.00,'express','delivered','2026-04-04','2026-04-06','2026-04-07 21:56:28','2026-04-07 21:56:28'),(5,'EXP-260506-AVOY24',5,1,3,1,'doni','086756438767','Puri Nirwana','adis','087656785263','CGA',5.00,110000.00,'express','in_transit','2026-05-06','2026-05-09','2026-05-05 19:08:16','2026-05-05 19:29:55');
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Ekspedisi','admin@ekspedisi.test',NULL,'$2y$12$uh67UTpMB9SIbodXm09wBe7X3rGXZidz8iPJ/ThPuLtoIigHucjOO','admin',NULL,'2026-04-07 21:56:27','2026-04-07 21:56:27'),(2,'Kurir Ekspedisi','courier@ekspedisi.test',NULL,'$2y$12$UNYNODlH5FzZPSu8CrXL4eTfi3Ws51YM9HQW0cYEgdxERrPPR3kvi','courier',NULL,'2026-04-07 21:56:27','2026-04-07 21:56:27'),(3,'Customer Ekspedisi','customer@ekspedisi.test',NULL,'$2y$12$JAC3ae4z/mak.X/Q9d8X/uybMm6WF/pxejmDPGx1oj4zQtneLfwpS','customer',NULL,'2026-04-07 21:56:28','2026-04-07 21:56:28'),(4,'Vito','andrianovito738@gmail.com',NULL,'$2y$12$29YHtzzd94tYsy9.I7Kw9.B5ssEnQFlwvokIb4YxZOl00Dwm74dxm','customer',NULL,'2026-04-08 00:08:53','2026-04-08 00:08:53'),(5,'Vito','btxninjapro@gmail.com',NULL,'$2y$12$Wc8kHTr/8zYvm8tTgmmG2uxm5as93muiONpVhKLJtY5QhgLMq379q','customer',NULL,'2026-05-05 18:51:20','2026-05-05 18:51:20'),(6,'doni','doni@gmail.com',NULL,'$2y$12$lVbVJE0qzvTr5OjIYk031.oMjBEp1bgoBfeXLozQmxbeRXcR0cDqy','customer',NULL,'2026-05-05 19:05:01','2026-05-05 19:05:01');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(255) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `driver_phone` varchar(20) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'B 9123 TXL','Box Van','Rudi Hartono','081300000111','on_trip','2026-04-07 21:56:28','2026-04-07 21:56:28'),(2,'D 8456 KUR','Pickup','Andi Saputra','081300000222','available','2026-04-07 21:56:28','2026-04-07 21:56:28'),(3,'L 7788 EXP','Truk CDE','Siti Lestari','081300000333','available','2026-04-07 21:56:28','2026-04-07 21:56:28');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-22 15:21:24
