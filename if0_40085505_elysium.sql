-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql202.infinityfree.com
-- Generation Time: Aug 12, 2026 at 06:22 PM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40085505_elysium`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password`, `created_at`, `last_login`) VALUES
(2, 'admin129', 'admin@elysium.com', 'Secretpass0721//', '2026-03-26 22:47:52', '2026-08-12 14:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'paystack_secret_key', 'sk_live_fc6a9d6fed91eadb4226db9b61408ab614c2533f', '2026-04-12 14:08:04'),
(2, 'paystack_test_secret_key', 'Secretpass0721//', '2026-04-02 05:02:55'),
(3, 'paystack_live_secret_key', 'sk_live_ecc963ab7c4417ea9a2051cefb9287ef18ba1cce', '2026-08-03 10:19:25'),
(4, 'paystack_use_live', '1', '2026-04-12 14:08:04'),
(5, 'paystack_test_public_key', 'admin129', '2026-04-02 05:02:55'),
(6, 'paystack_live_public_key', 'pk_live_b9c40ecf618fbab864012c1532a336f0ac497aad', '2026-08-03 10:19:25'),
(7, 'paystack_resolve_enabled', '1', '2026-08-03 09:45:14'),
(8, 'flutterwave_resolve_enabled', '0', '2026-08-03 09:45:14'),
(9, 'flutterwave_test_secret_key', 'Secretpass0721//', '2026-04-19 19:19:45'),
(10, 'flutterwave_live_secret_key', 'FLWSECK-45ee32c26c9ca1fc25c8e3c12a439eb9-19da72694fbvt-X', '2026-04-19 19:19:45');

-- --------------------------------------------------------

--
-- Table structure for table `bank_status`
--

CREATE TABLE `bank_status` (
  `id` int(11) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `status` enum('full_logs','weak_logs','pending_request','post_no_debit','fixed_account') DEFAULT 'full_logs',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bank_status`
--

INSERT INTO `bank_status` (`id`, `bank_code`, `bank_name`, `status`, `created_at`, `updated_at`) VALUES
(1, '033', 'UBA', 'full_logs', '2026-03-26 22:16:52', '2026-08-03 10:30:40'),
(2, '011', 'First Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(3, '044', 'Access Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(4, '057', 'Zenith Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(5, '058', 'Guaranty Trust Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(6, '301', 'Jaiz Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(7, '070', 'Fidelity Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(8, '030', 'Heritage Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(9, '082', 'Keystone Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(10, '232', 'Sterling Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(11, '032', 'Union Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(12, '215', 'Unity Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(13, '035', 'Wema Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(14, '50211', 'Kuda Bank', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(15, '50515', 'Moniepoint', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(16, '999992', 'OPay', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(17, '100033', 'PalmPay', 'full_logs', '2026-03-26 22:16:52', '2026-03-26 22:16:52'),
(18, '221', 'Stanbic IBTC Bank', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(19, '076', 'Polaris Bank', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(20, '214', 'FCMB', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(21, '050', 'Ecobank Nigeria', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(22, '068', 'Standard Chartered Bank', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(23, '023', 'Citibank Nigeria', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(24, '101', 'Providus Bank', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(25, '565', 'Carbon', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(26, '566', 'VFD Microfinance Bank', 'full_logs', '2026-04-12 01:12:48', '2026-04-12 01:12:48'),
(27, '035A', 'Unknown Bank', 'full_logs', '2026-08-03 10:26:22', '2026-08-03 10:26:22');

-- --------------------------------------------------------

--
-- Table structure for table `client_keys`
--

CREATE TABLE `client_keys` (
  `id` int(11) NOT NULL,
  `client_key` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `client_keys`
--

INSERT INTO `client_keys` (`id`, `client_key`, `is_active`, `created_at`) VALUES
(4, '5bcc5f337422d78eedec1e8ec67c64bcc8b545c6a777264bca849633c1d93b2c', 0, '2026-04-01 15:39:43'),
(5, '77cf8bb1a20acfd447a75ab021a3b574a6696873087dac63b0c79c021f49e717', 1, '2026-06-30 06:43:37'),
(6, '264de205f43dd3cfefdfc6ce8f8d4406add8f6d6a181a29d34f9ce1b5776b3d4', 1, '2026-08-03 09:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `international_banks`
--

CREATE TABLE `international_banks` (
  `id` int(11) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `bank_name` varchar(180) NOT NULL,
  `swift_prefix` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_banks`
--

INSERT INTO `international_banks` (`id`, `country_code`, `bank_name`, `swift_prefix`, `is_active`, `created_at`) VALUES
(1, 'GB', 'HSBC UK', 'HBUK', 1, '2026-03-27 15:59:37'),
(2, 'GB', 'Barclays', 'BARC', 1, '2026-03-27 15:59:37'),
(3, 'GB', 'Lloyds Bank', 'LOYD', 1, '2026-03-27 15:59:37'),
(4, 'GB', 'NatWest', 'NWBK', 1, '2026-03-27 15:59:37'),
(5, 'GB', 'Santander UK', 'ABBY', 1, '2026-03-27 15:59:37'),
(6, 'GB', 'Standard Chartered UK', 'SCBL', 1, '2026-03-27 15:59:37'),
(7, 'GB', 'TSB Bank', 'TSBS', 1, '2026-03-27 15:59:37'),
(8, 'GB', 'Nationwide Building Society', 'NAIA', 1, '2026-03-27 15:59:37'),
(9, 'GB', 'Halifax', 'HLFX', 1, '2026-03-27 15:59:37'),
(10, 'GB', 'Bank of Scotland', 'BOFS', 1, '2026-03-27 15:59:37'),
(11, 'GB', 'Monzo Bank', 'MONZ', 1, '2026-03-27 15:59:37'),
(12, 'GB', 'Revolut Ltd', 'REVO', 1, '2026-03-27 15:59:37'),
(13, 'GB', 'Starling Bank', 'SRLG', 1, '2026-03-27 15:59:37'),
(14, 'GB', 'Metro Bank', 'MYMB', 1, '2026-03-27 15:59:37'),
(15, 'GB', 'Clydesdale Bank', 'CLYD', 1, '2026-03-27 15:59:37'),
(16, 'GB', 'Virgin Money UK', 'VMUK', 1, '2026-03-27 15:59:37'),
(17, 'GB', 'Co-operative Bank', 'CPBK', 1, '2026-03-27 15:59:37'),
(18, 'GB', 'Close Brothers', 'CLOS', 1, '2026-03-27 15:59:37'),
(19, 'GB', 'Coutts', 'COUT', 1, '2026-03-27 15:59:37'),
(20, 'GB', 'Weatherbys Bank', 'WEAT', 1, '2026-03-27 15:59:37'),
(21, 'US', 'JPMorgan Chase Bank', 'CHAS', 1, '2026-03-27 15:59:37'),
(22, 'US', 'Bank of America', 'BOFA', 1, '2026-03-27 15:59:37'),
(23, 'US', 'Citibank', 'CITI', 1, '2026-03-27 15:59:37'),
(24, 'US', 'Wells Fargo Bank', 'WFBI', 1, '2026-03-27 15:59:37'),
(25, 'US', 'Goldman Sachs Bank', 'GSUS', 1, '2026-03-27 15:59:37'),
(26, 'US', 'Morgan Stanley Bank', 'MSUS', 1, '2026-03-27 15:59:37'),
(27, 'US', 'U.S. Bank', 'USBK', 1, '2026-03-27 15:59:37'),
(28, 'US', 'PNC Bank', 'PNCC', 1, '2026-03-27 15:59:37'),
(29, 'US', 'TD Bank USA', 'NRTH', 1, '2026-03-27 15:59:37'),
(30, 'US', 'Capital One', 'NFBK', 1, '2026-03-27 15:59:37'),
(31, 'US', 'Charles Schwab Bank', 'CSCH', 1, '2026-03-27 15:59:37'),
(32, 'US', 'BBVA USA', 'BBVA', 1, '2026-03-27 15:59:37'),
(33, 'US', 'Truist Bank', 'BRBT', 1, '2026-03-27 15:59:37'),
(34, 'US', 'Fifth Third Bank', 'FTBC', 1, '2026-03-27 15:59:37'),
(35, 'US', 'KeyBank', 'KEYB', 1, '2026-03-27 15:59:37'),
(36, 'US', 'BMO Harris Bank', 'HATR', 1, '2026-03-27 15:59:37'),
(37, 'US', 'Regions Bank', 'UPOB', 1, '2026-03-27 15:59:37'),
(38, 'US', 'Ally Bank', 'ALLY', 1, '2026-03-27 15:59:37'),
(39, 'US', 'Discover Bank', 'DCOV', 1, '2026-03-27 15:59:37'),
(40, 'US', 'First Republic Bank', 'FRBB', 1, '2026-03-27 15:59:37'),
(41, 'CA', 'Royal Bank of Canada', 'ROYCCAT2', 1, '2026-03-27 15:59:37'),
(42, 'CA', 'TD Canada Trust', 'TDOMCATTTOR', 1, '2026-03-27 15:59:37'),
(43, 'CA', 'Scotiabank', 'NOSCCATT', 1, '2026-03-27 15:59:37'),
(44, 'CA', 'Bank of Montreal', 'BOFMCAM2', 1, '2026-03-27 15:59:37'),
(45, 'CA', 'CIBC', 'CIBCCATT', 1, '2026-03-27 15:59:37'),
(46, 'CA', 'National Bank of Canada', 'BNDCCAMM', 1, '2026-03-27 15:59:37'),
(47, 'CA', 'HSBC Bank Canada', 'HKBCCATT', 1, '2026-03-27 15:59:37'),
(48, 'CA', 'Laurentian Bank of Canada', 'LAVACA', 1, '2026-03-27 15:59:37'),
(49, 'CA', 'Canadian Western Bank', 'CWBCCATT', 1, '2026-03-27 15:59:37'),
(50, 'CA', 'Desjardins', 'DJDACAT2', 1, '2026-03-27 15:59:37'),
(51, 'CA', 'Tangerine Bank', 'INGDCAT2', 1, '2026-03-27 15:59:37'),
(52, 'CA', 'Simplii Financial', 'CIBCCATT', 1, '2026-03-27 15:59:37'),
(53, 'CA', 'EQ Bank', 'EQBKCA', 1, '2026-03-27 15:59:37'),
(54, 'CA', 'Alterna Bank', 'ALTRCA', 1, '2026-03-27 15:59:37'),
(55, 'CA', 'ATB Financial', 'ATBFCA', 1, '2026-03-27 15:59:37'),
(56, 'CA', 'Servus Credit Union', 'SERVCA', 1, '2026-03-27 15:59:37'),
(57, 'CA', 'Vancity', 'VANC', 1, '2026-03-27 15:59:37'),
(58, 'CA', 'Coast Capital Savings', 'COASCA', 1, '2026-03-27 15:59:37'),
(59, 'CA', 'Meridian Credit Union', 'MERICA', 1, '2026-03-27 15:59:37'),
(60, 'CA', 'FirstOntario Credit Union', 'FONTC', 1, '2026-03-27 15:59:37'),
(61, 'FI', 'Nordea Bank Finland', 'NDEAFIHH', 1, '2026-03-27 15:59:37'),
(62, 'FI', 'OP Financial Group', 'OKOYFIHH', 1, '2026-03-27 15:59:37'),
(63, 'GR', 'National Bank of Greece', 'ETHNGRAA', 1, '2026-03-27 15:59:37'),
(64, 'GR', 'Piraeus Bank', 'PIRBGRAA', 1, '2026-03-27 15:59:37'),
(65, 'HU', 'OTP Bank', 'OTPVHUHB', 1, '2026-03-27 15:59:37'),
(66, 'HU', 'K&H Bank', 'OKHBHUHB', 1, '2026-03-27 15:59:37'),
(67, 'CZ', 'Ceska sporitelna', 'GIBACZPX', 1, '2026-03-27 16:01:49'),
(68, 'CZ', 'CSOB', 'CEKOCZPP', 1, '2026-03-27 16:01:49'),
(69, 'RO', 'Banca Transilvania', 'BTRLRO22', 1, '2026-03-27 16:01:49'),
(70, 'RO', 'BCR', 'RNCBROBU', 1, '2026-03-27 16:01:49'),
(71, 'BG', 'UniCredit Bulbank', 'UNCRBGSF', 1, '2026-03-27 16:01:49'),
(72, 'BG', 'DSK Bank', 'STSABGSF', 1, '2026-03-27 16:01:49'),
(73, 'HR', 'Privredna banka Zagreb', 'PBZGHR2X', 1, '2026-03-27 16:01:49'),
(74, 'HR', 'Zagrebacka banka', 'ZABAHR2X', 1, '2026-03-27 16:03:51'),
(75, 'SK', 'Slovenska sporitelna', 'GIBASKBX', 1, '2026-03-27 16:03:51'),
(76, 'SK', 'Tatra banka', 'TATRSKBX', 1, '2026-03-27 16:03:51'),
(77, 'SI', 'NLB d.d.', 'LJBASI2X', 1, '2026-03-27 16:03:51'),
(78, 'SI', 'Nova KBM', 'KBMASI2X', 1, '2026-03-27 16:03:51'),
(79, 'LT', 'Swedbank Lithuania', 'HABALT22', 1, '2026-03-27 16:03:51'),
(80, 'LT', 'SEB Lithuania', 'CBVILT2X', 1, '2026-03-27 16:03:51'),
(81, 'CH', 'UBS Switzerland AG', 'UBSWCHZH', 1, '2026-04-12 01:12:48'),
(82, 'CH', 'Credit Suisse', 'CRESCHZZ', 1, '2026-04-12 01:12:48'),
(83, 'CH', 'Zuercher Kantonalbank', 'ZKBKCHZZ', 1, '2026-04-12 01:12:48'),
(84, 'CH', 'Raiffeisen Switzerland', 'RAIFCH22', 1, '2026-04-12 01:12:48'),
(85, 'CH', 'PostFinance', 'POFICHBE', 1, '2026-04-12 01:12:48'),
(86, 'DE', 'Deutsche Bank', 'DEUTDEFF', 1, '2026-04-12 01:12:48'),
(87, 'DE', 'Commerzbank', 'COBADEFF', 1, '2026-04-12 01:12:48'),
(88, 'DE', 'DZ Bank', 'GENODEFF', 1, '2026-04-12 01:12:48'),
(89, 'DE', 'KfW Bankengruppe', 'KFWIDEFF', 1, '2026-04-12 01:12:48'),
(90, 'DE', 'Landesbank Baden-Wuerttemberg', 'SOLADEST', 1, '2026-04-12 01:12:48'),
(91, 'ES', 'Banco Santander', 'BSCHESMM', 1, '2026-04-12 01:12:48'),
(92, 'ES', 'BBVA', 'BBVAESMM', 1, '2026-04-12 01:12:48'),
(93, 'ES', 'CaixaBank', 'CABORNES', 1, '2026-04-12 01:12:48'),
(94, 'ES', 'Banco Sabadell', 'BSABESBB', 1, '2026-04-12 01:12:48'),
(95, 'ES', 'Bankinter', 'BKBKESMM', 1, '2026-04-12 01:12:48'),
(96, 'IT', 'Intesa Sanpaolo', 'BCITITMM', 1, '2026-04-12 01:12:48'),
(97, 'IT', 'UniCredit', 'UNCRITMM', 1, '2026-04-12 01:12:48'),
(98, 'IT', 'Banca Monte dei Paschi', 'PASCITM1', 1, '2026-04-12 01:12:48'),
(99, 'IT', 'Mediobanca', 'MABORNES', 1, '2026-04-12 01:12:48'),
(100, 'IT', 'Banco BPM', 'BAPPIT21', 1, '2026-04-12 01:12:48'),
(101, 'FR', 'BNP Paribas', 'BNPAFRPP', 1, '2026-04-12 01:12:48'),
(102, 'FR', 'Credit Agricole', 'AGRIFRPP', 1, '2026-04-12 01:12:48'),
(103, 'FR', 'Societe Generale', 'SOGEFRPP', 1, '2026-04-12 01:12:48'),
(104, 'FR', 'Groupe BPCE', 'CEPAFRPP', 1, '2026-04-12 01:12:48'),
(105, 'FR', 'Credit Mutuel', 'CMCIFRPP', 1, '2026-04-12 01:12:48'),
(106, 'DK', 'Danske Bank', 'DABADKKK', 1, '2026-04-12 01:12:48'),
(107, 'DK', 'Nordea Denmark', 'NDEADKKK', 1, '2026-04-12 01:12:48'),
(108, 'DK', 'Jyske Bank', 'JYBADKKK', 1, '2026-04-12 01:12:48'),
(109, 'DK', 'Nykredit Bank', 'NABORNES', 1, '2026-04-12 01:12:48'),
(110, 'DK', 'Sydbank', 'SABORNES', 1, '2026-04-12 01:12:48'),
(111, 'AT', 'Erste Group Bank', 'GIBAATWW', 1, '2026-04-12 01:12:48'),
(112, 'AT', 'Raiffeisen Bank International', 'RZBAATWW', 1, '2026-04-12 01:12:48'),
(113, 'AT', 'UniCredit Bank Austria', 'BKAUATWW', 1, '2026-04-12 01:12:48'),
(114, 'AT', 'BAWAG PSK', 'BAWAATWW', 1, '2026-04-12 01:12:48'),
(115, 'AT', 'Oberbank', 'OBKLAT2L', 1, '2026-04-12 01:12:48'),
(116, 'NL', 'ING Bank', 'INGBNL2A', 1, '2026-04-12 01:12:48'),
(117, 'NL', 'ABN AMRO', 'ABNANL2A', 1, '2026-04-12 01:12:48'),
(118, 'NL', 'Rabobank', 'RABONL2U', 1, '2026-04-12 01:12:48'),
(119, 'NL', 'De Volksbank', 'SNSBNL2A', 1, '2026-04-12 01:12:48'),
(120, 'NL', 'Triodos Bank', 'TRIONL2U', 1, '2026-04-12 01:12:48'),
(121, 'BE', 'KBC Bank', 'KREDBEBB', 1, '2026-04-12 01:12:48'),
(122, 'BE', 'BNP Paribas Fortis', 'GEBABEBB', 1, '2026-04-12 01:12:48'),
(123, 'BE', 'Belfius Bank', 'GKCCBEBB', 1, '2026-04-12 01:12:48'),
(124, 'BE', 'ING Belgium', 'BBRUBEBB', 1, '2026-04-12 01:12:48'),
(125, 'BE', 'Argenta', 'ARSPBE22', 1, '2026-04-12 01:12:48'),
(126, 'SE', 'Swedbank', 'SWEDSESS', 1, '2026-04-12 01:12:48'),
(127, 'SE', 'Handelsbanken', 'HANDSESS', 1, '2026-04-12 01:12:48'),
(128, 'SE', 'SEB', 'ESSESESS', 1, '2026-04-12 01:12:48'),
(129, 'SE', 'Nordea Sweden', 'NDEASESS', 1, '2026-04-12 01:12:48'),
(130, 'SE', 'Lansforsakringar Bank', 'ELLFSESS', 1, '2026-04-12 01:12:48'),
(131, 'NO', 'DNB Bank', 'DNBANOKK', 1, '2026-04-12 01:12:48'),
(132, 'NO', 'Nordea Norway', 'NDEANOKK', 1, '2026-04-12 01:12:48'),
(133, 'NO', 'SpareBank 1', 'SPSONO22', 1, '2026-04-12 01:12:48'),
(134, 'NO', 'Handelsbanken Norway', 'HANDNOKK', 1, '2026-04-12 01:12:48'),
(135, 'NO', 'Sbanken', 'SBAKNOBB', 1, '2026-04-12 01:12:48'),
(136, 'PT', 'Caixa Geral de Depositos', 'CGDIPTPL', 1, '2026-04-12 01:12:48'),
(137, 'PT', 'Millennium bcp', 'BCOMPTPL', 1, '2026-04-12 01:12:48'),
(138, 'PT', 'Novo Banco', 'BESCPTPL', 1, '2026-04-12 01:12:48'),
(139, 'PT', 'Santander Totta', 'TOTAPTPL', 1, '2026-04-12 01:12:48'),
(140, 'PT', 'BPI', 'BBPIPTPL', 1, '2026-04-12 01:12:48'),
(141, 'IE', 'Bank of Ireland', 'BOFIIE2D', 1, '2026-04-12 01:12:48'),
(142, 'IE', 'AIB', 'AABORNES', 1, '2026-04-12 01:12:48'),
(143, 'IE', 'Ulster Bank Ireland', 'ABORNES', 1, '2026-04-12 01:12:48'),
(144, 'IE', 'Permanent TSB', 'IPBSIE2D', 1, '2026-04-12 01:12:48'),
(145, 'IE', 'KBC Bank Ireland', 'KABORNES', 1, '2026-04-12 01:12:48'),
(146, 'ZA', 'Standard Bank', 'SBZAZAJJ', 1, '2026-04-12 01:12:48'),
(147, 'ZA', 'First National Bank', 'FIABORNES', 1, '2026-04-12 01:12:48'),
(148, 'ZA', 'Absa Bank', 'ABSAZAJJ', 1, '2026-04-12 01:12:48'),
(149, 'ZA', 'Nedbank', 'NEDSZAJJ', 1, '2026-04-12 01:12:48'),
(150, 'ZA', 'Capitec Bank', 'CABORNES', 1, '2026-04-12 01:12:48'),
(151, 'GH', 'GCB Bank', 'GHCBGHAC', 1, '2026-04-12 01:12:48'),
(152, 'GH', 'Ecobank Ghana', 'EABORNES', 1, '2026-04-12 01:12:48'),
(153, 'GH', 'Stanbic Bank Ghana', 'SBICGHAC', 1, '2026-04-12 01:12:48'),
(154, 'GH', 'Fidelity Bank Ghana', 'FABORNES', 1, '2026-04-12 01:12:48'),
(155, 'GH', 'CalBank', 'ACABORNES', 1, '2026-04-12 01:12:48'),
(156, 'EG', 'National Bank of Egypt', 'NBEGEGCX', 1, '2026-04-12 01:12:48'),
(157, 'EG', 'Banque Misr', 'BMISEGCX', 1, '2026-04-12 01:12:48'),
(158, 'EG', 'Commercial International Bank', 'CIABORNES', 1, '2026-04-12 01:12:48'),
(159, 'EG', 'QNB Alahli', 'QABORNES', 1, '2026-04-12 01:12:48'),
(160, 'EG', 'Arab African International Bank', 'ARABEGCX', 1, '2026-04-12 01:12:48'),
(161, 'KE', 'Kenya Commercial Bank', 'KCABORNES', 1, '2026-04-12 01:12:48'),
(162, 'KE', 'Equity Bank Kenya', 'EABORNES', 1, '2026-04-12 01:12:48'),
(163, 'KE', 'Co-operative Bank of Kenya', 'KCOOKENA', 1, '2026-04-12 01:12:48'),
(164, 'KE', 'Standard Chartered Kenya', 'SCBLKENX', 1, '2026-04-12 01:12:48'),
(165, 'KE', 'Absa Bank Kenya', 'BABORNES', 1, '2026-04-12 01:12:48'),
(166, 'UG', 'Stanbic Bank Uganda', 'SBICUGKA', 1, '2026-04-12 01:12:48'),
(167, 'UG', 'Standard Chartered Uganda', 'SCBLUGKA', 1, '2026-04-12 01:12:48'),
(168, 'UG', 'Centenary Bank', 'CEABORNES', 1, '2026-04-12 01:12:48'),
(169, 'UG', 'DFCU Bank', 'DFCUUGKA', 1, '2026-04-12 01:12:48'),
(170, 'UG', 'Absa Bank Uganda', 'BABORNES', 1, '2026-04-12 01:12:48'),
(171, 'CM', 'Afriland First Bank', 'ABORNES', 1, '2026-04-12 01:12:48'),
(172, 'CM', 'Societe Generale Cameroun', 'SGCMCMCX', 1, '2026-04-12 01:12:48'),
(173, 'CM', 'Ecobank Cameroon', 'EABORNES', 1, '2026-04-12 01:12:48'),
(174, 'CM', 'UBA Cameroon', 'UNABORNES', 1, '2026-04-12 01:12:48'),
(175, 'CM', 'BICEC', 'BICECMCX', 1, '2026-04-12 01:12:48'),
(176, 'TZ', 'CRDB Bank', 'COABORNES', 1, '2026-04-12 01:12:48'),
(177, 'TZ', 'NMB Bank', 'NMIBTZTZ', 1, '2026-04-12 01:12:48'),
(178, 'TZ', 'Stanbic Bank Tanzania', 'SBICTZTX', 1, '2026-04-12 01:12:48'),
(179, 'TZ', 'Exim Bank Tanzania', 'EXABORNES', 1, '2026-04-12 01:12:48'),
(180, 'TZ', 'Equity Bank Tanzania', 'EABORNES', 1, '2026-04-12 01:12:48'),
(181, 'MA', 'Attijariwafa Bank', 'BCMAMAMC', 1, '2026-04-12 01:12:48'),
(182, 'MA', 'Banque Populaire du Maroc', 'BCPOMAMC', 1, '2026-04-12 01:12:48'),
(183, 'MA', 'BMCE Bank', 'BMABORNES', 1, '2026-04-12 01:12:48'),
(184, 'MA', 'Societe Generale Maroc', 'SGMB', 1, '2026-04-12 01:12:48'),
(185, 'MA', 'CIH Bank', 'CIHMMAMC', 1, '2026-04-12 01:12:48'),
(186, 'RW', 'Bank of Kigali', 'BABORNES', 1, '2026-04-12 01:12:48'),
(187, 'RW', 'Equity Bank Rwanda', 'EABORNES', 1, '2026-04-12 01:12:48'),
(188, 'RW', 'I&M Bank Rwanda', 'IMBORNES', 1, '2026-04-12 01:12:48'),
(189, 'RW', 'Access Bank Rwanda', 'AABORNES', 1, '2026-04-12 01:12:48'),
(190, 'RW', 'Ecobank Rwanda', 'EABORNES', 1, '2026-04-12 01:12:48'),
(191, 'SN', 'CBAO Groupe Attijariwafa', 'CBAOSNDA', 1, '2026-04-12 01:12:48'),
(192, 'SN', 'Societe Generale Senegal', 'SGSNSNDA', 1, '2026-04-12 01:12:48'),
(193, 'SN', 'Ecobank Senegal', 'EABORNES', 1, '2026-04-12 01:12:48'),
(194, 'SN', 'Bank of Africa Senegal', 'BOABORNES', 1, '2026-04-12 01:12:48'),
(195, 'SN', 'UBA Senegal', 'UNABORNES', 1, '2026-04-12 01:12:48'),
(196, 'CI', 'Societe Generale Cote dIvoire', 'SGCICICX', 1, '2026-04-12 01:12:48'),
(197, 'CI', 'Ecobank Cote dIvoire', 'EABORNES', 1, '2026-04-12 01:12:48'),
(198, 'CI', 'NSIA Banque', 'NSIACIAB', 1, '2026-04-12 01:12:48'),
(199, 'CI', 'Bank of Africa Cote dIvoire', 'AFRIBJ', 1, '2026-04-12 01:12:48'),
(200, 'CI', 'UBA Cote dIvoire', 'UNABORNES', 1, '2026-04-12 01:12:48'),
(201, 'ET', 'Commercial Bank of Ethiopia', 'CBETETAA', 1, '2026-04-12 01:12:48'),
(202, 'ET', 'Dashen Bank', 'ABORNES', 1, '2026-04-12 01:12:48'),
(203, 'ET', 'Awash Bank', 'AABORNES', 1, '2026-04-12 01:12:48'),
(204, 'ET', 'Bank of Abyssinia', 'BOABORNES', 1, '2026-04-12 01:12:48'),
(205, 'ET', 'United Bank Ethiopia', 'UNITETAA', 1, '2026-04-12 01:12:48'),
(206, 'BW', 'First National Bank Botswana', 'FIABORNES', 1, '2026-04-12 01:12:48'),
(207, 'BW', 'Standard Chartered Botswana', 'SCBLBWGX', 1, '2026-04-12 01:12:48'),
(208, 'BW', 'Absa Bank Botswana', 'BABORNES', 1, '2026-04-12 01:12:48'),
(209, 'BW', 'Stanbic Bank Botswana', 'SBICBWGX', 1, '2026-04-12 01:12:48'),
(210, 'BW', 'Bank of Botswana', 'BBABORNES', 1, '2026-04-12 01:12:48'),
(211, 'ZM', 'Zanaco', 'ABORNES', 1, '2026-04-12 01:12:48'),
(212, 'ZM', 'Stanbic Bank Zambia', 'SBICZMLX', 1, '2026-04-12 01:12:48'),
(213, 'ZM', 'Standard Chartered Zambia', 'SCBLZMLX', 1, '2026-04-12 01:12:48'),
(214, 'ZM', 'Absa Bank Zambia', 'BABORNES', 1, '2026-04-12 01:12:48'),
(215, 'ZM', 'First National Bank Zambia', 'FIABORNES', 1, '2026-04-12 01:12:48'),
(216, 'ZW', 'CBZ Bank', 'COBZZWHA', 1, '2026-04-12 01:12:48'),
(217, 'ZW', 'Standard Chartered Zimbabwe', 'SCBLZWHX', 1, '2026-04-12 01:12:48'),
(218, 'ZW', 'Stanbic Bank Zimbabwe', 'SBICZWHX', 1, '2026-04-12 01:12:48'),
(219, 'ZW', 'FBC Bank', 'FBCZWHHA', 1, '2026-04-12 01:12:48'),
(220, 'ZW', 'NMB Bank Zimbabwe', 'NMBORNES', 1, '2026-04-12 01:12:48'),
(221, 'AE', 'Emirates NBD', 'EBILAEAD', 1, '2026-04-21 13:29:21'),
(222, 'AE', 'First Abu Dhabi Bank', 'NBADAEAA', 1, '2026-04-21 13:29:21'),
(223, 'AE', 'Abu Dhabi Commercial Bank', 'ADCBAEAA', 1, '2026-04-21 13:29:21'),
(224, 'AE', 'Dubai Islamic Bank', 'DUIBAEAD', 1, '2026-04-21 13:29:21'),
(225, 'AE', 'Mashreq Bank', 'BOMLAEAD', 1, '2026-04-21 13:29:21'),
(226, 'AE', 'Commercial Bank of Dubai', 'CBDUAEAD', 1, '2026-04-21 13:29:21'),
(227, 'AE', 'RAKBANK', 'NRAKAEAK', 1, '2026-04-21 13:29:21'),
(228, 'AE', 'Sharjah Islamic Bank', 'NBSHAEAS', 1, '2026-04-21 13:29:21'),
(229, 'AE', 'United Arab Bank', 'UABAEAAX', 1, '2026-04-21 13:29:21'),
(230, 'AE', 'National Bank of Fujairah', 'NBFUAEAF', 1, '2026-04-21 13:29:21'),
(231, 'SA', 'Saudi National Bank', 'NCBKSAJE', 1, '2026-04-21 13:29:21'),
(232, 'SA', 'Al Rajhi Bank', 'RJHISARI', 1, '2026-04-21 13:29:21'),
(233, 'SA', 'Riyad Bank', 'RIBLSARI', 1, '2026-04-21 13:29:21'),
(234, 'SA', 'Saudi Awwal Bank', 'SABBSARI', 1, '2026-04-21 13:29:21'),
(235, 'SA', 'Banque Saudi Fransi', 'BSFRSARI', 1, '2026-04-21 13:29:21'),
(236, 'SA', 'Arab National Bank', 'ARNBSARI', 1, '2026-04-21 13:29:21'),
(237, 'SA', 'Bank AlJazira', 'BJAZSAJE', 1, '2026-04-21 13:29:21'),
(238, 'SA', 'Bank AlBilad', 'ALBISARI', 1, '2026-04-21 13:29:21'),
(239, 'SA', 'The Saudi Investment Bank', 'SIBCSARI', 1, '2026-04-21 13:29:21'),
(240, 'SA', 'Bank Alinma', 'INMASARI', 1, '2026-04-21 13:29:21'),
(241, 'QA', 'Qatar National Bank', 'QNBAQAQA', 1, '2026-04-21 13:29:21'),
(242, 'QA', 'Commercial Bank of Qatar', 'CBQAQAQA', 1, '2026-04-21 13:29:21'),
(243, 'QA', 'Doha Bank', 'DOHBQAQA', 1, '2026-04-21 13:29:21'),
(244, 'QA', 'Qatar Islamic Bank', 'QISBQAQA', 1, '2026-04-21 13:29:21'),
(245, 'QA', 'Masraf Al Rayan', 'MABFQAQA', 1, '2026-04-21 13:29:21'),
(246, 'QA', 'Al Khaliji Bank', 'KLJIQAQA', 1, '2026-04-21 13:29:21'),
(247, 'QA', 'Qatar International Islamic Bank', 'QIIBQAQA', 1, '2026-04-21 13:29:21'),
(248, 'QA', 'Ahli Bank QSC', 'ABQQQAQA', 1, '2026-04-21 13:29:21'),
(249, 'QA', 'Dukhan Bank', 'QIIBQAQA', 1, '2026-04-21 13:29:21'),
(250, 'QA', 'Lesha Bank', 'QINVQAQA', 1, '2026-04-21 13:29:21'),
(251, 'KW', 'National Bank of Kuwait', 'NBOKKWKW', 1, '2026-04-21 13:29:21'),
(252, 'KW', 'Kuwait Finance House', 'KFHOKWKW', 1, '2026-04-21 13:29:21'),
(253, 'KW', 'Burgan Bank', 'BURGKWKW', 1, '2026-04-21 13:29:21'),
(254, 'KW', 'Gulf Bank', 'GULBKWKW', 1, '2026-04-21 13:29:21'),
(255, 'KW', 'Commercial Bank of Kuwait', 'COMBKWKW', 1, '2026-04-21 13:29:21'),
(256, 'KW', 'Boubyan Bank', 'BBYNKWKW', 1, '2026-04-21 13:29:21'),
(257, 'KW', 'Warba Bank', 'WARBKWKW', 1, '2026-04-21 13:29:21'),
(258, 'KW', 'Kuwait International Bank', 'KIBBKWKW', 1, '2026-04-21 13:29:21'),
(259, 'KW', 'Ahli United Bank Kuwait', 'AUBBKWKW', 1, '2026-04-21 13:29:21'),
(260, 'KW', 'Industrial Bank of Kuwait', 'IBOKKWKW', 1, '2026-04-21 13:29:21'),
(261, 'BH', 'National Bank of Bahrain', 'NBOBBHBM', 1, '2026-04-21 13:29:21'),
(262, 'BH', 'Bank of Bahrain and Kuwait', 'BBKUBHBM', 1, '2026-04-21 13:29:21'),
(263, 'BH', 'Ahli United Bank Bahrain', 'AUBBBHBM', 1, '2026-04-21 13:29:21'),
(264, 'BH', 'Bahrain Islamic Bank', 'BISBBHBM', 1, '2026-04-21 13:29:21'),
(265, 'BH', 'Kuwait Finance House Bahrain', 'KFHBBHBM', 1, '2026-04-21 13:29:21'),
(266, 'BH', 'Al Salam Bank Bahrain', 'ALSABHBM', 1, '2026-04-21 13:29:21'),
(267, 'BH', 'Khaleeji Commercial Bank', 'KHCBBHBM', 1, '2026-04-21 13:29:21'),
(268, 'BH', 'Ithmaar Bank', 'FIBHBHBM', 1, '2026-04-21 13:29:21'),
(269, 'BH', 'Arab Banking Corporation', 'ABCOBHBM', 1, '2026-04-21 13:29:21'),
(270, 'BH', 'Bahrain Development Bank', 'BDBBBHBM', 1, '2026-04-21 13:29:21'),
(271, 'JP', 'Mitsubishi UFJ Bank', 'BOTKJPJT', 1, '2026-04-21 13:29:21'),
(272, 'JP', 'Sumitomo Mitsui Banking Corporation', 'SMBCJPJT', 1, '2026-04-21 13:29:21'),
(273, 'JP', 'Mizuho Bank', 'MHCBJPJT', 1, '2026-04-21 13:29:21'),
(274, 'JP', 'Resona Bank', 'DIWAJPJT', 1, '2026-04-21 13:29:21'),
(275, 'JP', 'Japan Post Bank', 'JPPSJPJ1', 1, '2026-04-21 13:29:21'),
(276, 'JP', 'SBI Shinsei Bank', 'LTCBJPJT', 1, '2026-04-21 13:29:21'),
(277, 'JP', 'Aozora Bank', 'NCBTJPJT', 1, '2026-04-21 13:29:21'),
(278, 'JP', 'Fukuoka Bank', 'FKBKJPJT', 1, '2026-04-21 13:29:21'),
(279, 'JP', 'Shizuoka Bank', 'SHIZJPJT', 1, '2026-04-21 13:29:21'),
(280, 'JP', 'Chiba Bank', 'CHBAJPJT', 1, '2026-04-21 13:29:21'),
(281, 'CN', 'Industrial and Commercial Bank of China', 'ICBKCNBJ', 1, '2026-04-21 13:29:21'),
(282, 'CN', 'China Construction Bank', 'PCBCCNBJ', 1, '2026-04-21 13:29:21'),
(283, 'CN', 'Agricultural Bank of China', 'ABOCCNBJ', 1, '2026-04-21 13:29:21'),
(284, 'CN', 'Bank of China', 'BKCHCNBJ', 1, '2026-04-21 13:29:21'),
(285, 'CN', 'Bank of Communications', 'COMMCNSH', 1, '2026-04-21 13:29:21'),
(286, 'CN', 'China Merchants Bank', 'CMBCCNBS', 1, '2026-04-21 13:29:21'),
(287, 'CN', 'China CITIC Bank', 'CIBKCNBJ', 1, '2026-04-21 13:29:21'),
(288, 'CN', 'China Minsheng Bank', 'MSBCCNBJ', 1, '2026-04-21 13:29:21'),
(289, 'CN', 'Ping An Bank', 'SZDBCNBS', 1, '2026-04-21 13:29:21'),
(290, 'CN', 'Shanghai Pudong Development Bank', 'SPDBCNSH', 1, '2026-04-21 13:29:21'),
(291, 'KR', 'KB Kookmin Bank', 'CZNBKRSE', 1, '2026-04-21 13:29:21'),
(292, 'KR', 'Shinhan Bank', 'SHBKKRSE', 1, '2026-04-21 13:29:21'),
(293, 'KR', 'Woori Bank', 'HVBKKRSE', 1, '2026-04-21 13:29:21'),
(294, 'KR', 'Hana Bank', 'KOEXKRSE', 1, '2026-04-21 13:29:21');
INSERT INTO `international_banks` (`id`, `country_code`, `bank_name`, `swift_prefix`, `is_active`, `created_at`) VALUES
(295, 'KR', 'Industrial Bank of Korea', 'IBKOKRSE', 1, '2026-04-21 13:29:21'),
(296, 'KR', 'NongHyup Bank', 'NACFKRSE', 1, '2026-04-21 13:29:21'),
(297, 'KR', 'Korea Development Bank', 'KODBKRSE', 1, '2026-04-21 13:29:21'),
(298, 'KR', 'Suhyup Bank', 'NFFCKRSE', 1, '2026-04-21 13:29:21'),
(299, 'KR', 'Citi Korea', 'CITIKRSX', 1, '2026-04-21 13:29:21'),
(300, 'KR', 'SC First Bank Korea', 'SCBLKRSE', 1, '2026-04-21 13:29:21'),
(301, 'SG', 'DBS Bank', 'DBSSSGSG', 1, '2026-04-21 13:29:21'),
(302, 'SG', 'OCBC Bank', 'OCBCSGSG', 1, '2026-04-21 13:29:21'),
(303, 'SG', 'United Overseas Bank', 'UOVBSGSG', 1, '2026-04-21 13:29:21'),
(304, 'SG', 'Standard Chartered Singapore', 'SCBLSGSG', 1, '2026-04-21 13:29:21'),
(305, 'SG', 'Citibank Singapore', 'CITISGSG', 1, '2026-04-21 13:29:21'),
(306, 'SG', 'HSBC Singapore', 'HSBCSGSG', 1, '2026-04-21 13:29:21'),
(307, 'SG', 'Maybank Singapore', 'MBBESGSG', 1, '2026-04-21 13:29:21'),
(308, 'SG', 'RHB Bank Singapore', 'RHBBSGSG', 1, '2026-04-21 13:29:21'),
(309, 'SG', 'Bank of China Singapore', 'BKCHSGSG', 1, '2026-04-21 13:29:21'),
(310, 'SG', 'State Bank of India Singapore', 'SBINSGSG', 1, '2026-04-21 13:29:21'),
(311, 'HK', 'HSBC Hong Kong', 'HSBCHKHH', 1, '2026-04-21 13:29:21'),
(312, 'HK', 'Bank of China Hong Kong', 'BKCHHKHH', 1, '2026-04-21 13:29:21'),
(313, 'HK', 'Hang Seng Bank', 'HASEHKHH', 1, '2026-04-21 13:29:21'),
(314, 'HK', 'Standard Chartered Hong Kong', 'SCBLHKHH', 1, '2026-04-21 13:29:21'),
(315, 'HK', 'Bank of East Asia', 'BEASHKHH', 1, '2026-04-21 13:29:21'),
(316, 'HK', 'DBS Hong Kong', 'DHBKHKHH', 1, '2026-04-21 13:29:21'),
(317, 'HK', 'Citibank Hong Kong', 'CITIHKHX', 1, '2026-04-21 13:29:21'),
(318, 'HK', 'OCBC Wing Hang Bank', 'WIHBHKHH', 1, '2026-04-21 13:29:21'),
(319, 'HK', 'ICBC Asia', 'UBHKHKHH', 1, '2026-04-21 13:29:21'),
(320, 'HK', 'China CITIC Bank International', 'KWHKHKHH', 1, '2026-04-21 13:29:21'),
(321, 'ZA', 'Investec Bank', 'IVESZAJJ', 1, '2026-04-21 13:29:21'),
(322, 'ZA', 'TymeBank', 'TYMEZAJJ', 1, '2026-04-21 13:29:21'),
(323, 'ZA', 'African Bank', 'AFRCZAJJ', 1, '2026-04-21 13:29:21'),
(324, 'ZA', 'Sasfin Bank', 'SASFZAJJ', 1, '2026-04-21 13:29:21'),
(325, 'ZA', 'Bidvest Bank', 'BIDBZAJJ', 1, '2026-04-21 13:29:21'),
(326, 'NG', 'Zenith Bank', 'ZEIBNGLA', 1, '2026-04-21 13:29:21'),
(327, 'NG', 'Guaranty Trust Bank', 'GTBINGLA', 1, '2026-04-21 13:29:21'),
(328, 'NG', 'First Bank of Nigeria', 'FBNINGLA', 1, '2026-04-21 13:29:21'),
(329, 'NG', 'United Bank for Africa', 'UNAFNGLA', 1, '2026-04-21 13:29:21'),
(330, 'NG', 'Access Bank', 'ABNGNGLA', 1, '2026-04-21 13:29:21'),
(331, 'NG', 'Ecobank Nigeria', 'ECOCNGLA', 1, '2026-04-21 13:29:21'),
(332, 'NG', 'Fidelity Bank Nigeria', 'FIDTNGLA', 1, '2026-04-21 13:29:21'),
(333, 'NG', 'Sterling Bank', 'NAMENGLA', 1, '2026-04-21 13:29:21'),
(334, 'NG', 'Stanbic IBTC Bank', 'SBICNGLX', 1, '2026-04-21 13:29:21'),
(335, 'NG', 'Union Bank of Nigeria', 'UBNINGLA', 1, '2026-04-21 13:29:21'),
(336, 'EG', 'AlexBank', 'ALEXEGCX', 1, '2026-04-21 13:29:21'),
(337, 'EG', 'Bank of Alexandria', 'ALEXEGCX', 1, '2026-04-21 13:29:21'),
(338, 'EG', 'HSBC Egypt', 'HSBCEGCX', 1, '2026-04-21 13:29:21'),
(339, 'EG', 'Arab Bank Egypt', 'ARBKEGCX', 1, '2026-04-21 13:29:21'),
(340, 'EG', 'Faisal Islamic Bank Egypt', 'FAITEGCX', 1, '2026-04-21 13:29:21'),
(341, 'TN', 'Banque de Tunisie', 'BTBKTNTT', 1, '2026-04-21 13:29:21'),
(342, 'TN', 'Banque Internationale Arabe de Tunisie', 'BIATTNTT', 1, '2026-04-21 13:29:21'),
(343, 'TN', 'Banque Nationale Agricole', 'BNATTNTT', 1, '2026-04-21 13:29:21'),
(344, 'TN', 'Amen Bank', 'CFCTTNTT', 1, '2026-04-21 13:29:21'),
(345, 'TN', 'Attijari Bank Tunisie', 'BSTUTNTT', 1, '2026-04-21 13:29:21'),
(346, 'TN', 'Arab Tunisian Bank', 'ATBKTNTT', 1, '2026-04-21 13:29:21'),
(347, 'TN', 'Zitouna Bank', 'BZITTNTT', 1, '2026-04-21 13:29:21'),
(348, 'TN', 'Union Bancaire pour le Commerce et lIndustrie', 'UBCITNTT', 1, '2026-04-21 13:29:21'),
(349, 'TN', 'Banque de lHabitat', 'BHBKTNTT', 1, '2026-04-21 13:29:21'),
(350, 'TN', 'Societe Tunisienne de Banque', 'STBKTNTT', 1, '2026-04-21 13:29:21'),
(351, 'KE', 'NCBA Bank Kenya', 'CBAFKENX', 1, '2026-04-21 13:29:21'),
(352, 'KE', 'Diamond Trust Bank Kenya', 'DTKEKENA', 1, '2026-04-21 13:29:21'),
(353, 'KE', 'I&M Bank Kenya', 'IMBLKENA', 1, '2026-04-21 13:29:21'),
(354, 'KE', 'Family Bank', 'FABLKENA', 1, '2026-04-21 13:29:21'),
(355, 'KE', 'NCBK Bank Kenya', 'NCBAKENX', 1, '2026-04-21 13:29:21'),
(356, 'PL', 'PKO Bank Polski', 'BPKOPLPW', 1, '2026-04-21 13:29:21'),
(357, 'PL', 'Bank Pekao', 'PKOPPLPW', 1, '2026-04-21 13:29:21'),
(358, 'PL', 'Santander Bank Polska', 'WBKPPLPP', 1, '2026-04-21 13:29:21'),
(359, 'PL', 'mBank', 'BREXPLPW', 1, '2026-04-21 13:29:21'),
(360, 'PL', 'ING Bank Slaski', 'INGBPLPW', 1, '2026-04-21 13:29:21'),
(361, 'PL', 'Bank Millennium', 'BIGBPLPW', 1, '2026-04-21 13:29:21'),
(362, 'PL', 'Alior Bank', 'ALBPPLPW', 1, '2026-04-21 13:29:21'),
(363, 'PL', 'Bank Handlowy', 'CITIPLPX', 1, '2026-04-21 13:29:21'),
(364, 'PL', 'BNP Paribas Bank Polska', 'PPABPLPK', 1, '2026-04-21 13:29:21'),
(365, 'PL', 'Credit Agricole Bank Polska', 'AGRIPLPR', 1, '2026-04-21 13:29:21'),
(366, 'CZ', 'UniCredit Bank Czech Republic', 'BACXCZPP', 1, '2026-04-21 13:29:21'),
(367, 'CZ', 'Komercni banka', 'KOMBCZPP', 1, '2026-04-21 13:31:34'),
(368, 'CZ', 'Moneta Money Bank', 'AGBACZPP', 1, '2026-04-21 13:31:34'),
(369, 'CZ', 'Raiffeisenbank Czech Republic', 'RZBCCZPP', 1, '2026-04-21 13:31:34'),
(370, 'CZ', 'Air Bank', 'AIRACZPP', 1, '2026-04-21 13:31:34'),
(371, 'CZ', 'Fio banka', 'FIOBCZPP', 1, '2026-04-21 13:31:34'),
(372, 'CZ', 'Trinity Bank', 'MPUBCZPP', 1, '2026-04-21 13:31:34'),
(373, 'CZ', 'J&T Banka', 'JTBPCZPP', 1, '2026-04-21 13:31:34'),
(374, 'CZ', 'PPF banka', 'PMBPCZPP', 1, '2026-04-21 13:31:34'),
(375, 'CZ', 'mBank Czech Republic', 'BREXCZPP', 1, '2026-04-21 13:31:34'),
(376, 'GR', 'Eurobank', 'ERBKGRAA', 1, '2026-04-21 13:31:34'),
(377, 'GR', 'Alpha Bank Greece', 'CRBAGRAA', 1, '2026-04-21 13:31:34'),
(378, 'GR', 'Attica Bank', 'ATTIGRAA', 1, '2026-04-21 13:31:34'),
(379, 'GR', 'Optima Bank', 'OPTMGRAA', 1, '2026-04-21 13:31:34'),
(380, 'GR', 'Pancreta Bank', 'CHQBGRAX', 1, '2026-04-21 13:31:34'),
(381, 'GR', 'Viva Bank', 'VIVBGR21', 1, '2026-04-21 13:31:34'),
(382, 'GR', 'ProCredit Bank Greece', 'PRCBGRAA', 1, '2026-04-21 13:31:34'),
(383, 'GR', 'CrediaBank', 'PBAGRAXX', 1, '2026-04-21 13:31:34'),
(384, 'GR', 'Hellenic Development Bank', 'EATEGR21', 1, '2026-04-21 13:31:34'),
(385, 'GR', 'Cooperative Bank of Epirus', 'STSPGR21', 1, '2026-04-21 13:31:34'),
(386, 'HU', 'Erste Bank Hungary', 'GIBAHUHB', 1, '2026-04-21 13:31:34'),
(387, 'HU', 'UniCredit Bank Hungary', 'BACXHUHB', 1, '2026-04-21 13:31:34'),
(388, 'HU', 'Raiffeisen Bank Hungary', 'UBRTHUHB', 1, '2026-04-21 13:31:34'),
(389, 'HU', 'MBH Bank', 'MKKBHUHB', 1, '2026-04-21 13:31:34'),
(390, 'HU', 'CIB Bank', 'CIBHHUHB', 1, '2026-04-21 13:31:34'),
(391, 'HU', 'KDB Bank Europe', 'KDBLHUHB', 1, '2026-04-21 13:31:34'),
(392, 'HU', 'MagNet Bank', 'HBWEHUHB', 1, '2026-04-21 13:31:34'),
(393, 'HU', 'Granit Bank', 'GNBAHUHB', 1, '2026-04-21 13:31:34'),
(394, 'HU', 'Polgari Bank', 'POLBHU22', 1, '2026-04-21 13:31:34'),
(395, 'HU', 'Cetelem Bank Hungary', 'CETEHUHB', 1, '2026-04-21 13:31:34'),
(396, 'RO', 'BRD Groupe Societe Generale', 'BRDEROBU', 1, '2026-04-21 13:31:34'),
(397, 'RO', 'Raiffeisen Bank Romania', 'RZBRROBU', 1, '2026-04-21 13:31:34'),
(398, 'RO', 'UniCredit Bank Romania', 'BACXROBU', 1, '2026-04-21 13:31:34'),
(399, 'RO', 'CEC Bank', 'CECOROBU', 1, '2026-04-21 13:31:34'),
(400, 'RO', 'ING Bank Romania', 'INGBROBU', 1, '2026-04-21 13:31:34'),
(401, 'RO', 'Alpha Bank Romania', 'BUCUROBU', 1, '2026-04-21 13:31:34'),
(402, 'RO', 'OTP Bank Romania', 'OTPVROBU', 1, '2026-04-21 13:31:34'),
(403, 'RO', 'Garanti BBVA Romania', 'UGBIROBU', 1, '2026-04-21 13:31:34'),
(404, 'RO', 'Vista Bank Romania', 'VISTROBU', 1, '2026-04-21 13:31:34'),
(405, 'RO', 'Libra Internet Bank', 'BRELROBU', 1, '2026-04-21 13:31:34'),
(406, 'IN', 'State Bank of India', 'SBININBB', 1, '2026-07-02 12:44:18'),
(407, 'IN', 'HDFC Bank', 'HDFCINBB', 1, '2026-07-02 12:44:18'),
(408, 'IN', 'ICICI Bank', 'ICICINBB', 1, '2026-07-02 12:44:18'),
(409, 'IN', 'Axis Bank', 'AXISINBB', 1, '2026-07-02 12:44:18'),
(410, 'IN', 'Kotak Mahindra Bank', 'KKBKINBB', 1, '2026-07-02 12:44:18'),
(411, 'IN', 'Punjab National Bank', 'PUNBINBB', 1, '2026-07-02 12:44:18'),
(412, 'IN', 'Bank of Baroda', 'BARBINBB', 1, '2026-07-02 12:44:18'),
(413, 'IN', 'Canara Bank', 'CNRBINBB', 1, '2026-07-02 12:44:18'),
(414, 'IN', 'IndusInd Bank', 'INDBINBB', 1, '2026-07-02 12:44:18'),
(415, 'IN', 'Yes Bank', 'YESBINBB', 1, '2026-07-02 12:44:18'),
(416, 'TH', 'Bangkok Bank', 'BKKBTHBK', 1, '2026-07-02 12:44:18'),
(417, 'TH', 'Kasikorn Bank', 'KASITHBK', 1, '2026-07-02 12:44:18'),
(418, 'TH', 'Siam Commercial Bank', 'SICOTHBK', 1, '2026-07-02 12:44:18'),
(419, 'TH', 'Krungthai Bank', 'KRTHTHBK', 1, '2026-07-02 12:44:18'),
(420, 'TH', 'TMBThanachart Bank', 'TMBKTHBK', 1, '2026-07-02 12:44:18'),
(421, 'TH', 'Kiatnakin Phatra Bank', 'KKPBTHBK', 1, '2026-07-02 12:44:18'),
(422, 'TH', 'CIMB Thai', 'UBOBTHBK', 1, '2026-07-02 12:44:18'),
(423, 'TH', 'Government Savings Bank', 'GSBATHBK', 1, '2026-07-02 12:44:18'),
(424, 'TH', 'Bank of Ayudhya', 'AYUDTHBK', 1, '2026-07-02 12:44:18'),
(425, 'TH', 'Land and Houses Bank', 'LAHRTHB1', 1, '2026-07-02 12:44:18'),
(426, 'MY', 'Maybank', 'MBBEMYKL', 1, '2026-07-02 12:44:18'),
(427, 'MY', 'CIMB Bank', 'CIBBMYKL', 1, '2026-07-02 12:44:18'),
(428, 'MY', 'Public Bank', 'PBBEMYKL', 1, '2026-07-02 12:44:18'),
(429, 'MY', 'RHB Bank', 'RHBBMYKL', 1, '2026-07-02 12:44:18'),
(430, 'MY', 'Hong Leong Bank', 'HLBBMYKL', 1, '2026-07-02 12:44:18'),
(431, 'MY', 'AmBank', 'ARBKMYKL', 1, '2026-07-02 12:44:18'),
(432, 'MY', 'Alliance Bank', 'MFBBMYKL', 1, '2026-07-02 12:44:18'),
(433, 'MY', 'Bank Islam', 'BIMBMYKL', 1, '2026-07-02 12:44:18'),
(434, 'MY', 'Affin Bank', 'PHBMMYKL', 1, '2026-07-02 12:44:18'),
(435, 'MY', 'Standard Chartered Malaysia', 'SCBLMYKX', 1, '2026-07-02 12:44:18'),
(436, 'ID', 'Bank Mandiri', 'BMRIIDJA', 1, '2026-07-02 12:44:18'),
(437, 'ID', 'Bank Rakyat Indonesia', 'BRINIDJA', 1, '2026-07-02 12:44:18'),
(438, 'ID', 'Bank Central Asia', 'CENAIDJA', 1, '2026-07-02 12:44:18'),
(439, 'ID', 'Bank Negara Indonesia', 'BNINIDJA', 1, '2026-07-02 12:44:18'),
(440, 'ID', 'Bank Tabungan Negara', 'BTANIDJA', 1, '2026-07-02 12:44:18'),
(441, 'ID', 'Bank CIMB Niaga', 'BNIAIDJA', 1, '2026-07-02 12:44:18'),
(442, 'ID', 'Bank Danamon', 'BDINIDJA', 1, '2026-07-02 12:44:18'),
(443, 'ID', 'Bank Permata', 'BBBAIDJA', 1, '2026-07-02 12:44:18'),
(444, 'ID', 'OCBC NISP', 'NISPIDJA', 1, '2026-07-02 12:44:18'),
(445, 'ID', 'Panin Bank', 'PINBIDJA', 1, '2026-07-02 12:44:18'),
(446, 'PH', 'BDO Unibank', 'BNORPHMM', 1, '2026-07-02 12:44:18'),
(447, 'PH', 'Metropolitan Bank', 'MBTCPHMM', 1, '2026-07-02 12:44:18'),
(448, 'PH', 'Bank of the Philippine Islands', 'BOPIPHMM', 1, '2026-07-02 12:44:18'),
(449, 'PH', 'Land Bank of the Philippines', 'TLBPPHMM', 1, '2026-07-02 12:44:18'),
(450, 'PH', 'Philippine National Bank', 'PNBMPHMM', 1, '2026-07-02 12:44:18'),
(451, 'PH', 'Security Bank', 'SETCPHMM', 1, '2026-07-02 12:44:18'),
(452, 'PH', 'Union Bank of the Philippines', 'UBPHPHMM', 1, '2026-07-02 12:44:18'),
(453, 'PH', 'China Banking Corporation', 'CHBKPHMM', 1, '2026-07-02 12:44:18'),
(454, 'PH', 'Rizal Commercial Banking', 'RCBCPHMM', 1, '2026-07-02 12:44:18'),
(455, 'PH', 'Development Bank of the Philippines', 'DBPHPHMM', 1, '2026-07-02 12:44:18'),
(456, 'PK', 'Habib Bank', 'HABBPKKA', 1, '2026-07-02 12:44:18'),
(457, 'PK', 'United Bank', 'UNILPKKA', 1, '2026-07-02 12:44:18'),
(458, 'PK', 'MCB Bank', 'MUCBPKKA', 1, '2026-07-02 12:44:18'),
(459, 'PK', 'Allied Bank', 'ABPAPKKA', 1, '2026-07-02 12:44:18'),
(460, 'PK', 'National Bank of Pakistan', 'NBPAPKKA', 1, '2026-07-02 12:44:18'),
(461, 'PK', 'Bank Alfalah', 'ALFHPKKA', 1, '2026-07-02 12:44:18'),
(462, 'PK', 'Meezan Bank', 'MEZNPKKA', 1, '2026-07-02 12:44:18'),
(463, 'PK', 'Faysal Bank', 'FAYSPKKA', 1, '2026-07-02 12:44:18'),
(464, 'PK', 'Askari Bank', 'ASCMPKKA', 1, '2026-07-02 12:44:18'),
(465, 'PK', 'Bank Al Habib', 'BAHLPKKA', 1, '2026-07-02 12:44:18'),
(466, 'VN', 'Vietcombank', 'BFTVVNVX', 1, '2026-07-02 12:44:18'),
(467, 'VN', 'VietinBank', 'ICBVVNVX', 1, '2026-07-02 12:44:18'),
(468, 'VN', 'BIDV', 'BIDVVNVX', 1, '2026-07-02 12:44:18'),
(469, 'VN', 'Agribank', 'VBAAVNVX', 1, '2026-07-02 12:44:18'),
(470, 'VN', 'Techcombank', 'VTCBVNVX', 1, '2026-07-02 12:44:18'),
(471, 'VN', 'VPBank', 'VPBKVNVX', 1, '2026-07-02 12:44:18'),
(472, 'VN', 'MB Bank', 'MSCBVNVX', 1, '2026-07-02 12:44:18'),
(473, 'VN', 'ACB', 'ASCBVNVX', 1, '2026-07-02 12:44:18'),
(474, 'VN', 'Sacombank', 'SGTTVNVX', 1, '2026-07-02 12:44:18'),
(475, 'VN', 'HDBank', 'HDBCVNVX', 1, '2026-07-02 12:44:18'),
(476, 'TW', 'CTBC Bank', 'CTCBTWTP', 1, '2026-07-02 12:44:18'),
(477, 'TW', 'Cathay United Bank', 'UWCBTWTP', 1, '2026-07-02 12:44:18'),
(478, 'TW', 'E.SUN Bank', 'ESUNTWTP', 1, '2026-07-02 12:44:18'),
(479, 'TW', 'Taipei Fubon Bank', 'TPBKTWTP', 1, '2026-07-02 12:44:18'),
(480, 'TW', 'Taiwan Cooperative Bank', 'TACBTWTP', 1, '2026-07-02 12:44:18'),
(481, 'TW', 'First Commercial Bank', 'FCBKTWTP', 1, '2026-07-02 12:44:18'),
(482, 'TW', 'Hua Nan Bank', 'HNBKTWTP', 1, '2026-07-02 12:44:18'),
(483, 'TW', 'Mega International Commercial Bank', 'ICBCTWTP', 1, '2026-07-02 12:44:18'),
(484, 'TW', 'Taishin International Bank', 'TSIBTWTP', 1, '2026-07-02 12:44:18'),
(485, 'TW', 'Land Bank of Taiwan', 'LBOTTWTP', 1, '2026-07-02 12:44:18'),
(486, 'IL', 'Bank Leumi', 'LUMIILIT', 1, '2026-07-02 12:44:18'),
(487, 'IL', 'Bank Hapoalim', 'POALILIT', 1, '2026-07-02 12:44:18'),
(488, 'IL', 'Israel Discount Bank', 'IDBLILIT', 1, '2026-07-02 12:44:18'),
(489, 'IL', 'Mizrahi Tefahot Bank', 'MIZBILIT', 1, '2026-07-02 12:44:18'),
(490, 'IL', 'First International Bank', 'FIRBILIT', 1, '2026-07-02 12:44:18'),
(491, 'IL', 'Bank of Jerusalem', 'JERSILIT', 1, '2026-07-02 12:44:18'),
(492, 'IL', 'Mercantile Discount Bank', 'MERCILIT', 1, '2026-07-02 12:44:18'),
(493, 'IL', 'Union Bank of Israel', 'UNBKILIT', 1, '2026-07-02 12:44:18'),
(494, 'IL', 'Poalim IBI', 'POALILIB', 1, '2026-07-02 12:44:18'),
(495, 'IL', 'Bank Otsar Ha-Hayal', 'OTSHILIT', 1, '2026-07-02 12:44:18'),
(496, 'TR', 'Turkiye Is Bankasi', 'ISBKTRIS', 1, '2026-07-02 12:44:18'),
(497, 'TR', 'Garanti BBVA', 'TGBATRIS', 1, '2026-07-02 12:44:18'),
(498, 'TR', 'Akbank', 'AKBKTRIS', 1, '2026-07-02 12:44:18'),
(499, 'TR', 'Yapi Kredi Bank', 'YAPITRIS', 1, '2026-07-02 12:44:18'),
(500, 'TR', 'Ziraat Bankasi', 'TCZBTR2A', 1, '2026-07-02 12:44:18'),
(501, 'TR', 'Halkbank', 'TRHBTR2A', 1, '2026-07-02 12:44:18'),
(502, 'TR', 'VakifBank', 'TVBATR2A', 1, '2026-07-02 12:44:18'),
(503, 'TR', 'Denizbank', 'DENITRIS', 1, '2026-07-02 12:44:18'),
(504, 'TR', 'QNB Finansbank', 'FNNBTRIS', 1, '2026-07-02 12:44:18'),
(505, 'TR', 'Kuveyt Turk', 'KTEFTRIS', 1, '2026-07-02 12:44:18'),
(506, 'OM', 'Bank Muscat', 'BMUSOMRX', 1, '2026-07-02 12:44:18'),
(507, 'OM', 'National Bank of Oman', 'NBOMOMRX', 1, '2026-07-02 12:44:18'),
(508, 'OM', 'HSBC Oman', 'BBMEOMRX', 1, '2026-07-02 12:44:18'),
(509, 'OM', 'Ahli Bank Oman', 'AUBOOMRU', 1, '2026-07-02 12:44:18'),
(510, 'OM', 'Bank Dhofar', 'BDOFOMRU', 1, '2026-07-02 12:44:18'),
(511, 'OM', 'Oman Arab Bank', 'OMABOMRU', 1, '2026-07-02 12:44:18'),
(512, 'OM', 'Sohar International', 'BSHROMRU', 1, '2026-07-02 12:44:18'),
(513, 'OM', 'Bank Nizwa', 'BNZWOMRX', 1, '2026-07-02 12:44:18'),
(514, 'OM', 'Alizz Islamic Bank', 'AIZZOMRX', 1, '2026-07-02 12:44:18'),
(515, 'OM', 'Meethaq Islamic Bank', 'METHOMRX', 1, '2026-07-02 12:44:18'),
(516, 'JO', 'Arab Bank', 'ARABJOAX', 1, '2026-07-02 12:44:18'),
(517, 'JO', 'Housing Bank for Trade and Finance', 'HBHOJOAX', 1, '2026-07-02 12:44:18'),
(518, 'JO', 'Jordan Kuwait Bank', 'JKBAJOAM', 1, '2026-07-02 12:44:18'),
(519, 'JO', 'Cairo Amman Bank', 'CAABJOAM', 1, '2026-07-02 12:44:18'),
(520, 'JO', 'Islamic International Arab Bank', 'IIBAJOAM', 1, '2026-07-02 12:44:18'),
(521, 'JO', 'Bank al Etihad', 'ETIHJOAX', 1, '2026-07-02 12:44:18'),
(522, 'JO', 'Capital Bank of Jordan', 'EFBKJOAM', 1, '2026-07-02 12:44:18'),
(523, 'JO', 'Societe Generale Jordan', 'SGBLJOAM', 1, '2026-07-02 12:44:18'),
(524, 'JO', 'Ahli Bank Jordan', 'ABCOJOAM', 1, '2026-07-02 12:44:18'),
(525, 'JO', 'Jordan Commercial Bank', 'JGBAJOAM', 1, '2026-07-02 12:44:18'),
(526, 'LB', 'Bank Audi', 'AUDBLBBX', 1, '2026-07-02 12:44:18'),
(527, 'LB', 'Blom Bank', 'BLOMLBBX', 1, '2026-07-02 12:44:18'),
(528, 'LB', 'Byblos Bank', 'BYBALBBX', 1, '2026-07-02 12:44:18'),
(529, 'LB', 'Bank of Beirut', 'BABOLBBX', 1, '2026-07-02 12:44:18'),
(530, 'LB', 'Fransabank', 'FSABLBBX', 1, '2026-07-02 12:44:18'),
(531, 'LB', 'Credit Libanais', 'CLIBLBBX', 1, '2026-07-02 12:44:18'),
(532, 'LB', 'SGBL', 'SGLILBBX', 1, '2026-07-02 12:44:18'),
(533, 'LB', 'IBL Bank', 'IBLILBBX', 1, '2026-07-02 12:44:18'),
(534, 'LB', 'Banque Libano-Francaise', 'BLFLLBBX', 1, '2026-07-02 12:44:18'),
(535, 'LB', 'BankMed', 'MEDCLBBX', 1, '2026-07-02 12:44:18'),
(536, 'IQ', 'Rafidain Bank', 'RAFIIQBA', 1, '2026-07-02 12:44:18'),
(537, 'IQ', 'Rasheed Bank', 'RASHIQBA', 1, '2026-07-02 12:44:18'),
(538, 'IQ', 'Trade Bank of Iraq', 'TRIQIQBA', 1, '2026-07-02 12:44:18'),
(539, 'IQ', 'Iraqi Islamic Bank', 'IRIBIQBA', 1, '2026-07-02 12:44:18'),
(540, 'IQ', 'Bank of Baghdad', 'BBOBIQBA', 1, '2026-07-02 12:44:18'),
(541, 'IQ', 'North Bank', 'NORBIQBA', 1, '2026-07-02 12:44:18'),
(542, 'IQ', 'Cihan Bank', 'CIHNIQBA', 1, '2026-07-02 12:44:18'),
(543, 'IQ', 'Gulf Commercial Bank', 'GULFIQBA', 1, '2026-07-02 12:44:18'),
(544, 'IQ', 'Ashur International Bank', 'ASHUIQBA', 1, '2026-07-02 12:44:18'),
(545, 'IQ', 'First Iraqi Bank', 'FIRBIQBA', 1, '2026-07-02 12:44:18'),
(546, 'MX', 'BBVA Mexico', 'BCMRMXMM', 1, '2026-07-02 12:44:18'),
(547, 'MX', 'Citibanamex', 'BNMXMXMM', 1, '2026-07-02 12:44:18'),
(548, 'MX', 'Santander Mexico', 'BMSXMXMM', 1, '2026-07-02 12:44:18'),
(549, 'MX', 'Banorte', 'MENOMXMT', 1, '2026-07-02 12:44:18'),
(550, 'MX', 'HSBC Mexico', 'BIMEMXMM', 1, '2026-07-02 12:44:18'),
(551, 'MX', 'Scotiabank Mexico', 'MBCOMXMM', 1, '2026-07-02 12:44:18'),
(552, 'MX', 'Inbursa', 'INBUMXMM', 1, '2026-07-02 12:44:18'),
(553, 'MX', 'Banco Azteca', 'AZTKMXMM', 1, '2026-07-02 12:44:18'),
(554, 'MX', 'Banregio', 'RGIOMXMT', 1, '2026-07-02 12:44:18'),
(555, 'MX', 'Banco del Bajio', 'BJIOMXMX', 1, '2026-07-02 12:44:18'),
(556, 'BR', 'Itau Unibanco', 'ITAUBRSP', 1, '2026-07-02 12:44:18'),
(557, 'BR', 'Bradesco', 'BBDEBRSP', 1, '2026-07-02 12:44:18'),
(558, 'BR', 'Banco do Brasil', 'BRASBRSP', 1, '2026-07-02 12:44:18'),
(559, 'BR', 'Caixa Economica Federal', 'CEFXBRSP', 1, '2026-07-02 12:44:18'),
(560, 'BR', 'Santander Brasil', 'BSCHBRSP', 1, '2026-07-02 12:44:18'),
(561, 'BR', 'BTG Pactual', 'BPACBRSP', 1, '2026-07-02 12:44:18'),
(562, 'BR', 'Banco Safra', 'SAFRBRSP', 1, '2026-07-02 12:44:18'),
(563, 'BR', 'Banrisul', 'BRSUBRSP', 1, '2026-07-02 12:44:18'),
(564, 'BR', 'Sicoob', 'CPBNBRSP', 1, '2026-07-02 12:44:18'),
(565, 'BR', 'Nu Pagamentos', 'NUBCBRSP', 1, '2026-07-02 12:44:18'),
(566, 'AR', 'Banco de la Nacion Argentina', 'NACNARBA', 1, '2026-07-02 12:44:18'),
(567, 'AR', 'Banco Galicia', 'GALIARBA', 1, '2026-07-02 12:44:18'),
(568, 'AR', 'Banco Macro', 'BCRAARBA', 1, '2026-07-02 12:44:18'),
(569, 'AR', 'BBVA Argentina', 'FRIPARBA', 1, '2026-07-02 12:44:19'),
(570, 'AR', 'Banco Santander Rio', 'BSCHARBA', 1, '2026-07-02 12:44:19'),
(571, 'AR', 'ICBC Argentina', 'ICBKARBA', 1, '2026-07-02 12:44:19'),
(572, 'AR', 'Banco Credicoop', 'BCOOARBA', 1, '2026-07-02 12:44:19'),
(573, 'AR', 'Banco Provincia', 'PRBAARBA', 1, '2026-07-02 12:44:19'),
(574, 'AR', 'HSBC Argentina', 'BACOARBA', 1, '2026-07-02 12:44:19'),
(575, 'AR', 'Banco Ciudad', 'BACIARBA', 1, '2026-07-02 12:44:19'),
(576, 'CO', 'Bancolombia', 'COLOCOBM', 1, '2026-07-02 12:44:19'),
(577, 'CO', 'Banco de Bogota', 'BBOGCOBB', 1, '2026-07-02 12:44:19'),
(578, 'CO', 'Davivienda', 'CAFECOBB', 1, '2026-07-02 12:44:19'),
(579, 'CO', 'Banco de Occidente', 'OCCICOBC', 1, '2026-07-02 12:44:19'),
(580, 'CO', 'Banco Popular', 'BPOPCOBB', 1, '2026-07-02 12:44:19'),
(581, 'CO', 'BBVA Colombia', 'GEROCOBB', 1, '2026-07-02 12:44:19'),
(582, 'CO', 'Scotiabank Colpatria', 'COLPCOBB', 1, '2026-07-02 12:44:19'),
(583, 'CO', 'Banco AV Villas', 'BAVVCOBB', 1, '2026-07-02 12:44:19'),
(584, 'CO', 'Banco Caja Social', 'CASOCOBB', 1, '2026-07-02 12:44:19');
INSERT INTO `international_banks` (`id`, `country_code`, `bank_name`, `swift_prefix`, `is_active`, `created_at`) VALUES
(585, 'CO', 'Itau Colombia', 'BCTOCOBB', 1, '2026-07-02 12:44:19'),
(586, 'LU', 'Banque et Caisse Epargne', 'BCEELULL', 1, '2026-07-02 12:44:19'),
(587, 'LU', 'BGL BNP Paribas', 'BGLLLULL', 1, '2026-07-02 12:44:19'),
(588, 'LU', 'Banque Internationale Luxembourg', 'BILLLULL', 1, '2026-07-02 12:44:19'),
(589, 'LU', 'Banque de Luxembourg', 'BLUXLULL', 1, '2026-07-02 12:44:19'),
(590, 'LU', 'Banque Raiffeisen Luxembourg', 'BRLULULL', 1, '2026-07-02 12:44:19'),
(591, 'LU', 'ING Luxembourg', 'INGBLULX', 1, '2026-07-02 12:44:19'),
(592, 'LU', 'Deutsche Bank Luxembourg', 'DEUTLULL', 1, '2026-07-02 12:44:19'),
(593, 'LU', 'Societe Generale Luxembourg', 'SGABLULL', 1, '2026-07-02 12:44:19'),
(594, 'LU', 'J.P. Morgan Luxembourg', 'CHASLULX', 1, '2026-07-02 12:44:19'),
(595, 'LU', 'Banque de Commerce Luxembourg', 'BCLXLULL', 1, '2026-07-02 12:44:19'),
(596, 'MT', 'Bank of Valletta', 'VALLMTMT', 1, '2026-07-02 12:44:19'),
(597, 'MT', 'HSBC Bank Malta', 'HSBCMTMT', 1, '2026-07-02 12:44:19'),
(598, 'MT', 'BNF Bank', 'BNFAMTMT', 1, '2026-07-02 12:44:19'),
(599, 'MT', 'Lombard Bank Malta', 'LOMBMTMT', 1, '2026-07-02 12:44:19'),
(600, 'MT', 'APS Bank', 'APSBMTMT', 1, '2026-07-02 12:44:19'),
(601, 'MT', 'ME Bank', 'MEBKMTMT', 1, '2026-07-02 12:44:19'),
(602, 'MT', 'Izola Bank', 'IZOLMTMT', 1, '2026-07-02 12:44:19'),
(603, 'MT', 'FIMBank', 'FIMBMTMT', 1, '2026-07-02 12:44:19'),
(604, 'MT', 'Sparkasse Bank Malta', 'SPRKMTMT', 1, '2026-07-02 12:44:19'),
(605, 'MT', 'AgriBank', 'AGRKMTMT', 1, '2026-07-02 12:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `international_dashboard_profile`
--

CREATE TABLE `international_dashboard_profile` (
  `id` int(11) NOT NULL,
  `balance_usd` decimal(18,2) NOT NULL,
  `vault_label` varchar(100) NOT NULL,
  `masked_pan` varchar(30) NOT NULL,
  `route_label` varchar(100) NOT NULL,
  `route_description` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_dashboard_profile`
--

INSERT INTO `international_dashboard_profile` (`id`, `balance_usd`, `vault_label`, `masked_pan`, `route_label`, `route_description`, `updated_at`) VALUES
(1, '48250.40', 'Savings Vault • Platinum', '**** **** **** 8824', 'SWIFT Network', 'Delivery within 24-48 hours via premium rails.', '2026-03-26 22:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `international_recent_transfers`
--

CREATE TABLE `international_recent_transfers` (
  `id` int(11) NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `subtitle` varchar(180) NOT NULL,
  `transfer_date` varchar(80) NOT NULL,
  `amount_usd` decimal(18,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status_label` varchar(30) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_recent_transfers`
--

INSERT INTO `international_recent_transfers` (`id`, `recipient_name`, `subtitle`, `transfer_date`, `amount_usd`, `currency`, `status_label`, `sort_order`) VALUES
(1, 'Julianne De Luca', 'Chase Bank • London', 'Oct 24, 2024', '2400.00', 'USD', 'Success', 1),
(2, 'Aisha Mohammed', 'Access Bank • Lagos', 'Oct 22, 2024', '12850.00', 'USD', 'Pending', 2),
(3, 'Karl Lagerfeld Est.', 'SWIFT • Berlin', 'Oct 19, 2024', '850.00', 'USD', 'Failed', 3);

-- --------------------------------------------------------

--
-- Table structure for table `international_sender_settings`
--

CREATE TABLE `international_sender_settings` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_bank` varchar(150) NOT NULL,
  `sender_country` varchar(100) NOT NULL,
  `sender_address_line1` varchar(120) NOT NULL,
  `sender_address_line2` varchar(120) DEFAULT NULL,
  `sender_address_line3` varchar(120) DEFAULT NULL,
  `sender_swift` varchar(30) DEFAULT NULL,
  `sender_iban` varchar(60) DEFAULT NULL,
  `default_delivery_date` date DEFAULT NULL,
  `receipt_logo_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_sender_settings`
--

INSERT INTO `international_sender_settings` (`id`, `sender_name`, `sender_bank`, `sender_country`, `sender_address_line1`, `sender_address_line2`, `sender_address_line3`, `sender_swift`, `sender_iban`, `default_delivery_date`, `receipt_logo_path`, `updated_at`) VALUES
(1, 'ITEC SYSTEMS', 'DFCU BANK LIMITED', 'UGANDA', 'DFCU TOWERS PLOT 26 KYADONDO ROAD NAKASERO', 'ROAD NAKASERO', '4 RUE DE LA LIBERATION 3510 DUDELANGE LUXEMBOUG', 'DFCUUGKAXXX', '01363657953808', '2026-07-08', '/uploads/receipt_logos/receipt_f86fa6f4dbe32238.jpg', '2026-07-03 14:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `international_status`
--

CREATE TABLE `international_status` (
  `id` int(11) NOT NULL,
  `status` enum('none','pending','failed','reversed','network_error') NOT NULL DEFAULT 'none',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_status`
--

INSERT INTO `international_status` (`id`, `status`, `updated_at`) VALUES
(1, '', '2026-07-21 07:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `international_transactions`
--

CREATE TABLE `international_transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `beneficiary_iban` varchar(60) NOT NULL,
  `beneficiary_name` varchar(150) NOT NULL,
  `beneficiary_address` text NOT NULL,
  `swift_code` varchar(30) NOT NULL,
  `routing_number` varchar(9) NOT NULL DEFAULT '',
  `bank_name` varchar(150) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT '',
  `amount` decimal(18,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `message_type` varchar(80) NOT NULL,
  `delivery_date` date NOT NULL,
  `status` enum('SUCCESSFUL','PENDING','PROCESSING','FAILED','REVERSED','NETWORK_ERROR') NOT NULL DEFAULT 'SUCCESSFUL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `international_transactions`
--

INSERT INTO `international_transactions` (`id`, `reference`, `beneficiary_iban`, `beneficiary_name`, `beneficiary_address`, `swift_code`, `routing_number`, `bank_name`, `country_code`, `country_name`, `amount`, `currency`, `message_type`, `delivery_date`, `status`, `created_at`) VALUES
(64, 'ELY-1783023824692-129270', '1016168380', 'ASIA HOUSE', 'SG,29 Bd Haussmann, 75009 Paris, France', 'SCBLUS33XXX', '', 'Societe Generale', 'FR', 'France', '3000000.00', 'EUR', 'MT103 Cash Transfer', '2026-07-06', 'FAILED', '2026-07-02 20:23:52'),
(65, 'ELY-1783089822634-503458', '920020056372758', 'ASIA HOUSE', 'HARTHALA BRANCH, MORADABAD-244001, U.P, INDIA', 'AXISINBB282', '', 'Axis Bank', 'IN', 'India', '3000000.00', 'USD', 'MT103 Cash Transfer', '2026-07-08', 'REVERSED', '2026-07-03 14:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `local_dashboard_profile`
--

CREATE TABLE `local_dashboard_profile` (
  `id` int(11) NOT NULL,
  `account_type` varchar(100) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_number` varchar(30) NOT NULL DEFAULT '',
  `balance` decimal(18,2) NOT NULL,
  `masked_pan` varchar(30) NOT NULL,
  `tier_label` varchar(50) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_dashboard_profile`
--

INSERT INTO `local_dashboard_profile` (`id`, `account_type`, `account_name`, `account_number`, `balance`, `masked_pan`, `tier_label`, `updated_at`) VALUES
(1, 'Local Savings Account', 'Tunde O. Badmus', '1022090307', '10998000000.00', '**** 4492', 'Premium Tier', '2026-08-12 08:11:08');

-- --------------------------------------------------------

--
-- Table structure for table `local_frequent_recipients`
--

CREATE TABLE `local_frequent_recipients` (
  `id` int(11) NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_frequent_recipients`
--

INSERT INTO `local_frequent_recipients` (`id`, `recipient_name`, `sort_order`) VALUES
(1, 'Tayo', 1),
(2, 'Soji', 2),
(3, 'Ife', 3);

-- --------------------------------------------------------

--
-- Table structure for table `local_recent_transfers`
--

CREATE TABLE `local_recent_transfers` (
  `id` int(11) NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `subtitle` varchar(180) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `direction` enum('debit','credit') NOT NULL DEFAULT 'debit',
  `status_label` varchar(30) NOT NULL DEFAULT 'Success',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_recent_transfers`
--

INSERT INTO `local_recent_transfers` (`id`, `recipient_name`, `subtitle`, `amount`, `direction`, `status_label`, `sort_order`) VALUES
(1, 'Chioma Uzor', 'GTBank • 2:45 PM', '25000.00', 'debit', 'Success', 1),
(2, 'MTN Nigeria', 'Airtime Purchase • 11:20 AM', '5000.00', 'debit', 'Success', 2),
(3, 'Kola Lawal', 'Access Bank • Yesterday', '150000.00', 'debit', 'Success', 3),
(4, 'Self Top-up', 'Zenith Bank • Yesterday', '500000.00', 'credit', 'Success', 4);

-- --------------------------------------------------------

--
-- Table structure for table `local_transactions`
--

CREATE TABLE `local_transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'NGN',
  `beneficiary_name` varchar(150) NOT NULL,
  `beneficiary_bank` varchar(120) NOT NULL,
  `beneficiary_account` varchar(30) NOT NULL,
  `sender_account` varchar(30) NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `purpose` varchar(180) DEFAULT NULL,
  `status` enum('SUCCESSFUL','FAILED','PENDING') NOT NULL DEFAULT 'SUCCESSFUL',
  `direction` enum('debit','credit') NOT NULL DEFAULT 'debit',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_transactions`
--

INSERT INTO `local_transactions` (`id`, `reference`, `amount`, `currency`, `beneficiary_name`, `beneficiary_bank`, `beneficiary_account`, `sender_account`, `sender_name`, `purpose`, `status`, `direction`, `transaction_date`) VALUES
(1, 'LOC1776626787991224', '5000.00', 'NGN', 'JAMES MUSA SAMAILA', 'Access Bank PLC', '0105135790', '1022090307', 'Tunde O. Badmus', NULL, 'SUCCESSFUL', 'debit', '2026-04-19 19:26:32'),
(2, 'LOC1776629586767191', '200000.00', 'NGN', 'SPLENDID HOOD ENTERPRISE', 'Zenith Bank', '1311795728', '1022090307', 'Tunde O. Badmus', 'LAND PAYMENT', 'SUCCESSFUL', 'debit', '2026-04-19 20:13:09'),
(3, 'LOC1785752784399183', '1000000.00', 'NGN', 'SPLENDID HOOD ENTERPRISE', 'ALAT by WEMA', '0125813950', '1022090307', 'Tunde O. Badmus', 'LAND PAYMENT', 'SUCCESSFUL', 'debit', '2026-08-03 10:26:26'),
(4, 'LOC1786522267904432', '1000000.00', 'NGN', 'SPLENDID HOOD ENTERPRISE', 'ALAT by WEMA', '0125813950', '1022090307', 'Tunde O. Badmus', 'LAND PAYMENT', 'SUCCESSFUL', 'debit', '2026-08-12 08:11:08');

-- --------------------------------------------------------

--
-- Table structure for table `local_transfer_banks`
--

CREATE TABLE `local_transfer_banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(120) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `local_transfer_banks`
--

INSERT INTO `local_transfer_banks` (`id`, `bank_name`, `bank_code`, `is_active`) VALUES
(1, 'Access Bank PLC', '044', 1),
(2, 'Zenith Bank', '057', 1),
(3, 'Guaranty Trust Bank', '058', 1),
(4, 'United Bank for Africa', '033', 1),
(5, 'First Bank of Nigeria', '011', 1),
(6, 'Fidelity Bank', '070', 1),
(7, 'Union Bank of Nigeria', '032', 1),
(8, 'Sterling Bank', '232', 1),
(9, 'Stanbic IBTC Bank', '221', 1),
(10, 'Wema Bank', '035', 1),
(11, 'Polaris Bank', '076', 1),
(12, 'Keystone Bank', '082', 1),
(13, 'Heritage Bank', '030', 1),
(14, 'Jaiz Bank', '301', 1),
(15, 'Unity Bank', '215', 1),
(16, 'FCMB', '214', 1),
(17, 'Ecobank Nigeria', '050', 1),
(18, 'Standard Chartered Bank', '068', 1),
(19, 'Citibank Nigeria', '023', 1),
(20, 'Globus Bank', '00103', 1),
(21, 'SunTrust Bank', '100', 1),
(22, 'Providus Bank', '101', 1),
(23, 'Titan Trust Bank', '102', 1),
(24, 'Kuda Bank', '50211', 1),
(25, 'Moniepoint', '50515', 1),
(26, 'OPay', '999992', 1),
(27, 'PalmPay', '100033', 1),
(28, 'VFD Microfinance Bank', '566', 1),
(29, 'Rubies MFB', '125', 1),
(30, 'Sparkle MFB', '51310', 1),
(31, 'Carbon', '565', 1),
(32, 'ALAT by WEMA', '035A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `platform_status`
--

CREATE TABLE `platform_status` (
  `id` int(11) NOT NULL,
  `status` enum('on','off') NOT NULL DEFAULT 'on',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `platform_status`
--

INSERT INTO `platform_status` (`id`, `status`, `updated_at`) VALUES
(1, 'on', '2026-07-10 06:26:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `bank_status`
--
ALTER TABLE `bank_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_code` (`bank_code`);

--
-- Indexes for table `client_keys`
--
ALTER TABLE `client_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `international_banks`
--
ALTER TABLE `international_banks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_country_bank` (`country_code`,`bank_name`);

--
-- Indexes for table `international_dashboard_profile`
--
ALTER TABLE `international_dashboard_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `international_recent_transfers`
--
ALTER TABLE `international_recent_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `international_sender_settings`
--
ALTER TABLE `international_sender_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `international_status`
--
ALTER TABLE `international_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `international_transactions`
--
ALTER TABLE `international_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Indexes for table `local_dashboard_profile`
--
ALTER TABLE `local_dashboard_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `local_frequent_recipients`
--
ALTER TABLE `local_frequent_recipients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `local_recent_transfers`
--
ALTER TABLE `local_recent_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `local_transactions`
--
ALTER TABLE `local_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Indexes for table `local_transfer_banks`
--
ALTER TABLE `local_transfer_banks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_name` (`bank_name`);

--
-- Indexes for table `platform_status`
--
ALTER TABLE `platform_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bank_status`
--
ALTER TABLE `bank_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `client_keys`
--
ALTER TABLE `client_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `international_banks`
--
ALTER TABLE `international_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=606;

--
-- AUTO_INCREMENT for table `international_dashboard_profile`
--
ALTER TABLE `international_dashboard_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `international_recent_transfers`
--
ALTER TABLE `international_recent_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `international_transactions`
--
ALTER TABLE `international_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `local_dashboard_profile`
--
ALTER TABLE `local_dashboard_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `local_frequent_recipients`
--
ALTER TABLE `local_frequent_recipients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `local_recent_transfers`
--
ALTER TABLE `local_recent_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `local_transactions`
--
ALTER TABLE `local_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `local_transfer_banks`
--
ALTER TABLE `local_transfer_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
