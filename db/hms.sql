--
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `updationDate`) VALUES
(1, 'admin', '123456', '04-09-2021 05:28:27 PM');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `apid` int(11) NOT NULL,
  `doctorSpecialization` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `patient_Name` varchar(150) NOT NULL,
  `patient_Num` int(15) NOT NULL,
  `consultancyFees` int(11) DEFAULT NULL,
  `appointmentDate` varchar(255) DEFAULT NULL,
  `appointmentTime` varchar(255) DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `userStatus` int(11) DEFAULT NULL,
  `doctorStatus` int(11) DEFAULT NULL,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `employId` int(11) NOT NULL,
  `paid` int(50) DEFAULT NULL,
  `commnet` varchar(150) DEFAULT NULL,
  `method` varchar(150) DEFAULT NULL,
  `employname` varchar(150) DEFAULT 'Patient',
  `employStatues` int(11) DEFAULT NULL,
  `cancelledBy` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`apid`, `doctorSpecialization`, `doctorId`, `userId`, `patient_Name`, `patient_Num`, `consultancyFees`, `appointmentDate`, `appointmentTime`, `postingDate`, `userStatus`, `doctorStatus`, `updationDate`, `employId`, `paid`, `commnet`, `method`, `employname`, `employStatues`) VALUES
(218, 'orthopedics ', 13, 126, 'Zeyad ', 123451, 25, '2024-05-31', '15:49:15', '2024-05-31 14:18:55', 1, 1, '2024-05-31 14:19:58', 9, 23, '', 'Cash', 'amna mohamed', 0),
(219, 'General Surgery', 14, 127, 'moatasem ali', 323423423, 10, '2024-05-31', '16:18:56', '2024-05-31 14:19:33', 1, 1, '2024-05-31 14:19:58', 9, 10, '', 'Cash', 'amna mohamed', 0),
(221, 'Dermatologist', 18, 128, 'ahmed atef', 213123, 10, '2024-05-31', '16:21:49', '2024-05-31 14:22:14', 1, 1, '2024-05-31 14:22:49', 10, 0, '', 'Cash', 'abdelrahman gamal', 0),
(222, 'Gastroenterologist', 37, 129, 'amr abdelaziz', 435346, 450, '2024-05-31', '16:22:14', '2024-05-31 14:22:42', 1, 1, '2024-05-31 14:22:49', 10, 345, '', 'Cash', 'abdelrahman gamal', 0),
(225, 'Radiologist', 20, 130, 'waled hassan', 55857524, 350, '2024-05-31', '16:30:39', '2024-05-31 14:30:57', 1, 1, '2024-05-31 14:46:29', 11, 0, '', 'Cash', 'almoatasem ali', 0),
(230, 'Cardiology', 15, 134, 'ali ewias', 213123, 15, '2024-05-31', '16:34:48', '2024-05-31 14:39:33', 2, 2, '2024-05-31 16:25:26', 11, 0, '', 'Cash', 'almoatasem ali', 0),
(232, 'Pediatric specialist', 36, 135, 'ali hosam', 763452634, 300, '2024-05-31', '16:46:36', '2024-05-31 14:46:53', 1, 1, '2024-05-31 14:47:27', 12, 212, '', 'Cash', 'malak yahya', 0),
(233, 'Psychologist', 35, 136, 'amr mokhtar', 253245345, 350, '2024-05-31', '16:46:54', '2024-05-31 14:47:14', 1, 1, '2024-05-31 14:47:27', 12, 0, '', 'Cash', 'malak yahya', 0),
(237, 'Dentist', 40, 137, 'donia samer', 74845767, 600, '2024-05-31', '16:49:52', '2024-05-31 14:50:16', 1, 1, '2024-05-31 14:50:35', 14, 0, '', 'Cash', 'Amr Abdelaziz', 0),
(238, 'Gastroenterologist', 37, 138, 'amr anwar', 7853253, 450, '2024-05-31', '16:50:51', '2024-05-31 14:51:19', 1, 1, '2024-05-31 14:52:07', 15, 0, '', 'Cash', 'adham wael', 0),
(239, 'Dermatologist', 18, 139, 'mostafa elsayed', 23463456, 10, '2024-05-31', '16:51:19', '2024-05-31 14:51:39', 1, 1, '2024-05-31 14:52:07', 15, 0, '', 'Cash', 'adham wael', 0),
(240, 'General Surgery', 11, 140, 'kaream mohamed', 241234234, 455, '2024-05-31', '16:52:15', '2024-05-31 14:52:38', 1, 1, '2024-05-31 14:53:21', 16, 0, '', 'Cash', 'mazen osama', 0),
(241, 'obstetrician-gynecologist', 34, 141, 'shaimaa abdallah', 90928968, 700, '2024-06-07', '16:52:38', '2024-05-31 14:53:08', 1, 1, '2024-06-07 13:28:56', 13, 332, '', 'Cash', 'zeyad Yaser', 0),
(244, 'Cardiology', 15, 124, 'Zeyad Yasser', 0, 15, '2024-06-02', '23:05:02', '2024-05-31 21:02:38', 2, 2, '2024-06-06 00:32:26', 0, NULL, NULL, NULL, 'Patient', NULL),
(251, 'urologist', 38, 142, 'ECHO ', 0, 500, '2024-06-02', '18:06:54', '2024-06-02 16:54:37', 1, 1, '2024-06-01 22:02:22', 0, NULL, NULL, NULL, 'Patient', NULL),
(253, 'plastic surgery', 16, 124, 'Zeyad Yasser', 0, 25, '2024-06-02', '00:06:26', '2024-06-01 22:26:44', 2, 2, '2024-06-06 00:32:26', 0, NULL, NULL, NULL, 'Patient', NULL),
(254, 'Cardiology', 15, 144, 'vbn hgf', 456346, 15, '2024-06-07', '00:45:53', '2024-06-01 22:46:25', 1, 1, '2024-06-07 13:28:51', 13, 54, '', 'Cash', 'zeyad Yaser', 1),
(255, 'Oncologist', 39, 124, 'Zeyad Yasser', 0, 650, '2024-06-06', '00:06:49', '2024-06-01 22:49:31', 2, 2, '2024-06-06 13:04:20', 0, NULL, NULL, NULL, 'Patient', NULL),
(271, 'Cardiology', 15, 124, 'Zeyad Yasser', 0, 15, '2024-06-06', '16:06:13', '2024-06-06 14:13:41', 2, 2, '2024-06-07 00:11:16', 0, NULL, NULL, NULL, 'Patient', NULL),
(272, 'General Surgery', 14, 145, 'zxcv', 0, 10, '2024-06-06', '16:06:15', '2024-06-06 14:15:15', 1, 1, NULL, 0, NULL, NULL, NULL, 'Patient', NULL),
(273, 'orthopedics ', 13, 124, 'Zeyad Yasser', 0, 25, '2024-06-07', '01:06:52', '2024-06-06 23:52:41', 2, 2, '2024-06-07 13:28:47', 13, NULL, NULL, NULL, 'zeyad Yaser', NULL),
(274, 'orthopedics ', 13, 124, 'Mohamed Nageub', 0, 25, '2024-06-06', '02:06:04', '2024-06-07 00:04:27', 2, 2, '2024-06-07 00:34:21', 0, NULL, NULL, NULL, 'Patient', NULL),
(275, 'General Surgery', 11, 146, 'mohamed abdelaziem', 0, 455, '2024-06-06', '02:06:17', '2024-06-07 00:17:21', 2, 2, '2024-06-07 00:35:38', 0, NULL, NULL, NULL, 'Patient', NULL),
(279, 'Cardiology', 15, 148, 'Mohamed Abdelazim', 1002802638, 15, '2024-06-07', '13:16:07', '2024-06-07 11:16:34', 2, 2, '2024-06-07 13:28:42', 13, 0, '', 'Cash', 'zeyad Yaser', 1),
(280, 'Cardiology', 15, 124, 'Zeyad Yasser', 0, 15, '2024-06-07', '14:06:36', '2024-06-07 12:36:33', 2, 2, '2024-06-08 01:55:50', 13, NULL, NULL, NULL, 'zeyad Yaser', NULL),
(281, 'Cardiology', 15, 124, 'Zeyad Yasser', 0, 15, '2024-06-08', '02:06:31', '2024-06-08 00:31:52', 2, 2, '2024-06-08 01:55:50', 0, NULL, NULL, NULL, 'Patient', NULL),
(282, 'General Surgery', 11, 124, 'Zeyad Yasser', 0, 455, '2024-06-08', '02:06:35', '2024-06-08 00:35:25', 2, 2, '2024-06-08 01:55:50', 0, NULL, NULL, NULL, 'Patient', NULL),
(283, 'Cardiology', 15, 150, 'Zeyad YAsser ', 0, 15, '2024-06-08', '02:06:41', '2024-06-08 00:41:31', 2, 2, '2024-06-08 00:44:48', 0, NULL, NULL, NULL, 'Patient', NULL),
(284, 'General Surgery', 11, 151, 'Zeyad Abdallah', 0, 455, '2024-06-08', '02:06:56', '2024-06-08 00:56:38', 2, 2, '2024-06-08 00:57:58', 0, NULL, NULL, NULL, 'Patient', NULL),
(285, 'Cardiology', 15, 151, 'Zeyad Abdallah', 0, 15, '2024-06-08', '02:06:56', '2024-06-08 00:56:46', 2, 2, '2024-06-08 00:57:58', 0, NULL, NULL, NULL, 'Patient', NULL),
(286, 'plastic surgery', 16, 0, 'Adel Saed', 2147483647, 25, '2024-06-08', '02:58:40', '2024-06-08 00:59:10', 1, 1, NULL, 9, 25, '', 'Cash', 'amna mohamed', 1),
(287, 'obstetrician-gynecologist', 34, 152, 'hossam mohamed', 1221121212, 700, '2024-06-08', '03:02:47', '2024-06-08 01:03:06', 1, 1, '2024-06-08 01:03:06', 9, 0, '', 'Cash', 'amna mohamed', 1),
(288, 'Psychologist', 35, 148, 'Mohamed Abdelazim', 2147483647, 350, '2024-06-08', '04:04:47', '2024-06-08 02:05:05', 1, 1, '2024-06-08 16:58:34', 9, 350, '', 'Cash', 'amna mohamed', 1),
(289, 'Psychologist', 35, 153, 'Zeyad2w', 893853, 350, '2024-06-08', '04:05:14', '2024-06-08 02:05:23', 1, 1, '2024-06-08 02:05:23', 9, 0, '', 'Cash', 'amna mohamed', 1),
(291, 'General Surgery', 14, 151, 'Zeyad Abdallah', 0, 10, '2024-06-08', '18:06:53', '2024-06-08 16:53:50', 0, 1, '2024-06-08 16:53:56', 0, NULL, NULL, NULL, 'Patient', NULL),
(292, 'Oncologist', 39, 148, 'Mohamed Abdelazim', 10215, 650, '2024-06-08', '18:58:16', '2024-06-08 16:58:34', 1, 1, '2024-06-08 16:58:34', 9, 0, '', 'Cash', 'amna mohamed', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `specilization` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `doctorName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `docFees` varchar(255) DEFAULT NULL,
  `docEmail` varchar(255) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `role` varchar(150) NOT NULL,
  `statue` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `specilization`, `doctorName`, `docFees`, `docEmail`, `password`, `creationDate`, `updationDate`, `role`, `statue`) VALUES
(11, 'General Surgery', 'DR / Hamada Saad', '455', 'hamadsa@z.z', '123456', '2021-08-30 13:12:34', '2024-06-08 02:03:08', 'Doctor', 0),
(13, 'orthopedics', 'DR / Nabila Laz', '25', 'nabilal@z.z', '123456', '2021-09-04 22:17:30', '2024-06-08 00:28:15', 'Doctor', 0),
(14, 'General Surgery', 'DR / Mostafa Hassan', '10', 'mostafah@z.z', '123456', '2021-09-04 22:19:00', '2024-05-30 19:58:24', 'Doctor', 1),
(15, 'Cardiology', 'DR / Ahmed Fathy', '15', 'drahmed@z.z', '123456', '2021-09-04 22:21:09', '2024-06-08 16:57:19', 'Doctor', 0),
(16, 'plastic surgery', 'DR / Ibrahem Ramy', '25', 'ibrahemr@z.z', '123456', '2021-09-04 22:22:23', NULL, 'Doctor', 0),
(17, 'Oncologist', 'DR / Bassem Hassan', '25', 'bassemh@z.z', '123456', '2021-09-04 22:25:18', NULL, 'Doctor', 1),
(18, 'Dermatologist', 'DR / Motaz Elsafy', '10', 'motazs@z.z', '123456', '2021-09-04 22:26:16', NULL, 'Doctor', 0),
(19, 'Hematologist', 'DR / Hoda Yousef', '20', 'hoday@z.z', '123456', '2021-09-04 22:27:20', NULL, 'Doctor\r\n', 0),
(20, 'Radiologist', 'DR / Ahmed Farouk', '350', 'ahmedf@z.z', '123456', '2021-09-04 22:28:26', '2024-05-30 20:00:28', 'Doctor', 0),
(34, 'obstetrician-gynecologist', 'DR / Ahmed Okasha', '700', 'ahmedo@z.z', '123456', '2024-05-23 18:05:58', NULL, 'Doctor', 1),
(35, 'Psychologist', 'DR / Amnaa Mohamed ', '350', 'amnaam@z.z', '123456', '2024-05-30 19:28:19', NULL, 'Doctor', NULL),
(38, 'urologist', 'DR / Salma Anwar', '500', 'salaman@z.z', '123456', '2024-05-30 19:36:07', NULL, 'Doctor', NULL),
(39, 'Oncologist', 'DR / Ahmed Hassan', '650', 'ahmedhas@z.z', '123456', '2024-05-30 19:36:58', NULL, 'Doctor', NULL),
(40, 'Dentist', 'DR / Mostafa Lamlom', '600', 'mostafal@z.z', '123456', '2024-05-30 20:29:45', NULL, 'Doctor', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctorslog`
--

CREATE TABLE `doctorslog` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `userip` binary(16) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT current_timestamp(),
  `logout` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctorslog`
--

INSERT INTO `doctorslog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(20, NULL, 'ali@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:29:05', NULL, 0),
(21, NULL, 'ali@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:29:11', NULL, 0),
(22, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:29:57', '30-08-2021 07:03:56 PM', 1),
(23, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:37:07', NULL, 1),
(24, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:09:55', '31-08-2021 05:41:15 PM', 1),
(25, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:16:33', '31-08-2021 05:49:02 PM', 1),
(26, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 18:00:05', NULL, 1),
(27, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 10:40:28', NULL, 1),
(28, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 13:45:00', '01-09-2021 07:25:54 PM', 1),
(29, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 13:57:24', NULL, 1),
(30, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 14:03:47', '01-09-2021 07:38:39 PM', 1),
(31, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 14:11:52', '01-09-2021 07:47:13 PM', 1),
(32, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-02 11:43:10', NULL, 1),
(33, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-02 18:21:56', NULL, 1),
(34, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-04 12:15:51', '04-09-2021 05:50:13 PM', 1),
(35, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-05 09:53:18', NULL, 1),
(36, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-20 13:02:57', NULL, 1),
(37, NULL, 'zeyady2233@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 19:07:24', NULL, 0),
(38, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 19:07:39', NULL, 1),
(39, NULL, 'zeyady2233@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 19:24:23', NULL, 0),
(40, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 19:24:27', NULL, 1),
(41, 17, 'ali6@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 19:59:45', NULL, 1),
(42, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 21:21:18', NULL, 0),
(43, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 21:21:39', NULL, 0),
(44, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-21 21:28:01', NULL, 0),
(45, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:44:29', NULL, 0),
(46, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:44:32', NULL, 0),
(47, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:44:37', NULL, 0),
(48, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:44:48', NULL, 0),
(49, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:44:58', NULL, 0),
(50, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:45:21', NULL, 0),
(51, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:45:32', NULL, 0),
(52, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:45:34', NULL, 0),
(53, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:45:35', NULL, 0),
(54, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:48:25', NULL, 0),
(55, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-22 16:49:33', NULL, 0),
(56, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:17:01', NULL, 0),
(57, NULL, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:18:10', NULL, 0),
(58, NULL, 'admin', 0x3a3a3100000000000000000000000000, '2024-05-24 14:18:15', NULL, 0),
(59, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:19:48', NULL, 1),
(60, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:31:41', NULL, 1),
(61, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:32:37', NULL, 1),
(62, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-24 14:33:11', NULL, 1),
(63, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-25 10:39:58', NULL, 1),
(64, 11, 'ali1@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-25 22:17:32', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctorspecilization`
--

CREATE TABLE `doctorspecilization` (
  `id` int(11) NOT NULL,
  `specilization` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctorspecilization`
--

INSERT INTO `doctorspecilization` (`id`, `specilization`, `creationDate`, `updationDate`) VALUES
(17, 'orthopedics ', '2021-09-04 12:21:30', NULL),
(18, 'General Surgery', '2021-09-04 12:21:47', NULL),
(19, 'Cardiology', '2021-09-04 12:22:00', NULL),
(20, 'plastic surgery', '2021-09-04 12:22:15', NULL),
(23, 'Oncologist', '2021-09-04 12:22:50', NULL),
(24, 'Dermatologist', '2021-09-04 12:23:19', NULL),
(25, 'Hematologist', '2021-09-04 12:23:28', NULL),
(26, 'urologist', '2021-09-04 12:23:41', NULL),
(27, 'Gastroenterologist', '2021-09-04 12:23:57', NULL),
(28, 'Pediatric specialist', '2021-09-04 22:10:53', '2024-05-29 21:33:46'),
(29, 'Psychologist', '2021-09-04 22:11:28', NULL),
(31, 'obstetrician-gynecologist', '2021-09-04 22:12:04', NULL),
(35, 'Radiologist', '2024-05-30 20:15:04', NULL),
(36, 'Ophthalmologist', '2024-05-30 20:17:08', NULL),
(38, 'Dentist', '2024-05-30 20:17:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employ`
--

CREATE TABLE `employ` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` date DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `employ_statue` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employ`
--

INSERT INTO `employ` (`id`, `username`, `email`, `password`, `regdate`, `updationDate`, `role`, `employ_statue`) VALUES
(1, 'Zeyad Yaser', 'zeyad@z.com', '123456', '2024-05-19 11:32:15', '2024-06-09', 'System Admin', 1),
(7, 'Admin', 'admin@e.e', '123456', '2024-05-30 18:59:50', '2024-06-08', 'Admin', 0),
(9, 'amna mohamed', 'user1@z.z', '123456', '2024-05-31 13:37:32', '2024-06-08', 'User', 0),
(10, 'abdelrahman gamal', 'user2@z.z', '123456', '2024-05-31 13:37:56', '2024-06-02', 'User', 0),
(11, 'almoatasem ali', 'user3@z.z', '123456', '2024-05-31 13:38:17', '2024-05-31', 'User', 1),
(12, 'malak yahya', 'user4@z.z', '123456', '2024-05-31 13:38:51', '2024-05-31', 'User', 1),
(13, 'zeyad Yaser', 'user5@z.z', '123456', '2024-05-31 13:41:49', '2024-05-31', 'User', 1),
(14, 'Amr Abdelaziz', 'user6@z.z', '123456', '2024-05-31 13:42:07', '2024-05-31', 'User', 1),
(15, 'adham wael', 'user7@z.z', '123456', '2024-05-31 13:42:49', '2024-05-31', 'User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactus`
--

CREATE TABLE `tblcontactus` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contactno` bigint(12) DEFAULT NULL,
  `message` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `AdminRemark` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `LastupdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `IsRead` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactus`
--

INSERT INTO `tblcontactus` (`id`, `fullname`, `email`, `contactno`, `message`, `PostingDate`, `AdminRemark`, `LastupdationDate`, `IsRead`) VALUES
(6, 'kdwdwadwa', 'teds@gmail.com', 4521754874, ' 425425', '2021-09-04 11:07:45', 'تم', '2021-09-04 11:16:38', 1),
(7, 'ahmed', 'teds@gmail.com', 4521754874, ' صيششششششششششششش', '2021-09-04 11:08:39', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblmedicalhistory`
--

CREATE TABLE `tblmedicalhistory` (
  `ID` int(10) NOT NULL,
  `UserID` int(10) DEFAULT NULL,
  `BloodPressure` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `BloodSugar` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `treatment` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Report` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CreationDate` date DEFAULT NULL,
  `apid` int(150) NOT NULL,
  `Scan` longtext NOT NULL DEFAULT 'Does not need'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblmedicalhistory`
--

INSERT INTO `tblmedicalhistory` (`ID`, `UserID`, `BloodPressure`, `BloodSugar`, `treatment`, `Report`, `CreationDate`, `apid`, `Scan`) VALUES
(53, 124, NULL, '', 'PlanNon-Surgical Management:Physical Therapy: Referral to a physical therapist for strengthening exe', 'ExaminationInspection: Swelling and mild erythema noted around the right knee joint.Palpation: Tenderness over the medial joint line.Range of Motion: Limited flexion and extension due to pain.Special Tests: Positive McMurray test indicating possible meniscal tear.Diagnostic', NULL, 274, ''),
(55, 146, NULL, '', ' - *Intravenous Fluids:* Normal saline to maintain hydration.    - *Antibiotics:* IV administration ', '\r\n*Complete Blood Count (CBC):* Elevated white blood cell count (WBC) of 15,000/mm³ indicating infection.\r\n- *Abdominal Ultrasound:* Suggestive of acute appendicitis with an enlarged, non-compressible appendix.\r\n- *CT Scan (Abdomen and Pelvis):* Confirmed diagnosis of acute appendicitis with signs of inflammation.\r\n\r\n### Diagnosis\r\n- Acute Appendicitis', NULL, 275, ''),
(56, 147, NULL, '', '1. *Medical Management:*    - *Antiplatelet Therapy:* Aspirin 81 mg daily.    - *Beta Blocker:* Meto', 'Electrocardiogram (ECG):* Showed signs of ischemia with ST segment depression in the anterior leads.\r\n- *Echocardiogram:* Normal left ventricular function with no significant valvular abnormalities.\r\n- *Stress Test:* Positive for inducible ischemia with chest pain and ECG changes during exercise.\r\n- *Coronary Angiography:* Revealed a 70% stenosis in the left anterior descending (LAD) artery.\r\n\r\n### Diagnosis\r\n- Coronary Artery Disease (CAD) with significant LAD stenosis.', NULL, 277, ''),
(57, 148, NULL, '', '### Treatment Plan\r\n1. *Medical Management:*\r\n   - *Antiplatelet Therapy:* Aspirin 81 mg daily.\r\n   ', '### Physical Examination\r\n- *General Appearance:* Patient appears well-nourished and in no acute distress.\r\n- *Vital Signs:* \r\n  - Temperature: 36.8°C (98.2°F)\r\n  - Heart Rate: 85 bpm\r\n  - Blood Pressure: 130/80 mmHg\r\n  - Respiratory Rate: 18 breaths per minute\r\n- *Cardiovascular Examination:*\r\n  - Inspection: No visible abnormalities.\r\n  - Palpation: Normal apical impulse.\r\n  - Auscultation: Regular heart rhythm, no murmurs, gallops, or rubs.\r\n  - Peripheral Pulses: All palpable and symmetrical.\r\n\r\n### Diagnosis\r\n- Coronary Artery Disease (CAD) with significant LAD stenosis.', NULL, 279, '### Diagnostic Tests\r\n- *Electrocardiogram (ECG):* Showed signs of ischemia with ST segment depression in the anterior leads.\r\n- *Echocardiogram:* Normal left ventricular function with no significant valvular abnormalities.\r\n- *Stress Test:* Positive for inducible ischemia with chest pain and ECG changes during exercise.\r\n- *Coronary Angiography:* Revealed a 70% stenosis in the left anterior descending (LAD) artery.'),
(58, 150, NULL, '', 'treatment', 'zzz', NULL, 283, 'scan3'),
(59, 151, NULL, '', 'TraetMent', 'Report', NULL, 285, 'Scan'),
(60, 124, NULL, '', 'treatment', 'report', NULL, 282, 'scan'),
(61, 151, NULL, '', 'afzsdv', 'zsdfvdv', NULL, 284, 'zxdvzxfv'),
(62, 151, NULL, '', 'Treatment', 'report', NULL, 284, 'scan');

-- --------------------------------------------------------

--
-- Table structure for table `tblpatient`
--

CREATE TABLE `tblpatient` (
  `ID` int(10) NOT NULL,
  `Docid` int(10) DEFAULT NULL,
  `PatientName` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PatientContno` bigint(10) DEFAULT NULL,
  `PatientEmail` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Patientpassword` varchar(50) NOT NULL,
  `PatientGender` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PatientAdd` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PatientAge` int(10) DEFAULT NULL,
  `PatientMedhis` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpatient`
--

INSERT INTO `tblpatient` (`ID`, `Docid`, `PatientName`, `PatientContno`, `PatientEmail`, `Patientpassword`, `PatientGender`, `PatientAdd`, `PatientAge`, `PatientMedhis`, `CreationDate`, `UpdationDate`) VALUES
(10, NULL, 'Zeyad', 5210, 'Zeyad@z.z', '123456', 'male', 'hgfh', 20, 'ufyg', '2024-05-19 16:50:27', '2024-05-19 16:50:27'),
(11, 11, 'z', 408, 'awd@sadf.sdf', '', 'ذكر', 'adsasd', 20, 'dasd', '2024-05-21 21:18:54', '2024-05-21 21:18:54');

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `userip` binary(16) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT current_timestamp(),
  `logout` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(24, NULL, ' admin', 0x3a3a3100000000000000000000000000, '2021-08-30 12:22:38', NULL, 0),
(25, NULL, 'admin', 0x3a3a3100000000000000000000000000, '2021-08-30 12:22:57', NULL, 0),
(26, NULL, ' test@demo.com', 0x3a3a3100000000000000000000000000, '2021-08-30 12:24:33', NULL, 0),
(27, 2, 'test@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 12:25:39', '30-08-2021 05:56:55 PM', 1),
(28, 8, 'admin22@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:15:32', '30-08-2021 06:57:19 PM', 1),
(29, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-30 13:28:16', '30-08-2021 06:58:54 PM', 1),
(30, NULL, 'test@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 11:48:12', NULL, 0),
(31, NULL, 'ali@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 11:49:50', NULL, 0),
(32, NULL, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 11:51:21', NULL, 0),
(33, 10, 'admin5@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 11:52:10', '31-08-2021 05:39:28 PM', 1),
(34, NULL, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:11:24', NULL, 0),
(35, NULL, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:11:46', NULL, 0),
(36, NULL, 'admin20@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:13:24', NULL, 0),
(37, NULL, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:14:49', NULL, 0),
(38, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:15:31', '31-08-2021 05:46:21 PM', 1),
(39, NULL, 'admin12@admin.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:19:18', NULL, 0),
(40, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 12:20:00', '31-08-2021 06:12:10 PM', 1),
(41, NULL, 'admin12@admin.com', 0x3a3a3100000000000000000000000000, '2021-08-31 15:28:56', NULL, 0),
(42, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 15:29:29', '31-08-2021 09:07:30 PM', 1),
(43, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 15:51:51', NULL, 1),
(44, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-08-31 18:01:04', NULL, 1),
(45, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 10:48:08', NULL, 1),
(46, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-01 13:56:49', NULL, 1),
(47, 9, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-04 11:52:41', NULL, 1),
(48, 11, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2021-09-05 09:54:41', NULL, 1),
(49, 11, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-18 19:49:05', NULL, 1),
(50, 11, 'admin12@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-18 20:22:03', NULL, 1),
(51, NULL, 'h@h.h', 0x3a3a3100000000000000000000000000, '2024-05-20 14:06:07', NULL, 0),
(52, NULL, 'z@z.z', 0x3a3a3100000000000000000000000000, '2024-05-20 14:07:22', NULL, 0),
(53, NULL, 'z@z.z', 0x3a3a3100000000000000000000000000, '2024-05-20 14:07:30', NULL, 0),
(54, 0, '', 0x00000000000000000000000000000000, '2024-05-20 14:56:45', NULL, 0),
(55, NULL, 'Zeyad@z.z', 0x3a3a3100000000000000000000000000, '2024-05-25 10:39:40', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `fullName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `regDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `Docid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `PatientContno` int(30) NOT NULL,
  `p_age` int(30) NOT NULL,
  `PatientMedhis` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `fullName`, `gender`, `email`, `password`, `regDate`, `updationDate`, `Docid`, `PatientContno`, `p_age`, `PatientMedhis`) VALUES
(124, 'Zeyad Yasser', 'Male', 'zeyad@z.z', 'Zeyad-1', '2024-05-30 20:21:47', '2024-05-30 21:44:40', NULL, 1024474059, 20, ''),
(125, 'Youssef Mohamed', NULL, NULL, NULL, '2024-05-30 20:25:48', NULL, NULL, 1112806405, 0, ''),
(126, 'Zeyad ', NULL, NULL, NULL, '2024-05-31 14:18:56', NULL, NULL, 123451, 0, ''),
(127, 'moatasem ali', NULL, NULL, NULL, '2024-05-31 14:19:33', NULL, NULL, 323423423, 0, ''),
(128, 'ahmed atef', NULL, NULL, NULL, '2024-05-31 14:22:14', NULL, NULL, 213123, 0, ''),
(129, 'amr abdelaziz', NULL, NULL, NULL, '2024-05-31 14:22:42', NULL, NULL, 435346, 0, ''),
(130, 'waled hassan', NULL, NULL, NULL, '2024-05-31 14:30:57', NULL, NULL, 55857524, 0, ''),
(133, 'ahmed atef', NULL, NULL, NULL, '2024-05-31 14:39:33', NULL, NULL, 213123, 0, ''),
(134, 'ali ewias', NULL, NULL, NULL, '2024-05-31 14:39:33', NULL, NULL, 213123, 0, ''),
(135, 'ali hosam', NULL, NULL, NULL, '2024-05-31 14:46:53', NULL, NULL, 763452634, 0, ''),
(136, 'amr mokhtar', NULL, NULL, NULL, '2024-05-31 14:47:14', NULL, NULL, 253245345, 0, ''),
(137, 'donia samer', NULL, NULL, NULL, '2024-05-31 14:50:16', NULL, NULL, 74845767, 0, ''),
(138, 'amr anwar', NULL, NULL, NULL, '2024-05-31 14:51:19', NULL, NULL, 7853253, 0, ''),
(139, 'mostafa elsayed', NULL, NULL, NULL, '2024-05-31 14:51:39', NULL, NULL, 23463456, 0, ''),
(140, 'kaream mohamed', NULL, NULL, NULL, '2024-05-31 14:52:38', NULL, NULL, 241234234, 0, ''),
(141, 'shaimaa abdallah', NULL, NULL, NULL, '2024-05-31 14:53:08', NULL, NULL, 90928968, 0, ''),
(142, 'ECHO ', NULL, 'echo@z.z', 'Zeyad-1', '2024-05-31 18:17:38', NULL, NULL, 2147483647, 0, ''),
(143, 'saif', NULL, 'sa@s.s', 'Zeyad-1', '2024-06-01 19:51:37', NULL, NULL, 2147483647, 0, ''),
(144, 'vbn hgf', NULL, NULL, NULL, '2024-06-01 22:46:25', NULL, NULL, 456346, 0, ''),
(145, 'zxcv', NULL, 'zxc@zxc.zxc', 'Zeyad-1', '2024-06-06 13:46:38', NULL, NULL, 2147483647, 0, ''),
(146, 'amnaa salah', NULL, 'amnaa@a.a', 'Zeyad-1', '2024-06-07 00:16:48', NULL, NULL, 2147483647, 0, ''),
(147, 'almoatasem ali', NULL, NULL, NULL, '2024-06-07 00:25:12', NULL, NULL, 1221121212, 0, ''),
(148, 'Mohamed Abdelazim', NULL, NULL, NULL, '2024-06-07 11:15:26', NULL, NULL, 1002802638, 0, ''),
(149, 'Mohamed Abdelazim', NULL, NULL, NULL, '2024-06-07 11:16:34', NULL, NULL, 1002802638, 0, ''),
(151, 'Zeyad Abdallah', 'Male', 'zeyad4a@gmail.com', 'Zeyad-1', '2024-06-08 00:56:12', '2024-06-08 16:02:08', NULL, 1024474059, 21, ''),
(152, 'hossam mohamed', NULL, NULL, NULL, '2024-06-08 01:03:06', NULL, NULL, 1221121212, 0, ''),
(153, 'Zeyad2w', NULL, NULL, NULL, '2024-06-08 02:05:23', NULL, NULL, 893853, 0, ''),
(154, 'Mohamed Abdelazim', NULL, NULL, NULL, '2024-06-08 16:58:34', NULL, NULL, 10215, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`apid`),
  ADD KEY `employId` (`employId`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `docEmail` (`docEmail`),
  ADD UNIQUE KEY `docEmail_2` (`docEmail`);

--
-- Indexes for table `doctorslog`
--
ALTER TABLE `doctorslog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employ`
--
ALTER TABLE `employ`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `tblcontactus`
--
ALTER TABLE `tblcontactus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpatient`
--
ALTER TABLE `tblpatient`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `apid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `doctorslog`
--
ALTER TABLE `doctorslog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `employ`
--
ALTER TABLE `employ`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tblcontactus`
--
ALTER TABLE `tblcontactus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `tblpatient`
--
ALTER TABLE `tblpatient`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
