

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay_clearances`
--

CREATE TABLE `barangay_clearances` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `birthplace` varchar(100) NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `civil_status` enum('Single','Married','Widowed') NOT NULL,
  `purpose` text NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `valid_id_path` varchar(255) NOT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` time NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(20) DEFAULT 'pending',
  `reference_number` varchar(20) NOT NULL,
  `payment_method` varchar(10) NOT NULL,
  `proof_of_payment_path` varchar(255) DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `picked_up_by` varchar(50) NOT NULL DEFAULT 'owner',
  `authorized_person_name` varchar(255) DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay_ids`
--

CREATE TABLE `barangay_ids` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `civil_status` enum('Single','Married','Widowed') NOT NULL,
  `height` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `emergency_name` varchar(100) NOT NULL,
  `emergency_address` varchar(255) NOT NULL,
  `emergency_contact` varchar(20) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `valid_id_path` varchar(255) NOT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `pickup_time` time NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(50) DEFAULT 'pending',
  `reference_number` varchar(20) NOT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `picked_up_by` varchar(50) NOT NULL DEFAULT 'owner',
  `authorized_person_name` varchar(255) DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cedula_requests`
--

CREATE TABLE `cedula_requests` (
  `id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `present_address` text NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` enum('approved','rejected','pickup scheduled','picked_up','pending') DEFAULT 'pending',
  `payment_method` enum('cash','gcash') NOT NULL,
  `proof_of_payment_path` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `picked_up_by` varchar(255) DEFAULT NULL,
  `authorized_person_name` varchar(255) DEFAULT NULL,
  `gcash_reference` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_indigency`
--

CREATE TABLE `certificate_of_indigency` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `maglalakad` varchar(100) NOT NULL,
  `kaano_ano` varchar(100) NOT NULL,
  `saan_ipapasa` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `valid_id_path` varchar(255) NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` time NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(50) DEFAULT 'pending',
  `reference_number` varchar(20) NOT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `picked_up_by` varchar(50) NOT NULL DEFAULT 'owner',
  `authorized_person_name` varchar(255) DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_residency`
--

CREATE TABLE `certificate_of_residency` (
  
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `valid_id_path` varchar(255) NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` time NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(50) DEFAULT 'pending',
  `reference_number` varchar(20) NOT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `picked_up_by` varchar(50) NOT NULL DEFAULT 'owner',
  `authorized_person_name` varchar(255) DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presently_requests`
--

CREATE TABLE `presently_requests` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `present_address` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `valid_id_path` varchar(255) NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(50) DEFAULT 'pending',
  `reference_number` varchar(20) NOT NULL,
  `pickup_time` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL,
  `picked_up_by` varchar(255) DEFAULT 'owner',
  `authorized_person_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `status` enum('Single','Married','Widowed') NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `father_first_name` varchar(50) NOT NULL,
  `father_last_name` varchar(50) NOT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_first_name` varchar(50) NOT NULL,
  `mother_last_name` varchar(50) NOT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Resident','Staff','Admin') NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `blk_street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `account_status` varchar(20) DEFAULT 'pending',
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(6) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `place_of_birth`, `birthdate`, `gender`, `status`, `contact_number`, `father_first_name`, `father_last_name`, `father_occupation`, `mother_first_name`, `mother_last_name`, `mother_occupation`, `email`, `role`, `username`, `password`, `created_at`, `blk_street`, `barangay`, `city`, `province`, `region`, `account_status`, `verified`, `verification_code`, `reset_token`, `reset_expires`) VALUES

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `barangay_ids`
--
ALTER TABLE `barangay_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `cedula_requests`
--
ALTER TABLE `cedula_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD KEY `user_id_index` (`user_id`),
  ADD KEY `reference_number_index` (`reference_number`);

--
-- Indexes for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD UNIQUE KEY `reference_number_2` (`reference_number`);

--
-- Indexes for table `presently_requests`
--
ALTER TABLE `presently_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `barangay_ids`
--
ALTER TABLE `barangay_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `cedula_requests`
--
ALTER TABLE `cedula_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `presently_requests`
--
ALTER TABLE `presently_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cedula_requests`
--
ALTER TABLE `cedula_requests`
  ADD CONSTRAINT `cedula_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
