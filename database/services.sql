-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 01:00 PM
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
-- Database: `glaxit_chatbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `min_price` decimal(10,2) NOT NULL,
  `max_price` decimal(10,2) NOT NULL,
  `min_timeline` int(11) NOT NULL,
  `max_timeline` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category`, `service_name`, `description`, `min_price`, `max_price`, `min_timeline`, `max_timeline`, `created_at`, `updated_at`) VALUES
(1, 'Custom Software Development', 'Enterprise Grade Applications', 'We build robust, scalable enterprise software solutions tailored to your business needs, including CRM, ERP, and custom business applications that handle complex workflows and large datasets', 10000.00, 25000.00, 28, 56, '2025-10-13 02:53:00', '2025-10-16 08:56:18'),
(3, 'Custom Software Development', 'System Integration', 'We connect your existing software systems, APIs, and platforms to work together seamlessly. Our integration solutions ensure data flows smoothly between different applications and databases.', 100000.00, 250000.00, 180, 360, '2025-10-13 02:53:00', '2025-10-16 08:57:00'),
(4, 'Custom Software Development', 'Legacy Modernization', 'We transform your outdated systems into modern, efficient applications using latest technologies. Our modernization services include code refactoring, platform migration, and feature enhancements.', 50000.00, 200000.00, 90, 360, '2025-10-13 02:53:00', '2025-10-16 08:57:18'),
(7, 'Mobile App Development', 'iOS & Android Apps', 'We create native mobile applications for both iOS and Android platforms with optimized performance, smooth user experience, and platform-specific features.', 60000.00, 120000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 08:57:36'),
(10, 'AI Solutions', 'Natural Language Processing', 'We implement NLP solutions for text analysis, sentiment analysis, chatbots, language translation, and content processing to understand and generate human language.', 20000.00, 40000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 08:59:38'),
(15, 'Data & Analytics', 'Machine Learning', 'We implement machine learning algorithms for pattern recognition, classification, clustering, and predictive modeling to extract valuable insights from your data.', 50000.00, 150000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 09:00:33'),
(17, 'Cloud Solutions', 'AWS & Azure', 'We provide comprehensive cloud solutions on Amazon Web Services and Microsoft Azure, including infrastructure setup, migration, and optimization for scalability and cost-efficiency.', 20000.00, 40000.00, 60, 120, '2025-10-13 02:53:00', '2025-10-16 09:02:37'),
(18, 'Cloud Solutions', 'DevOps & CI/CD', 'We implement DevOps practices and Continuous Integration/Continuous Deployment pipelines to automate software development, testing, and deployment processes.', 30000.00, 40000.00, 90, 180, '2025-10-13 02:53:00', '2025-10-16 09:03:17'),
(19, 'Cloud Solutions', 'Microservices Architecture', 'We design and develop microservices-based applications that are scalable, maintainable, and resilient, breaking down monoliths into independent, deployable services.', 50000.00, 120000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 09:03:40'),
(21, 'Digital Transformation', 'Process Automation', 'We automate your end-to-end business processes using digital technologies to improve efficiency, reduce costs, and enhance customer experiences.', 15000.00, 40000.00, 60, 120, '2025-10-13 02:53:00', '2025-10-16 09:04:06'),
(22, 'Digital Transformation', 'Digital Strategy', 'We develop comprehensive digital transformation roadmaps that align technology investments with business goals, ensuring measurable ROI and competitive advantage.', 30000.00, 40000.00, 90, 180, '2025-10-13 02:53:00', '2025-10-16 09:04:24'),
(72, 'Custom Software Development', 'Workflow Automation', 'We automate your business processes to increase efficiency, reduce manual errors, and save time. Our solutions streamline repetitive tasks, approvals, and data processing across departments.', 30000.00, 45000.00, 90, 180, '2025-10-13 02:36:07', '2025-10-17 10:59:10'),
(76, 'Mobile App Development', 'React Native', 'We develop cross-platform mobile apps using React Native that work on both iOS and Android with native-like performance and faster development time.', 20000.00, 30000.00, 90, 180, '2025-10-13 02:40:05', '2025-10-16 08:57:55'),
(79, 'AI Solutions', 'Custom AI Models', 'We develop custom artificial intelligence models tailored to your specific business needs, including predictive analytics, recommendation engines, and intelligent automation solutions.', 20000.00, 40000.00, 60, 120, '2025-10-13 02:42:52', '2025-10-16 08:59:20'),
(81, 'AI Solutions', 'Predictive Analytics', 'We build predictive models that analyze historical data to forecast trends, customer behavior, market changes, and business outcomes for better decision-making.', 80000.00, 200000.00, 180, 360, '2025-10-13 02:44:40', '2025-10-16 09:00:07'),
(83, 'Data & Analytics', 'Data Warehousing', 'We design and implement centralized data warehouses that consolidate information from multiple sources for comprehensive business intelligence and reporting.', 15000.00, 40000.00, 60, 120, '2025-10-13 02:46:34', '2025-10-16 09:01:09'),
(84, 'Data & Analytics', 'Business Intelligence', 'We develop BI solutions with interactive dashboards, data visualization, and reporting tools that transform raw data into actionable business insights.', 30000.00, 40000.00, 90, 180, '2025-10-13 02:48:31', '2025-10-16 09:00:51'),
(86, 'Data & Analytics', 'OCR Solutions', 'We develop Optical Character Recognition systems that automatically extract text from images, documents, and scanned files, converting them into editable and searchable digital data.', 60000.00, 200000.00, 120, 240, '2025-10-13 02:50:15', '2025-10-16 09:01:53'),
(87, 'Mobile App Development', 'Flutter', 'We build beautiful, high-performance cross-platform applications using Flutter that deliver consistent user experience across iOS, Android, and web platforms.', 20000.00, 40000.00, 90, 180, '2025-10-13 02:40:05', '2025-10-16 08:58:14'),
(88, 'AI Solutions', 'Computer Vision', 'We create computer vision applications for image recognition, object detection, facial recognition, and visual data analysis using advanced AI algorithms.', 50000.00, 150000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 08:59:52'),
(89, 'Data & Analytics', 'Real-time Analytics', 'We build real-time data processing systems that provide instant insights, live dashboards, and immediate analytics for time-sensitive business decisions.', 50000.00, 150000.00, 120, 240, '2025-10-13 02:53:00', '2025-10-16 09:01:34'),
(90, 'Data & Analytics', 'RPA Automation', 'We implement Robotic Process Automation to automate rule-based digital tasks, reducing manual work and increasing operational efficiency across your organization.', 60000.00, 200000.00, 120, 240, '2025-10-13 02:50:15', '2025-10-16 09:02:16'),
(91, 'Cloud Solutions', 'Cloud Migration', 'We seamlessly migrate your applications, data, and infrastructure to cloud platforms with minimal downtime and maximum security, ensuring smooth transition and optimal performance.', 20000.00, 40000.00, 60, 120, '2025-10-13 02:53:00', '2025-10-16 09:02:51'),
(92, 'Digital Transformation', 'Change Management', 'We facilitate organizational change during digital transformation, providing training, support, and strategies to ensure smooth adoption of new technologies and processes.', 100000.00, 250000.00, 180, 360, '2025-10-13 02:53:00', '2025-10-16 09:04:47'),
(93, 'Digital Transformation', 'Technology Consulting', 'We provide expert technology advisory services to help you choose the right solutions, plan implementations, and maximize the value of your technology investments.', 30000.00, 40000.00, 90, 180, '2025-10-13 02:53:00', '2025-10-17 10:59:41'),
(94, 'Digital Transformation', 'Implementation Services', 'We manage end-to-end implementation of software solutions, from requirement analysis and planning to deployment, testing, and ongoing support.', 100000.00, 250000.00, 180, 360, '2025-10-13 02:53:00', '2025-10-16 09:05:25'),
(95, 'Digital Transformation', 'Project Management', 'We provide professional project management services using agile methodologies to ensure your projects are delivered on time, within budget, and meeting quality standards.', 100000.00, 250000.00, 180, 360, '2025-10-13 02:53:00', '2025-10-16 09:05:44'),
(96, 'Mobile App Development', 'Mobile UI/UX Design', 'We design intuitive and engaging mobile interfaces with user-centered design principles, creating seamless navigation and delightful user experiences.', 10000.00, 30000.00, 60, 120, '2025-10-16 08:58:55', '2025-10-16 08:58:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_name` (`service_name`),
  ADD KEY `idx_price_range` (`min_price`,`max_price`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
