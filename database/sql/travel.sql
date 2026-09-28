-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 07:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: travel_test
--

-- --------------------------------------------------------

--
-- Table structure for table addons
--

CREATE TABLE addons (
  id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  description text DEFAULT NULL,
  price decimal(15,2) NOT NULL,
  image_url varchar(255) DEFAULT NULL,
  is_active tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table banners
--

CREATE TABLE banners (
  id bigint(20) UNSIGNED NOT NULL,
  title varchar(255) NOT NULL,
  image_url varchar(255) NOT NULL,
  target_url varchar(255) DEFAULT NULL,
  position varchar(50) DEFAULT 'home_main',
  sort_order int(11) DEFAULT 0,
  is_active tinyint(1) DEFAULT 1,
  start_date datetime DEFAULT NULL,
  end_date datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table bookings
--

CREATE TABLE bookings (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  tour_schedule_id bigint(20) UNSIGNED NOT NULL,
  coupon_id bigint(20) UNSIGNED DEFAULT NULL,
  total_price decimal(15,2) NOT NULL,
  discount_amount decimal(15,2) DEFAULT 0.00,
  adults_count int(11) NOT NULL DEFAULT 1,
  children_count int(11) NOT NULL DEFAULT 0,
  booking_status enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table booking_addons
--

CREATE TABLE booking_addons (
  id bigint(20) UNSIGNED NOT NULL,
  booking_id bigint(20) UNSIGNED NOT NULL,
  addon_id bigint(20) UNSIGNED DEFAULT NULL,
  addon_name varchar(255) NOT NULL,
  price decimal(15,2) NOT NULL,
  quantity int(11) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table booking_passengers
--

CREATE TABLE booking_passengers (
  id bigint(20) UNSIGNED NOT NULL,
  booking_id bigint(20) UNSIGNED NOT NULL,
  full_name varchar(255) NOT NULL,
  date_of_birth date DEFAULT NULL,
  identity_number varchar(50) DEFAULT NULL,
  gender enum('male','female','other') DEFAULT NULL,
  passenger_type enum('adult','child') DEFAULT 'adult',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table categories
--

CREATE TABLE categories (
  id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table coupons
--

CREATE TABLE coupons (
  id bigint(20) UNSIGNED NOT NULL,
  code varchar(50) NOT NULL,
  discount_type enum('percent','fixed') NOT NULL,
  discount_value decimal(15,2) NOT NULL,
  min_order_value decimal(15,2) DEFAULT 0.00,
  max_discount decimal(15,2) DEFAULT NULL,
  valid_from datetime NOT NULL,
  valid_until datetime NOT NULL,
  usage_limit int(11) DEFAULT NULL,
  used_count int(11) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table destinations
--

CREATE TABLE destinations (
  id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  description text DEFAULT NULL,
  image_url varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table invoices
--

CREATE TABLE invoices (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  booking_id bigint(20) UNSIGNED DEFAULT NULL,
  ticket_booking_id bigint(20) UNSIGNED DEFAULT NULL,
  invoice_type enum('individual','company') DEFAULT 'company',
  buyer_name varchar(255) DEFAULT NULL,
  company_name varchar(255) DEFAULT NULL,
  tax_code varchar(50) DEFAULT NULL,
  billing_address varchar(255) NOT NULL,
  billing_email varchar(255) NOT NULL,
  total_amount decimal(15,2) NOT NULL,
  tax_amount decimal(15,2) NOT NULL,
  invoice_number varchar(100) DEFAULT NULL,
  status enum('pending','issued','cancelled') DEFAULT 'pending',
  issued_at datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table payments
--

CREATE TABLE payments (
  id bigint(20) UNSIGNED NOT NULL,
  booking_id bigint(20) UNSIGNED DEFAULT NULL,
  ticket_booking_id bigint(20) UNSIGNED DEFAULT NULL,
  amount decimal(15,2) NOT NULL,
  payment_method varchar(50) NOT NULL,
  transaction_code varchar(100) DEFAULT NULL,
  payment_status enum('pending','success','failed') DEFAULT 'pending',
  paid_at datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table refunds
--

CREATE TABLE refunds (
  id bigint(20) UNSIGNED NOT NULL,
  booking_id bigint(20) UNSIGNED NOT NULL,
  payment_id bigint(20) UNSIGNED NOT NULL,
  refund_amount decimal(15,2) NOT NULL,
  reason text DEFAULT NULL,
  status enum('pending','approved','processed','rejected') DEFAULT 'pending',
  processed_at datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table reviews
--

CREATE TABLE reviews (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  rating tinyint(4) NOT NULL CHECK (rating >= 1 and rating <= 5),
  comment text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table schedule_guides
--

CREATE TABLE schedule_guides (
  tour_schedule_id bigint(20) UNSIGNED NOT NULL,
  guide_id bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tickets
--

CREATE TABLE tickets (
  id bigint(20) UNSIGNED NOT NULL,
  destination_id bigint(20) UNSIGNED NOT NULL,
  title varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  description text DEFAULT NULL,
  provider_name varchar(255) DEFAULT NULL,
  cancellation_policy text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table ticket_bookings
--

CREATE TABLE ticket_bookings (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  ticket_option_id bigint(20) UNSIGNED NOT NULL,
  quantity int(11) NOT NULL DEFAULT 1,
  total_price decimal(15,2) NOT NULL,
  discount_amount decimal(15,2) DEFAULT 0.00,
  coupon_id bigint(20) UNSIGNED DEFAULT NULL,
  visit_date date NOT NULL,
  booking_status enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  qr_code_url varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table ticket_images
--

CREATE TABLE ticket_images (
  id bigint(20) UNSIGNED NOT NULL,
  ticket_id bigint(20) UNSIGNED NOT NULL,
  image_url varchar(255) NOT NULL,
  is_primary tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table ticket_options
--

CREATE TABLE ticket_options (
  id bigint(20) UNSIGNED NOT NULL,
  ticket_id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  description text DEFAULT NULL,
  price decimal(15,2) NOT NULL,
  original_price decimal(15,2) DEFAULT NULL,
  conditions longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(conditions)),
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tours
--

CREATE TABLE tours (
  id bigint(20) UNSIGNED NOT NULL,
  destination_id bigint(20) UNSIGNED NOT NULL,
  title varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  description text DEFAULT NULL,
  duration_days int(11) NOT NULL,
  duration_nights int(11) NOT NULL,
  base_price decimal(15,2) NOT NULL,
  ai_tags longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(ai_tags)),
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_categories
--

CREATE TABLE tour_categories (
  tour_id bigint(20) UNSIGNED NOT NULL,
  category_id bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_guides
--

CREATE TABLE tour_guides (
  id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  phone varchar(20) NOT NULL,
  email varchar(255) DEFAULT NULL,
  bio text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_images
--

CREATE TABLE tour_images (
  id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  image_url varchar(255) NOT NULL,
  is_primary tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_itineraries
--

CREATE TABLE tour_itineraries (
  id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  day_number int(11) NOT NULL,
  title varchar(255) NOT NULL,
  description text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_schedules
--

CREATE TABLE tour_schedules (
  id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  departure_date datetime NOT NULL,
  return_date datetime NOT NULL,
  capacity int(11) NOT NULL,
  available_seats int(11) NOT NULL,
  status enum('available','full','cancelled') DEFAULT 'available',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table tour_videos
--

CREATE TABLE tour_videos (
  id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  video_url varchar(255) NOT NULL,
  thumbnail_url varchar(255) DEFAULT NULL,
  platform enum('youtube','vimeo','tiktok','local') DEFAULT 'youtube',
  sort_order int(11) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE users (
  id bigint(20) UNSIGNED NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  email_verified_at timestamp NULL DEFAULT NULL,
  password varchar(255) NOT NULL,
  phone varchar(20) DEFAULT NULL,
  role enum('admin','customer') DEFAULT 'customer',
  preferences longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(preferences)),
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table user_addresses
--

CREATE TABLE user_addresses (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  is_default tinyint(1) DEFAULT 0,
  address_type varchar(50) DEFAULT 'home',
  receiver_name varchar(255) NOT NULL,
  phone varchar(20) NOT NULL,
  province_id varchar(20) NOT NULL,
  ward_id varchar(20) NOT NULL,
  detailed_address varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table user_identities
--

CREATE TABLE user_identities (
  id bigint(20) UNSIGNED NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  identity_number varchar(20) NOT NULL,
  full_name varchar(255) NOT NULL,
  date_of_birth date NOT NULL,
  gender enum('male','female','other') DEFAULT NULL,
  nationality varchar(100) DEFAULT 'Vietnam',
  place_of_origin varchar(255) DEFAULT NULL,
  place_of_residence varchar(255) DEFAULT NULL,
  issue_date date NOT NULL,
  expiry_date date NOT NULL,
  issue_place varchar(255) NOT NULL,
  front_image_url varchar(255) DEFAULT NULL,
  back_image_url varchar(255) DEFAULT NULL,
  verification_status enum('pending','verified','rejected') DEFAULT 'pending',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table wishlists
--

CREATE TABLE wishlists (
  user_id bigint(20) UNSIGNED NOT NULL,
  tour_id bigint(20) UNSIGNED NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table addons
--
ALTER TABLE addons
  ADD PRIMARY KEY (id);

--
-- Indexes for table banners
--
ALTER TABLE banners
  ADD PRIMARY KEY (id),
  ADD KEY idx_banners_active_position (is_active,position);

--
-- Indexes for table bookings
--
ALTER TABLE bookings
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id),
  ADD KEY tour_schedule_id (tour_schedule_id),
  ADD KEY idx_bookings_status (booking_status),
  ADD KEY fk_bookings_coupon (coupon_id);

--
-- Indexes for table booking_addons
--
ALTER TABLE booking_addons
  ADD PRIMARY KEY (id),
  ADD KEY booking_id (booking_id),
  ADD KEY fk_booking_addons_addon (addon_id);

--
-- Indexes for table booking_passengers
--
ALTER TABLE booking_passengers
  ADD PRIMARY KEY (id),
  ADD KEY booking_id (booking_id);

--
-- Indexes for table categories
--
ALTER TABLE categories
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY slug (slug);

--
-- Indexes for table coupons
--
ALTER TABLE coupons
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY code (code);

--
-- Indexes for table destinations
--
ALTER TABLE destinations
  ADD PRIMARY KEY (id);

--
-- Indexes for table invoices
--
ALTER TABLE invoices
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY invoice_number (invoice_number),
  ADD KEY user_id (user_id),
  ADD KEY booking_id (booking_id),
  ADD KEY ticket_booking_id (ticket_booking_id),
  ADD KEY idx_invoices_tax_code (tax_code),
  ADD KEY idx_invoices_number (invoice_number),
  ADD KEY idx_invoices_status (status);

--
-- Indexes for table payments
--
ALTER TABLE payments
  ADD PRIMARY KEY (id),
  ADD KEY idx_payments_transaction_code (transaction_code),
  ADD KEY idx_payments_status (payment_status),
  ADD KEY fk_payments_ticket_booking (ticket_booking_id),
  ADD KEY fk_payments_booking (booking_id);

--
-- Indexes for table refunds
--
ALTER TABLE refunds
  ADD PRIMARY KEY (id),
  ADD KEY booking_id (booking_id),
  ADD KEY payment_id (payment_id);

--
-- Indexes for table reviews
--
ALTER TABLE reviews
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id),
  ADD KEY tour_id (tour_id);

--
-- Indexes for table schedule_guides
--
ALTER TABLE schedule_guides
  ADD PRIMARY KEY (tour_schedule_id,guide_id),
  ADD KEY guide_id (guide_id);

--
-- Indexes for table tickets
--
ALTER TABLE tickets
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY slug (slug),
  ADD KEY destination_id (destination_id);

--
-- Indexes for table ticket_bookings
--
ALTER TABLE ticket_bookings
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id),
  ADD KEY ticket_option_id (ticket_option_id),
  ADD KEY coupon_id (coupon_id);

--
-- Indexes for table ticket_images
--
ALTER TABLE ticket_images
  ADD PRIMARY KEY (id),
  ADD KEY ticket_id (ticket_id);

--
-- Indexes for table ticket_options
--
ALTER TABLE ticket_options
  ADD PRIMARY KEY (id),
  ADD KEY ticket_id (ticket_id);

--
-- Indexes for table tours
--
ALTER TABLE tours
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY slug (slug),
  ADD KEY destination_id (destination_id),
  ADD KEY idx_tours_base_price (base_price),
  ADD KEY idx_tours_duration (duration_days);

--
-- Indexes for table tour_categories
--
ALTER TABLE tour_categories
  ADD PRIMARY KEY (tour_id,category_id),
  ADD KEY category_id (category_id);

--
-- Indexes for table tour_guides
--
ALTER TABLE tour_guides
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

--
-- Indexes for table tour_images
--
ALTER TABLE tour_images
  ADD PRIMARY KEY (id),
  ADD KEY tour_id (tour_id);

--
-- Indexes for table tour_itineraries
--
ALTER TABLE tour_itineraries
  ADD PRIMARY KEY (id),
  ADD KEY tour_id (tour_id);

--
-- Indexes for table tour_schedules
--
ALTER TABLE tour_schedules
  ADD PRIMARY KEY (id),
  ADD KEY tour_id (tour_id),
  ADD KEY idx_schedules_departure (departure_date),
  ADD KEY idx_schedules_status (status);

--
-- Indexes for table tour_videos
--
ALTER TABLE tour_videos
  ADD PRIMARY KEY (id),
  ADD KEY tour_id (tour_id);

--
-- Indexes for table users
--
ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email),
  ADD KEY idx_users_phone (phone),
  ADD KEY idx_users_role (role);

--
-- Indexes for table user_addresses
--
ALTER TABLE user_addresses
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id);

--
-- Indexes for table user_identities
--
ALTER TABLE user_identities
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY user_id (user_id),
  ADD UNIQUE KEY identity_number (identity_number);

--
-- Indexes for table wishlists
--
ALTER TABLE wishlists
  ADD PRIMARY KEY (user_id,tour_id),
  ADD KEY tour_id (tour_id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table addons
--
ALTER TABLE addons
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table banners
--
ALTER TABLE banners
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table bookings
--
ALTER TABLE bookings
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table booking_addons
--
ALTER TABLE booking_addons
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table booking_passengers
--
ALTER TABLE booking_passengers
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table categories
--
ALTER TABLE categories
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table coupons
--
ALTER TABLE coupons
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table destinations
--
ALTER TABLE destinations
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table invoices
--
ALTER TABLE invoices
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table payments
--
ALTER TABLE payments
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table refunds
--
ALTER TABLE refunds
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table reviews
--
ALTER TABLE reviews
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tickets
--
ALTER TABLE tickets
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table ticket_bookings
--
ALTER TABLE ticket_bookings
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table ticket_images
--
ALTER TABLE ticket_images
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table ticket_options
--
ALTER TABLE ticket_options
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tours
--
ALTER TABLE tours
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tour_guides
--
ALTER TABLE tour_guides
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tour_images
--
ALTER TABLE tour_images
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tour_itineraries
--
ALTER TABLE tour_itineraries
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tour_schedules
--
ALTER TABLE tour_schedules
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table tour_videos
--
ALTER TABLE tour_videos
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table users
--
ALTER TABLE users
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table user_addresses
--
ALTER TABLE user_addresses
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table user_identities
--
ALTER TABLE user_identities
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table bookings
--
ALTER TABLE bookings
  ADD CONSTRAINT bookings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT bookings_ibfk_2 FOREIGN KEY (tour_schedule_id) REFERENCES tour_schedules (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_bookings_coupon FOREIGN KEY (coupon_id) REFERENCES coupons (id) ON DELETE SET NULL;

--
-- Constraints for table booking_addons
--
ALTER TABLE booking_addons
  ADD CONSTRAINT booking_addons_ibfk_1 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_booking_addons_addon FOREIGN KEY (addon_id) REFERENCES addons (id) ON DELETE SET NULL;

--
-- Constraints for table booking_passengers
--
ALTER TABLE booking_passengers
  ADD CONSTRAINT booking_passengers_ibfk_1 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE;

--
-- Constraints for table invoices
--
ALTER TABLE invoices
  ADD CONSTRAINT invoices_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT invoices_ibfk_2 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE SET NULL,
  ADD CONSTRAINT invoices_ibfk_3 FOREIGN KEY (ticket_booking_id) REFERENCES ticket_bookings (id) ON DELETE SET NULL;

--
-- Constraints for table payments
--
ALTER TABLE payments
  ADD CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_payments_ticket_booking FOREIGN KEY (ticket_booking_id) REFERENCES ticket_bookings (id) ON DELETE CASCADE;

--
-- Constraints for table refunds
--
ALTER TABLE refunds
  ADD CONSTRAINT refunds_ibfk_1 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE,
  ADD CONSTRAINT refunds_ibfk_2 FOREIGN KEY (payment_id) REFERENCES payments (id) ON DELETE CASCADE;

--
-- Constraints for table reviews
--
ALTER TABLE reviews
  ADD CONSTRAINT reviews_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT reviews_ibfk_2 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;

--
-- Constraints for table schedule_guides
--
ALTER TABLE schedule_guides
  ADD CONSTRAINT schedule_guides_ibfk_1 FOREIGN KEY (tour_schedule_id) REFERENCES tour_schedules (id) ON DELETE CASCADE,
  ADD CONSTRAINT schedule_guides_ibfk_2 FOREIGN KEY (guide_id) REFERENCES tour_guides (id) ON DELETE CASCADE;

--
-- Constraints for table tickets
--
ALTER TABLE tickets
  ADD CONSTRAINT tickets_ibfk_1 FOREIGN KEY (destination_id) REFERENCES destinations (id) ON DELETE CASCADE;

--
-- Constraints for table ticket_bookings
--
ALTER TABLE ticket_bookings
  ADD CONSTRAINT ticket_bookings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT ticket_bookings_ibfk_2 FOREIGN KEY (ticket_option_id) REFERENCES ticket_options (id) ON DELETE CASCADE,
  ADD CONSTRAINT ticket_bookings_ibfk_3 FOREIGN KEY (coupon_id) REFERENCES coupons (id) ON DELETE SET NULL;

--
-- Constraints for table ticket_images
--
ALTER TABLE ticket_images
  ADD CONSTRAINT ticket_images_ibfk_1 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE;

--
-- Constraints for table ticket_options
--
ALTER TABLE ticket_options
  ADD CONSTRAINT ticket_options_ibfk_1 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE;

--
-- Constraints for table tours
--
ALTER TABLE tours
  ADD CONSTRAINT tours_ibfk_1 FOREIGN KEY (destination_id) REFERENCES destinations (id) ON DELETE CASCADE;

--
-- Constraints for table tour_categories
--
ALTER TABLE tour_categories
  ADD CONSTRAINT tour_categories_ibfk_1 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE,
  ADD CONSTRAINT tour_categories_ibfk_2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE;

--
-- Constraints for table tour_images
--
ALTER TABLE tour_images
  ADD CONSTRAINT tour_images_ibfk_1 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;

--
-- Constraints for table tour_itineraries
--
ALTER TABLE tour_itineraries
  ADD CONSTRAINT tour_itineraries_ibfk_1 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;

--
-- Constraints for table tour_schedules
--
ALTER TABLE tour_schedules
  ADD CONSTRAINT tour_schedules_ibfk_1 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;

--
-- Constraints for table tour_videos
--
ALTER TABLE tour_videos
  ADD CONSTRAINT tour_videos_ibfk_1 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;

--
-- Constraints for table user_addresses
--
ALTER TABLE user_addresses
  ADD CONSTRAINT user_addresses_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Constraints for table user_identities
--
ALTER TABLE user_identities
  ADD CONSTRAINT user_identities_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Constraints for table wishlists
--
ALTER TABLE wishlists
  ADD CONSTRAINT wishlists_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT wishlists_ibfk_2 FOREIGN KEY (tour_id) REFERENCES tours (id) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
