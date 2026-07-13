-- Eagle Reports Generator
-- Production Ready Database Schema

CREATE DATABASE IF NOT EXISTS `eagle_reports_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `eagle_reports_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `company_name` VARCHAR(150) NULL,
  `company_phone` VARCHAR(50) NULL,
  `company_email` VARCHAR(150) NULL,
  `company_website` VARCHAR(150) NULL,
  `company_footer` TEXT NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`email`)
) ENGINE=InnoDB;

-- 2. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `default_company_name` VARCHAR(150) NOT NULL,
  `default_email` VARCHAR(150) NOT NULL,
  `default_phone` VARCHAR(50) NULL,
  `default_website` VARCHAR(150) NULL,
  `default_footer` TEXT NULL,
  `pdf_margin_top` INT DEFAULT 15,
  `pdf_margin_bottom` INT DEFAULT 15,
  `pdf_margin_left` INT DEFAULT 15,
  `pdf_margin_right` INT DEFAULT 15,
  `primary_color` VARCHAR(10) DEFAULT '#CFFE1C',
  `secondary_color` VARCHAR(10) DEFAULT '#141414',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Reports Table
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `business_name` VARCHAR(150) NOT NULL,
  `report_month` VARCHAR(20) NOT NULL,
  `report_year` VARCHAR(10) NOT NULL,
  `generated_date` DATE NOT NULL,
  `business_logo` VARCHAR(255) NULL,
  `cover_image` VARCHAR(255) NULL,
  
  -- Step 2: Performance Summary
  `people_viewed` INT DEFAULT 0,
  `search_direct` INT DEFAULT 0,
  `search_discovery` INT DEFAULT 0,
  `profile_interactions` INT DEFAULT 0,
  `reviews_count` INT DEFAULT 0,
  `rating_average` DECIMAL(3,2) DEFAULT 0.00,
  `views_maps` INT DEFAULT 0,
  `views_search` INT DEFAULT 0,
  
  -- Step 4: Local Ranking Grid
  `heatmap_image` VARCHAR(255) NULL,
  `avg_rank` DECIMAL(3,2) DEFAULT 0.00,
  `top_3_percentage` DECIMAL(5,2) DEFAULT 0.00,
  `points_tracked` INT DEFAULT 0,
  `insight_text` TEXT NULL,
  
  -- Step 6: Geo Fence
  `geofence_map_url` TEXT NULL,
  
  -- Step 7: Next Month Plan
  `next_month_plan` TEXT NULL,
  
  -- Step 8: Thank You Details
  `company_email` VARCHAR(150) NULL,
  `company_phone` VARCHAR(50) NULL,
  `company_website` VARCHAR(150) NULL,
  `footer_notes` TEXT NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX (`business_name`),
  INDEX (`report_month`, `report_year`)
) ENGINE=InnoDB;

-- 4. Keyword Rankings Table (Step 3)
CREATE TABLE IF NOT EXISTS `keyword_rankings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_id` INT NOT NULL,
  `keyword` VARCHAR(255) NOT NULL,
  `prev_rank` INT DEFAULT 0,
  `curr_rank` INT DEFAULT 0,
  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Backlinks Table (Step 5)
CREATE TABLE IF NOT EXISTS `backlinks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_id` INT NOT NULL,
  `category` VARCHAR(50) NOT NULL, -- 'business_listings', 'profile_creations', 'web_2', 'blogs', 'google_stacking', 'stacking_properties', 'guest_posting'
  `url` TEXT NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Active',
  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert Default Admin User (Password is 'password123' hashed with bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `company_name`, `company_phone`, `company_email`, `company_website`, `company_footer`) 
VALUES (1, 'Admin User', 'admin@eagle.com', '$2y$10$oXf/k70NstD6C78f6YFvWeR0sWHeZ6CqI6pGg6p0hYV/6O1W/K9sO', 'Eagle Digital Agency', '+1 (555) 019-2831', 'reports@eagledigital.com', 'www.eagledigital.com', 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Insert Default Settings
INSERT INTO `settings` (`id`, `default_company_name`, `default_email`, `default_phone`, `default_website`, `default_footer`, `pdf_margin_top`, `pdf_margin_bottom`, `pdf_margin_left`, `pdf_margin_right`, `primary_color`, `secondary_color`) 
VALUES (1, 'Eagle Digital Agency', 'reports@eagledigital.com', '+1 (555) 019-2831', 'www.eagledigital.com', 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.', 15, 15, 15, 15, '#CFFE1C', '#141414')
ON DUPLICATE KEY UPDATE `id`=`id`;
