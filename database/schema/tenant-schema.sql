/* Tenant baseline schema — regenerate with: php artisan tenancy:dump-tenant-schema */
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
SET SESSION sql_mode = "NO_ENGINE_SUBSTITUTION";

DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opens_at` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closes_at` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cutoff_time` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `bulletins`;
CREATE TABLE `bulletins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_option` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulletin_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulletin_description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `c_m_s`;
CREATE TABLE `c_m_s` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint unsigned DEFAULT NULL,
  `page_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `c_m_s_created_by_foreign` (`created_by`),
  KEY `c_m_s_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_customer_id_foreign` (`customer_id`),
  KEY `carts_product_id_foreign` (`product_id`),
  KEY `carts_created_by_foreign` (`created_by`),
  KEY `carts_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  KEY `categories_created_by_foreign` (`created_by`),
  KEY `categories_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `device_info` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_unique_id_unique` (`unique_id`),
  KEY `customers_created_by_foreign` (`created_by`),
  KEY `customers_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `delivery_assignments`;
CREATE TABLE `delivery_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `delivery_boy_id` bigint unsigned NOT NULL,
  `status` enum('assigned','in_transit','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_assignments_order_id_foreign` (`order_id`),
  KEY `delivery_assignments_delivery_boy_id_foreign` (`delivery_boy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `email_settings`;
CREATE TABLE `email_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `host` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `port` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encryption` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `mail_logs`;
CREATE TABLE `mail_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `send_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notification_user`;
CREATE TABLE `notification_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_user_notification_id_foreign` (`notification_id`),
  KEY `notification_user_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order',
  `order_id` bigint unsigned DEFAULT NULL,
  `stock_check_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `options` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `options` json DEFAULT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `applied_discount` decimal(10,2) NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `order_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `delivery_boy_id` bigint unsigned DEFAULT NULL,
  `delivery_assigned_at` timestamp NULL DEFAULT NULL,
  `delivery_updated_at` timestamp NULL DEFAULT NULL,
  `delivery_accepted_at` timestamp NULL DEFAULT NULL,
  `delivery_started_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `delivery_canceled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  UNIQUE KEY `orders_tracking_id_unique` (`tracking_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_created_by_foreign` (`created_by`),
  KEY `orders_updated_by_foreign` (`updated_by`),
  KEY `orders_assigned_to_foreign` (`assigned_to`),
  KEY `orders_delivery_boy_id_foreign` (`delivery_boy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payment_gate_ways`;
CREATE TABLE `payment_gate_ways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  `credentials` json DEFAULT NULL,
  `config` json DEFAULT NULL,
  `webhook_config` json DEFAULT NULL,
  `supported_features` json DEFAULT NULL,
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment_processor',
  `supported_currencies` json DEFAULT NULL,
  `supported_countries` json DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_gate_ways_name_unique` (`name`),
  KEY `payment_gate_ways_provider_is_active_index` (`provider`,`is_active`),
  KEY `payment_gate_ways_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payment_gateway_capabilities`;
CREATE TABLE `payment_gateway_capabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_supported` tinyint(1) NOT NULL DEFAULT '1',
  `limits` json DEFAULT NULL,
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateway_capabilities_gateway_name_capability_index` (`gateway_name`,`capability`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `stock_quantity` int DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `min_stock_level` int NOT NULL DEFAULT '0',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `quotes`;
CREATE TABLE `quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rooms` json NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `assemble_cabinets_check` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_status` enum('yes','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_id` bigint unsigned DEFAULT NULL,
  `user_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_tax` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total_assemble_cost` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sub_total_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total_weight` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pallets_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `liftgate_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unload_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `miscellaneous_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `business_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_start_end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency_symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '$',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'UTC',
  `date_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'Y-m-d',
  `time_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'H:i:s',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `delivery_range_northeast_lat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Northeast corner latitude of delivery area',
  `delivery_range_northeast_lng` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Northeast corner longitude of delivery area',
  `delivery_range_southwest_lat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Southwest corner latitude of delivery area',
  `delivery_range_southwest_lng` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Southwest corner longitude of delivery area',
  `facebook_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_plus_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `available_delivery_start_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_delivery_end_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `shipping_quotes`;
CREATE TABLE `shipping_quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rooms` json NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `assemble_cabinets_check` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_status` enum('yes','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_id` bigint unsigned DEFAULT NULL,
  `user_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_tax` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total_assemble_cost` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sub_total_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total_weight` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pallets_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `liftgate_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unload_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `miscellaneous_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_quotes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `stock_check_reqs`;
CREATE TABLE `stock_check_reqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rooms` json DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','in_progress','available','out_of_stock','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_check_reqs_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `stock_check_requests`;
CREATE TABLE `stock_check_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rooms` json DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `user_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_check_requests_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `subscribtions`;
CREATE TABLE `subscribtions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `last_subscription_date` date DEFAULT NULL,
  `subscription_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `table_reservations`;
CREATE TABLE `table_reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guests` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `table_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tax_values`;
CREATE TABLE `tax_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_values_created_by_foreign` (`created_by`),
  KEY `tax_values_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_verified_by_admin` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `is_in_range` tinyint(1) NOT NULL DEFAULT '0',
  `is_available_for_delivery` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wishlists_customer_id_foreign` (`customer_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  KEY `wishlists_created_by_foreign` (`created_by`),
  KEY `wishlists_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0000_01_01_0000_create_currencies_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2018_04_14_103804_create_permission_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2024_03_20_000005_create_categories_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2024_03_20_000007_create_products_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2024_03_21_000001_create_customers_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2024_03_21_000002_create_carts_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2024_03_21_000003_create_orders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2024_03_21_000003_create_wishlists_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2024_03_21_000004_create_order_items_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2024_03_21_000005_create_order_details_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2025_02_14_141327_create_bulletins_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2025_02_27_022152_create_tax_values_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2025_02_27_022155_create_stock_check_requests_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2025_03_02_062930_create_shipping_quotes_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2025_03_02_063222_create_quotes_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19, '2025_03_10_232716_create_contacts_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20, '2025_04_15_112914_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21, '2025_04_24_160306_create_settings_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22, '2025_04_24_160307_add_delivery_range_columns_to_settings_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23, '2025_04_24_160307_add_description_to_roles_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24, '2025_06_13_113624_add_cols_to_settings', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25, '2025_06_13_122341_create_subscribers_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26, '2025_06_23_122923_create_email_settings_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27, '2025_06_23_183559_create_table_reservations_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28, '2025_07_16_111228_create_notifications_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29, '2025_07_18_075714_create_c_m_s_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30, '2025_07_18_092517_create_stock_check_reqs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31, '2025_09_04_060450_add_col_delivery_location_to_setting', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32, '2025_09_04_094526_add_otp_fields_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33, '2025_09_16_052254_create_subscribtions_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34, '2025_09_29_081339_create_mail_logs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35, '2025_09_30_082146_add_location_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36, '2025_10_01_050700_add_order_type_orders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37, '2025_10_03_101832_create_media_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38, '2025_10_08_111955_create_delivery_assignments_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39, '2025_10_08_112249_add_assigned_to_field_to_orders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40, '2025_10_09_102101_create_notification_user_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41, '2025_10_09_102240_add_is_global_to_notifications_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42, '2025_10_10_064454_update_mail_type_field_to_varchar_in_mail_logs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43, '2025_10_13_095256_add_delivery_boy_id_to_orders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44, '2025_10_13_105033_add_is_available_for_delivery_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45, '2025_10_22_071059_update_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46, '2025_10_24_103417_add_fields_to_orders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47, '2025_10_31_114752_update_table_reservations_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48, '2025_11_03_055944_add_fields_to_stock_check_reqs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49, '2025_11_05_062613_create_payment_gate_ways_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50, '2026_04_27_000001_add_is_verified_by_admin_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51, '2026_04_27_120000_create_branches_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52, '2026_04_27_140000_add_phone_and_hours_to_branches_table', 1);

SET FOREIGN_KEY_CHECKS=1;