-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 01:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(3, 'moeed', '$2y$10$nIegQtkfjDsNyMb3LyczoeQE8uCeDqypoGyAMxce/HILMy.PaEZZy', '2025-10-06 18:54:25'),
(4, 'uzair', '$2y$10$WxIGTik7p2L9HnZBwgKcBOVMYVatZpJCI2e0ZcnivxfseejY99NF.', '2025-10-06 18:55:06'),
(5, 'Aimen', '$2y$10$nxsEuSUcSpAVneGfjMjrHOmubk8mC9RJ2hrWS9vbw.n.lUote/AmO', '2025-10-08 10:55:57');

-- --------------------------------------------------------

--
-- Table structure for table `chat_logs`
--

CREATE TABLE `chat_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_logs`
--

INSERT INTO `chat_logs` (`id`, `user_id`, `user_message`, `bot_response`, `timestamp`) VALUES
(1, 1, 'i want to develop  e commerece website', NULL, '2025-10-06 15:57:56'),
(2, 1, 'i want to develop  e commerece website', 'Excellent! I\'d be happy to help you develop an e-commerce website. To provide you with an accurate estimate, I need to gather some details about your project.\n\nCould you please tell me:\n1. What type of products will you be selling?\n2. Approximately how many products do you plan to have in your catalog?\n3. Do you need any special features like inventory management, payment gateways, or shipping integrations?\n4. Will you need any custom functionality beyond standard e-commerce features?\n\nThis information will help me match your project with the right service package and provide you with an accurate budget and timeline estimate.', '2025-10-06 15:58:03'),
(3, 1, 'no', NULL, '2025-10-06 15:58:05'),
(4, 1, 'no', 'Based on your requirement for an e-commerce website, here is the estimate:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-06 15:58:10'),
(5, 1, 'Yes, I agree with the estimate', NULL, '2025-10-06 15:58:12'),
(6, 1, 'Yes, I agree with the estimate', 'ACCEPTED PROPOSAL\n\n**Requirements:**  \n- E-commerce website development\n\n**Budget:** $15,000–$50,000  \n**Timeline:** 8–16 weeks  \n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-06 15:58:19'),
(7, 2, 'i want to develop e comerece website', NULL, '2025-10-06 15:59:43'),
(8, 2, 'i want to develop e comerece website', 'Excellent! I\'m Glaxit\'s AI Sales Assistant, and I\'d be happy to help you with your e-commerce website project.\n\nTo provide you with an accurate estimate, I need to understand your requirements better. Could you please tell me:\n\n1. What type of products will you be selling?\n2. Approximately how many products do you plan to have in your catalog?\n3. Do you need any special features like user accounts, wishlists, inventory management, or payment integrations?\n4. Will you need mobile responsiveness and cross-browser compatibility?\n5. Do you have any specific design preferences or branding requirements?\n\nWould you like to add more requirements?', '2025-10-06 15:59:53'),
(9, 2, 'no more', NULL, '2025-10-06 15:59:57'),
(10, 2, 'no more', 'Based on your requirement for an e-commerce website development, here is the estimate:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-06 16:00:01'),
(11, 2, 'No, I need some changes to the proposal', NULL, '2025-10-06 16:00:09'),
(12, 2, 'No, I need some changes to the proposal', 'Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?', '2025-10-06 16:00:09'),
(13, 2, 'budget issue', NULL, '2025-10-06 16:00:17'),
(14, 2, 'budget issue', 'REJECTED PROPOSAL\n\n**Requirements:**  \n- E-commerce website development\n\n**Estimated Budget:** $15,000–$50,000  \n**Estimated Timeline:** 8–16 weeks  \n\n**Status:** Rejected due to budget concerns  \n\nThank you for showing your interest! Our Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!\n\n❌ **REJECTED PROPOSAL SUMMARY**\n\nOur Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!', '2025-10-06 16:00:26'),
(23, 4, 'i want chatgpt app', NULL, '2025-10-06 18:23:45'),
(24, 4, 'i want chatgpt app', 'Hello! I\'m Glaxit\'s AI Sales Assistant, and I\'m here to help you with your project needs.\n\nYou mentioned you want a ChatGPT-like app. To provide you with an accurate estimate, I need to gather a few more details about your project:\n\n1. **Platform**: Will this be a web app, mobile app (iOS/Android), or desktop app?\n2. **Core Features**: Besides chat functionality, do you need user authentication, payment integration, admin panel, or any other specific features?\n3. **Target Audience**: Is this for personal use, a small business, or an enterprise?\n4. **Customization**: Do you need any unique or advanced AI features beyond standard chat?\n\nPlease provide these details so I can match your project with the right service and pricing.', '2025-10-06 18:23:56'),
(25, 4, 'using Simple Tools', NULL, '2025-10-06 18:24:03'),
(26, 4, 'using Simple Tools', 'Thank you for providing that detail. To ensure I understand your project fully, could you please clarify:\n\n- Is this a desktop application you\'re looking to develop?\n- What specific functionality do you need in this ChatGPT-like app? (e.g., text generation, conversation history, API integration, etc.)\n- Are there any specific platforms or operating systems you need to support?\n\nThis will help me provide you with the most accurate estimate for your project.', '2025-10-06 18:24:09'),
(27, 4, 'no', NULL, '2025-10-06 18:24:11'),
(28, 4, 'no', 'Based on your requirements for a ChatGPT-like app using Simple Tools, this falls under Desktop Apps category.\n\nBudget: $8,000–$20,000, Timeline: 1–2 Months\n\nDo you agree with this estimate?', '2025-10-06 18:24:16'),
(29, 4, 'Yes, I agree with the estimate', NULL, '2025-10-06 18:24:20'),
(30, 4, 'Yes, I agree with the estimate', 'ACCEPTED PROPOSAL\n\nThank you for choosing Glaxit for your project. Below is your accepted proposal based on the requirements we\'ve gathered:\n\n**Gathered Requirements:**\n- i want chatgpt app\n- using Simple Tools\n\n**Budget:** $8,000–$20,000,\n**Timeline:** 1–2 Months\n\nThis proposal has been accepted and stored in our system. Our team will contact you within 24 hours to finalize the details and begin your project.\n\nThank you for your trust in Glaxit! Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-06 18:24:30'),
(31, 6, 'i want to make website for my buisness gym', NULL, '2025-10-07 10:58:16'),
(32, 6, 'i want to make website for my buisness gym', 'Hello! I\'m Glaxit\'s AI Sales Assistant, and I\'m here to help you with your website project for your gym business.\n\nTo provide you with an accurate estimate, I need to gather some details about your project. Let me start by asking a few structured questions:\n\n1. What type of website are you looking for? (e.g., basic informational site, business site with features like class schedules, membership sign-ups, or e-commerce for selling merchandise?)\n\n2. Do you need any specific features, such as online booking, payment processing, member portals, or integration with social media?\n\n3. Will you require content creation, branding, or SEO services as part of this project?\n\nPlease provide as much detail as you can. If you\'re unsure about any aspect, feel free to ask for help!', '2025-10-07 10:58:25'),
(33, 6, 'its a bsuiness site with online registering member', NULL, '2025-10-07 10:58:48'),
(34, 6, 'its a bsuiness site with online registering member', 'Thank you for providing those details about your business website project with online member registration.\n\nBased on your requirements for a business website with online member registration functionality, I would estimate:\n\n**Budget: $8,000–$20,000, Timeline: 6–10 weeks**\n\nWould you like to add more requirements to your project?', '2025-10-07 10:58:53'),
(35, 6, 'yes also add the payment integration', NULL, '2025-10-07 10:59:08'),
(36, 6, 'yes also add the payment integration', 'Thank you for confirming the payment integration requirement. Let me summarize what I have so far:\n\n- Business website for a gym\n- Online member registration system\n- Payment integration\n\nWould you like to add more requirements to your project?', '2025-10-07 10:59:12'),
(37, 6, 'no more', NULL, '2025-10-07 10:59:17'),
(38, 6, 'no more', 'Based on your requirements for a business website with online member registration and payment integration, this falls under our \"Business Sites\" category with e-commerce functionality.\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-07 10:59:21'),
(39, 6, 'Yes, I agree with the estimate', NULL, '2025-10-07 10:59:27'),
(40, 6, 'Yes, I agree with the estimate', '**ACCEPTED PROPOSAL**\n\n**Project Requirements Summary:**  \n- Website for a gym business  \n- Business site with online member registration  \n- Payment integration  \n\n**Budget Estimate:**  \nPrice Range: $15,000–$50,000  \n\n**Timeline Estimate:**  \nTimeline: 8–16 weeks  \n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 10:59:34'),
(41, 6, 'i want to make saas platform website', NULL, '2025-10-07 11:00:17'),
(42, 6, 'i want to make saas platform website', 'Thank you for your interest in Glaxit\'s services! I\'m here to help you with your SaaS platform website project.\n\nTo provide you with an accurate estimate, I need to gather some details about your requirements:\n\n1. What is the primary purpose of your SaaS platform?\n2. What key features do you need (e.g., user authentication, payment processing, dashboard, analytics)?\n3. Do you need mobile app integration or just web-based?\n4. What is your expected user scale (number of users)?\n5. Do you require any third-party integrations (APIs, cloud services, etc.)?\n6. What is your timeline expectation?\n\nPlease provide as much detail as possible so I can match you with the right service package.', '2025-10-07 11:00:26'),
(43, 6, 'user authentication, payment processing, dashboard, analytics also add this', NULL, '2025-10-07 11:01:14'),
(44, 6, 'user authentication, payment processing, dashboard, analytics also add this', 'Thank you for providing those additional requirements! Based on your project description of a SaaS platform website with user authentication, payment processing, dashboard, and analytics, I can see this is a high-value project.\n\nSince SaaS platforms fall into our high-value category and typically exceed our $50,000 threshold for automated estimates, I\'ll need to escalate this to our Sales Team for a customized proposal.\n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your SaaS platform requirements in detail. Goodbye!', '2025-10-07 11:01:21'),
(45, 6, 'ok goodbye', NULL, '2025-10-07 11:01:54'),
(46, 6, 'ok goodbye', 'Thank you for your interest in Glaxit! If you have any project requirements in the future, feel free to reach out. Goodbye!', '2025-10-07 11:01:58'),
(47, 6, 'i want to make mobile app', NULL, '2025-10-07 11:02:34'),
(48, 6, 'i want to make mobile app', 'Hello! I\'m Glaxit\'s AI Sales Assistant, here to help you with your mobile app project.\n\nTo provide you with an accurate estimate, I need to understand your requirements better. Could you tell me:\n\n1. What type of mobile app are you planning? (e.g., business app, social media, e-commerce, game, utility tool, etc.)\n2. Will this be for iOS, Android, or both platforms?\n3. What are the key features you want to include?\n4. Do you need any integrations (APIs, payment systems, etc.)?\n5. What is your target timeline for completion?\n\nFeel free to share as much detail as you can about your vision for the app!', '2025-10-07 11:02:43'),
(49, 6, 'its a business app using iOS olatform', NULL, '2025-10-07 11:02:57'),
(50, 6, 'its a business app using iOS olatform', 'Thank you for providing that information. To help me understand your project better and provide an accurate estimate, could you tell me:\n\n1. What specific business functions will the iOS app perform?\n2. Do you need any backend/database integration?\n3. Will there be any payment processing or e-commerce features?\n4. What is your target audience size?\n5. Do you have any specific design requirements?\n\nWould you like to add more requirements?', '2025-10-07 11:03:04'),
(51, 6, 'add these customer engagement and tracking system', NULL, '2025-10-07 11:03:28'),
(52, 6, 'add these customer engagement and tracking system', 'Thank you for providing that additional requirement! To ensure I understand your project fully, let me confirm what we have so far:\n\n- You want to develop a mobile app\n- It\'s a business app using iOS platform\n- You want to add customer engagement and tracking system capabilities\n\nWould you like to add more requirements to your mobile app project?', '2025-10-07 11:03:33'),
(53, 6, 'no more', NULL, '2025-10-07 11:03:37'),
(54, 6, 'no more', 'Based on your requirements for a business mobile app using iOS platform with customer engagement and tracking system, this falls under our Native Apps category with complex features.\n\nBudget: $40,000–$100,000, Timeline: 4–7 months\n\nDo you agree with this estimate?', '2025-10-07 11:03:41'),
(55, 6, 'No, I need some changes to the proposal', NULL, '2025-10-07 11:03:45'),
(56, 6, 'No, I need some changes to the proposal', 'Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?', '2025-10-07 11:03:45'),
(57, 6, 'budget issue', NULL, '2025-10-07 11:03:53'),
(58, 6, 'budget issue', '**REJECTED PROPOSAL**\n\n**Gathered Requirements:**\n- Mobile app development\n- Business app using iOS platform\n- Customer engagement and tracking system\n\n**Estimated Budget:** $40,000–$100,000  \n**Estimated Timeline:** 4–7 months\n\n**Status:** Rejected due to budget concerns\n\nThank you for showing your interest! Our Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!\n\n❌ **REJECTED PROPOSAL SUMMARY**\n\nOur Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!', '2025-10-07 11:03:58'),
(59, 6, 'i want to make e-commerce website', NULL, '2025-10-07 11:04:28'),
(60, 6, 'i want to make e-commerce website', 'Hello! I\'m Glaxit\'s AI Sales Assistant, and I\'m here to help you with your e-commerce website project.\n\nTo provide you with an accurate estimate, I need to gather some details about your requirements. Based on your initial request, I understand you want an e-commerce website. Could you please provide more specifics, such as:\n\n- Number of products you plan to list\n- Payment gateway requirements (e.g., PayPal, Stripe, etc.)\n- Any special features like user accounts, reviews, or inventory management?\n- Do you need SEO optimization or mobile responsiveness?\n\nWould you like to add more requirements?', '2025-10-07 11:04:37'),
(61, 6, 'product category is kid cloths and there are atleast 50 productas also add the payment gateways', NULL, '2025-10-07 11:05:19'),
(62, 6, 'product category is kid cloths and there are atleast 50 productas also add the payment gateways', 'Thank you for providing these details! I understand you want an e-commerce website for kids\' clothing with at least 50 products and payment gateway integration.\n\nTo give you an accurate estimate, I need to ask a few more questions:\n\n1. Will you need any special features like size/color selectors, wishlists, or customer reviews?\n2. Do you require inventory management and admin dashboard?\n3. Should we include shipping calculator and tax settings?\n4. Will you need mobile-responsive design?\n5. Do you require SEO optimization?\n\nWould you like to add more requirements?', '2025-10-07 11:05:25'),
(63, 6, 'add  mobile-responsive', NULL, '2025-10-07 11:06:14'),
(64, 6, 'add  mobile-responsive', 'Great! I\'ll add \"mobile-responsive\" to your requirements for the e-commerce website with kid\'s clothing and payment gateways.\n\nYour updated requirements are:\n- E-commerce website\n- Product category: kid\'s clothing\n- At least 50 products\n- Payment gateways integration\n- Mobile-responsive design\n\nWould you like to add more requirements?', '2025-10-07 11:06:20'),
(65, 6, 'no more', NULL, '2025-10-07 11:06:32'),
(66, 6, 'no more', 'Based on your requirements for an e-commerce website with kid\'s clothing category, 50+ products, and payment gateway integration:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-07 11:06:36'),
(67, 6, 'No, I need some changes to the proposal', NULL, '2025-10-07 11:06:44'),
(68, 6, 'No, I need some changes to the proposal', 'Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?', '2025-10-07 11:06:44'),
(69, 6, 'update requirement', NULL, '2025-10-07 11:06:52'),
(70, 6, 'update requirement', 'Thank you for clarifying. Please provide the updated or additional requirements for your project, and I\'ll revise the estimate accordingly.', '2025-10-07 11:06:52'),
(71, 6, 'i want to add payment integartiona also', NULL, '2025-10-07 11:07:07'),
(72, 6, 'i want to add payment integartiona also', 'Thank you for providing additional requirements! I\'ve added \"payment integration\" to your project specifications.\n\nBased on all the requirements you\'ve shared:\n- E-commerce website\n- Product category: kids clothing\n- Minimum 50 products\n- Payment gateway integration\n- Payment integration\n\nYour project falls under our E-Commerce service category.\n\nBudget: $15,000–$50,000\nTimeline: 8–16 weeks\n\nWould you like to add more requirements?', '2025-10-07 11:07:16'),
(73, 6, 'yes add website mobile responsiveness', NULL, '2025-10-07 11:07:43'),
(74, 6, 'yes add website mobile responsiveness', 'Thank you for confirming mobile responsiveness for your e-commerce website! \n\nBased on all the requirements you\'ve provided:\n- E-commerce website\n- Product category: kids clothing\n- At least 50 products\n- Payment gateway integration\n- Mobile responsive design\n\nWould you like to add more requirements to your project?', '2025-10-07 11:07:50'),
(75, 6, 'no more', NULL, '2025-10-07 11:07:55'),
(76, 6, 'no more', 'Based on your requirements for an e-commerce website with 50+ kids clothing products, payment gateway integration, and mobile responsiveness:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-07 11:08:01'),
(77, 6, 'Yes, I agree with the estimate', NULL, '2025-10-07 11:08:13'),
(78, 6, 'Yes, I agree with the estimate', '**ACCEPTED PROPOSAL**\n\n**Project Requirements Summary:**  \n- E-commerce website development  \n- Product category: Kids clothing  \n- Minimum 50 products  \n- Payment gateway integration  \n- Mobile responsiveness  \n\n**Budget Estimate:**  \nPrice Range: $15,000–$50,000  \n\n**Timeline Estimate:**  \nTimeline: 8–16 weeks  \n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 11:08:21'),
(79, 6, 'i want to make AAA games', NULL, '2025-10-07 11:08:38'),
(80, 6, 'i want to make AAA games', 'Thank you for your interest in Glaxit\'s game development services! AAA games represent the highest tier of game development with premium quality and production values.\n\nSince AAA game development falls into our high-value category, I\'ll need to escalate this to our Sales Team who specializes in these large-scale projects. They will contact you within 24 hours to discuss your specific requirements, budget considerations, and timeline expectations.\n\nThank you for showing your interest! Sales team will contact you within 24 hours. Goodbye!', '2025-10-07 11:08:46'),
(81, 7, 'hi', NULL, '2025-10-07 17:54:53'),
(82, 7, 'hi', 'Hello! Welcome to Glaxit! I\'m your AI Sales Assistant, here to help you with your project needs.\n\nTo get started, could you tell me what type of project you\'re interested in? For example: web design, mobile app, desktop software, game development, branding, SEO, digital marketing, or process automation?', '2025-10-07 17:54:59'),
(83, 7, 'i want to develop simple e commerece website', NULL, '2025-10-07 17:55:48'),
(84, 7, 'i want to develop simple e commerece website', 'Hello! I\'m Glaxit\'s AI Sales Assistant, and I\'m here to help you with your project needs.\n\nYou mentioned you want to develop a simple e-commerce website. To provide you with an accurate estimate, I need to gather a few more details about your requirements. Could you please answer the following questions?\n\n1. What type of products will you be selling? (e.g., physical goods, digital downloads, services)\n2. Do you need any specific features? (e.g., shopping cart, payment gateway integration, user accounts, inventory management)\n3. Will you require any third-party integrations? (e.g., shipping providers, CRM, email marketing tools)\n4. What is your target audience or market? (e.g., local, national, global)\n5. Do you have any design preferences or existing branding materials?\n\nPlease provide as much detail as possible. Would you like to add more requirements?', '2025-10-07 17:56:06'),
(85, 7, 'no', NULL, '2025-10-07 17:56:09'),
(86, 7, 'no', 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.\n\nDo you agree with this estimate?', '2025-10-07 17:56:11'),
(87, 7, 'Yes, I agree with the estimate', NULL, '2025-10-07 17:56:14'),
(88, 7, 'Yes, I agree with the estimate', 'Based on our conversation, I understand you\'re requesting a proposal for a simple e-commerce website development project. However, I notice that we haven\'t completed the requirements gathering process yet, and no price estimate has been calculated or locked in.\n\nLet me first ask some questions to properly understand your project requirements:\n\n1. What type of products will you be selling on your e-commerce website?\n2. Do you need any specific payment gateway integrations?\n3. Will you require inventory management features?\n4. Do you need customer account/login functionality?\n5. What about shipping and tax calculation features?\n\nOnce I understand your complete requirements, I can provide you with an accurate budget estimate and timeline. Would you like to provide these details?\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 17:56:24'),
(89, 7, 'hi', NULL, '2025-10-07 17:56:36'),
(90, 7, 'hi', 'Hello! I\'m Glaxit\'s AI Sales Assistant. Welcome! How can I help you today?\n\nTo get started, could you tell me what type of project you\'re interested in? For example: web design, mobile app, branding, game development, or something else?', '2025-10-07 17:56:43'),
(91, 7, 'what services you are offering', NULL, '2025-10-07 17:57:23'),
(92, 7, 'what services you are offering', 'Hello! I\'m Glaxit\'s AI Sales Assistant, here to help you find the right solution for your project needs.\n\nAt Glaxit, we offer a wide range of services including:\n- Web Design & Development\n- Mobile App Development (iOS & Android)\n- Desktop Application Development\n- Game Development\n- Branding & Logo Design\n- Digital Marketing (SEO, Social Media, Email Marketing)\n- Automation & AI Solutions (RPA, Chatbots, Data Science)\n- Cyber Security (Cloud Security, Endpoint Security, Identity Security)\n\nTo provide you with an accurate estimate, could you please tell me what type of project you\'re looking to develop?', '2025-10-07 17:57:34'),
(93, 7, 'i want to develop website', NULL, '2025-10-07 17:57:44'),
(94, 7, 'i want to develop website', 'Hello! I\'m Glaxit\'s AI Sales Assistant, here to help you with your website development project.\n\nTo provide you with an accurate estimate, I need to understand your project requirements better. Could you please tell me:\n\n1. What type of website are you looking to develop? (e.g., basic informational site, business website, e-commerce store, SaaS platform)\n2. Do you have any specific features or functionality requirements?\n3. What is your target timeline for completion?\n4. Do you need any additional services like SEO, branding, or ongoing maintenance?\n\nWould you like to add more requirements?', '2025-10-07 17:57:53'),
(95, 7, 'i want an e-commerce store', NULL, '2025-10-07 17:58:25'),
(96, 7, 'i want an e-commerce store', 'Great! I\'d be happy to help you with your e-commerce store project. To provide you with an accurate estimate, I need to gather some specific requirements.\n\nCould you please tell me:\n1. What type of products will you be selling?\n2. Approximately how many products do you plan to have in your store?\n3. Do you need any special features like inventory management, customer reviews, or integration with payment gateways?\n4. Will you need any custom design work or prefer using templates?\n5. Do you require mobile app development alongside the web store?\n\nWould you like to add more requirements?', '2025-10-07 17:58:36'),
(97, 7, 'no', NULL, '2025-10-07 17:58:41'),
(98, 7, 'no', 'Based on your requirements for an e-commerce website development, here is the estimate:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-07 17:58:48'),
(99, 7, 'Yes, I agree with the estimate', NULL, '2025-10-07 17:58:56'),
(100, 7, 'Yes, I agree with the estimate', 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 17:58:58'),
(101, 8, 'hello', NULL, '2025-10-07 18:01:12'),
(102, 8, 'hello', 'Hello! Welcome to Glaxit. I\'m your AI Sales Assistant here to help you with your project needs.\n\nTo get started, could you please tell me what type of project you\'re interested in? For example: web design, mobile app, desktop software, game development, branding, SEO, digital marketing, cloud security, or process automation?', '2025-10-07 18:01:18'),
(103, 8, 'i want to develop website', NULL, '2025-10-07 18:01:26'),
(104, 8, 'i want to develop website', 'Hello! I\'m Glaxit\'s AI Sales Assistant, and I\'m here to help you with your website development project.\n\nTo provide you with an accurate estimate, I need to understand your project requirements better. Could you please tell me what type of website you\'re looking to develop? For example:\n- A basic informational website\n- A business website with more features\n- An e-commerce site for selling products\n- Or something else?\n\nThis will help me match you with the right service and pricing.', '2025-10-07 18:01:34'),
(105, 8, 'i want simple e commerece website', NULL, '2025-10-07 18:01:52'),
(106, 8, 'i want simple e commerece website', 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.', '2025-10-07 18:01:54'),
(107, 8, 'what the hell', NULL, '2025-10-07 18:02:12'),
(108, 8, 'what the hell', 'Hello! I\'m Glaxit\'s AI Sales Assistant. I see you\'ve mentioned wanting to develop a website and a simple e-commerce website. To provide you with an accurate estimate, could you please clarify:\n\n1. What type of products or services will be sold on the e-commerce site?\n2. Do you need any specific features like payment integration, user accounts, or inventory management?\n3. What is your target timeline for completion?\n\nWould you like to add more requirements?', '2025-10-07 18:02:20'),
(109, 8, 'hello', NULL, '2025-10-07 18:02:30'),
(110, 8, 'hello', 'Hello! Welcome to Glaxit\'s AI Sales Assistant. I\'m here to help you explore our services and provide an estimate for your project.\n\nTo get started, could you please tell me what type of project you\'re looking to develop? For example: website, mobile app, desktop application, game, branding, SEO, digital marketing, or automation?', '2025-10-07 18:02:40'),
(111, 8, 'i want a e commerece website', NULL, '2025-10-07 18:02:56'),
(112, 8, 'i want a e commerece website', 'Great! I\'m Glaxit\'s AI Sales Assistant, and I\'d love to help you with your e-commerce website project.\n\nTo provide you with an accurate estimate, I need to gather some details about your requirements:\n\n1. What type of products will you be selling?\n2. Approximately how many products do you plan to have in your catalog?\n3. Do you need any special features like user accounts, wishlists, or inventory management?\n4. Will you need payment gateway integration (like PayPal, Stripe, etc.)?\n5. Do you require any third-party integrations (shipping, accounting, etc.)?\n6. What level of design customization are you looking for?\n\nPlease share these details so I can provide you with the best possible estimate for your e-commerce website project!', '2025-10-07 18:03:12'),
(113, 8, 'no', NULL, '2025-10-07 18:03:20'),
(114, 8, 'no', 'Based on your requirements for a simple e-commerce website, here is the estimate:\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-07 18:03:25'),
(115, 8, 'Yes, I agree with the estimate', NULL, '2025-10-07 18:03:27'),
(116, 8, 'Yes, I agree with the estimate', 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 18:03:31'),
(117, 9, 'hello', NULL, '2025-10-07 18:08:09'),
(118, 9, 'hello', 'Hello! Welcome to Glaxit! I\'m your AI Sales Assistant here to help you with your project needs.\n\nTo get started, could you please tell me what type of project you\'re looking to develop? For example:\n- Web Design & Development\n- Mobile Apps  \n- Desktop Apps\n- Games\n- Branding\n- SEO\n- Cloud Security\n- Digital Marketing\n- Process Automation\n\nThis will help me understand your requirements better and provide you with an accurate estimate.', '2025-10-07 18:08:17'),
(119, 9, 'i want to develop  website', NULL, '2025-10-07 18:08:29'),
(120, 9, 'i want to develop  website', 'Great! I\'m Glaxit\'s AI Sales Assistant, and I\'d love to help you develop your website.\n\nTo provide you with an accurate estimate, I need to understand your project requirements better. Could you please tell me:\n\n1. What type of website are you looking to develop? (e.g., basic informational site, business website, e-commerce store, SaaS platform)\n2. Do you have any specific features or functionalities in mind?\n3. What is your target audience or industry?\n4. Do you need any additional services like branding, SEO, or ongoing maintenance?\n\nWould you like to add more requirements?', '2025-10-07 18:08:38'),
(121, 9, 'no', NULL, '2025-10-07 18:08:43'),
(122, 9, 'no', 'Based on your requirement to develop a website, I\'ll provide an estimate from our standard pricing:\n\nBudget: $2,000–$5,000, Timeline: 2–4 weeks\n\nWould you like to add more requirements to your website project?\n\nDo you agree with this estimate?', '2025-10-07 18:08:50'),
(123, 9, 'Yes, I agree with the estimate', NULL, '2025-10-07 18:08:52'),
(124, 9, 'Yes, I agree with the estimate', 'ACCEPTED PROPOSAL\n\n**Project Requirements:**\n- Develop a website\n\n**Budget Estimate:** $2,000–$5,000  \n**Timeline Estimate:** 2–4 weeks\n\n**Additional Notes:**  \nWould you like to add more requirements to your website project?\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 18:09:01'),
(125, 10, 'hello', NULL, '2025-10-07 18:11:16'),
(126, 10, 'hello', 'Hello! Welcome to Glaxit\'s AI Sales Assistant. I\'m here to help you explore our services and provide a tailored estimate for your project.\n\nTo get started, could you please tell me what type of project you\'re interested in? For example:\n- Web Design & Development\n- Mobile Apps\n- Desktop Apps\n- Games\n- Branding\n- SEO\n- Cloud Security\n- Digital Marketing\n- Process Automation\n\nLet me know your project type and any initial requirements you have in mind!', '2025-10-07 18:11:26'),
(127, 10, 'i want to develop 3d car racing game', NULL, '2025-10-07 18:11:44'),
(128, 10, 'i want to develop 3d car racing game', 'Excellent! I\'m Glaxit\'s AI Sales Assistant, and I\'d be happy to help you with your 3D car racing game project.\n\nTo provide you with an accurate estimate, I need to gather some details about your project:\n\n1. What platforms are you targeting? (Mobile, PC, Console, or multiple platforms?)\n2. What is the intended scope? (Simple arcade-style racing, realistic simulation, or something in between?)\n3. Do you require multiplayer functionality?\n4. Are there any specific features you\'d like to include? (Customization, career mode, specific racing mechanics, etc.)\n5. What is your target audience? (Casual gamers, racing enthusiasts, etc.)\n\nPlease provide these details so I can better understand your requirements and provide an accurate estimate!', '2025-10-07 18:11:57'),
(129, 10, 'for pc and mobile both multi platforms', NULL, '2025-10-07 18:12:21'),
(130, 10, 'for pc and mobile both multi platforms', 'Thank you for providing that requirement! To ensure I understand your project fully, could you please clarify:\n\n1. Is this a 3D car racing game for both PC and mobile platforms?\n2. Are you looking for cross-platform development (single codebase for both platforms) or native development (separate codebases)?\n3. Do you have any specific features in mind (multiplayer, in-app purchases, advanced graphics, etc.)?\n4. What is your target audience and expected scale of the game?\n\nWould you like to add more requirements?', '2025-10-07 18:12:31'),
(131, 10, 'no', NULL, '2025-10-07 18:12:42'),
(132, 10, 'no', 'Based on your requirements for a 3D car racing game for both PC and mobile platforms, this falls under our \"3D Mobile\" category with cross-platform complexity.\n\nBudget: $50,000–$150,000, Timeline: 6–12 Months\n\n\n🚀 **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• i want to develop 3d car racing game\n• for pc and mobile both multi platforms\n\n**Estimated Budget:** $50,000–$150,000,\n**Estimated Timeline:** 6–12 Months\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        \n\n📞 **ESCALATION NOTICE**\n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your high-value project requirements. Goodbye!', '2025-10-07 18:12:47'),
(133, 11, 'hello', NULL, '2025-10-07 18:14:46'),
(134, 11, 'hello', 'Hello! Welcome to Glaxit\'s AI Sales Assistant. I\'m here to help you with your project needs and provide an estimate.\n\nTo get started, could you please tell me what type of project you\'re interested in? For example:\n- Web Design & Development\n- Mobile Apps\n- Desktop Apps\n- Games\n- Branding\n- SEO\n- Cloud Security\n- Digital Marketing\n- Process Automation\n\nOr feel free to describe your project in your own words!', '2025-10-07 18:14:54'),
(135, 11, 'i want to develop cloud security  project  who monitring', NULL, '2025-10-07 18:15:28'),
(136, 11, 'i want to develop cloud security  project  who monitring', 'Thank you for your interest in Glaxit\'s cloud security services! I understand you want to develop a cloud security project with monitoring capabilities.\n\nTo provide you with an accurate estimate, I need to gather some details about your project:\n\n1. What is the primary goal of this cloud security monitoring project?\n2. Which cloud platforms are you using (AWS, Azure, Google Cloud, etc.)?\n3. What scale of infrastructure needs monitoring (number of servers, applications, users)?\n4. Do you need real-time monitoring, alerting systems, or automated threat detection?\n5. Are there any compliance requirements (GDPR, HIPAA, PCI-DSS, etc.)?\n6. Do you need integration with existing security tools or systems?\n\nWould you like to add more requirements?', '2025-10-07 18:15:38'),
(137, 11, 'alerting systems', NULL, '2025-10-07 18:16:00'),
(138, 11, 'alerting systems', 'Thank you for your interest in Glaxit\'s cloud security monitoring and alerting systems! To provide you with an accurate estimate, I need to gather some details about your project.\n\nCould you please tell me:\n1. What is the size of your infrastructure that needs monitoring?\n2. Do you need real-time monitoring or periodic scans?\n3. What types of alerts are you looking for (security breaches, performance issues, compliance violations)?\n4. Do you require integration with existing security tools or systems?\n5. What is your preferred deployment model (cloud-based, on-premises, hybrid)?\n\nWould you like to add more requirements?', '2025-10-07 18:16:11'),
(139, 11, 'no thats it', NULL, '2025-10-07 18:16:21'),
(140, 11, 'no thats it', 'Based on your requirement for a cloud security monitoring project, I estimate:\n\nBudget: $2,000–$8,000/month, Timeline: 2–6 weeks\n\nDo you agree with this estimate?', '2025-10-07 18:16:26'),
(141, 11, 'No, I need some changes to the proposal', NULL, '2025-10-07 18:16:30'),
(142, 11, 'No, I need some changes to the proposal', 'Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?', '2025-10-07 18:16:30'),
(143, 11, 'i have requirments concern', NULL, '2025-10-07 18:16:42'),
(144, 11, 'i have requirments concern', 'I\'m sorry, I didn\'t understand. Could you please clarify if the issue is: 1) Budget issue, or 2) You want to update requirements?', '2025-10-07 18:16:42'),
(145, 11, 'i have requirements issue', NULL, '2025-10-07 18:16:51'),
(146, 11, 'i have requirements issue', 'Thank you for clarifying. Please provide the updated or additional requirements for your project, and I\'ll revise the estimate accordingly.', '2025-10-07 18:16:51'),
(147, 11, 'i also need real-time monitoring', NULL, '2025-10-07 18:17:39'),
(148, 11, 'i also need real-time monitoring', 'Thank you for providing that additional requirement. I\'ve added \"real-time monitoring\" to your cloud security project.\n\nBased on your requirements for a cloud security project with monitoring capabilities and real-time monitoring, I have the following estimate:\n\n**Budget: $2,000–$8,000, Timeline: 2–6 weeks**\n\nWould you like to add more requirements to your project?', '2025-10-07 18:17:45'),
(149, 11, 'no', NULL, '2025-10-07 18:17:50'),
(150, 11, 'no', 'Budget: $2,000–$8,000, Timeline: 2–6 weeks\n\nDo you agree with this estimate?', '2025-10-07 18:17:53'),
(151, 11, 'Yes, I agree with the estimate', NULL, '2025-10-07 18:18:07'),
(152, 11, 'Yes, I agree with the estimate', 'ACCEPTED PROPOSAL\n\n**Project Requirements:**\n- Cloud security project with monitoring capabilities\n- Real-time monitoring implementation\n\n**Budget Estimate:** $2,000–$8,000  \n**Timeline Estimate:** 2–6 weeks\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 18:18:14'),
(153, 13, 'hello whatsup', NULL, '2025-10-08 09:07:40'),
(154, 13, 'hello whatsup', '\n\nHello! I\'m Glaxit\'s AI Sales Assistant. ???? Could you tell me about the project you have in mind? Specifically:  \n- **Project type** (e.g., website, mobile app, automation, etc.)  \n- **Key requirements** (features, goals, timeline expectations)  \n\nThis will help me provide an accurate estimate!', '2025-10-08 09:07:48'),
(155, 13, 'mobile app', NULL, '2025-10-08 09:07:56'),
(156, 13, 'mobile app', '\n\nGreat! Let\'s clarify your mobile app requirements. Could you please answer these questions:  \n\n1. **App Type**: Native (iOS/Android), Cross-Platform (e.g., React Native), or MVP (Minimum Viable Product)?  \n2. **Platforms**: iOS, Android, or both?  \n3. **Key Features**: (e.g., user auth, payments, APIs, real-time chat, GPS, etc.)  \n4. **Backend/Integrations**: Do you need a custom backend or third-party integrations (e.g., AWS, Firebase, Stripe)?  \n5. **Design Complexity**: Custom UI/UX designs or standard templates?  \n6. **Timeline**: Do you have a deadline?  \n7. **Budget Range**: (Optional) Any rough estimate to align expectations?  \n\nLet me know when you’re ready! ????', '2025-10-08 09:08:08'),
(157, 13, 'i want Native (iOS/Android) with bckend integration also', NULL, '2025-10-08 09:08:25'),
(158, 13, 'i want Native (iOS/Android) with bckend integration also', NULL, '2025-10-08 09:08:42'),
(159, 13, 'i want Native (iOS/Android) with bckend integration also', NULL, '2025-10-08 09:08:59'),
(160, 13, 'i want Native (iOS/Android) with bckend integration also', '\n\nGreat! Let\'s gather some details about your Native (iOS/Android) app with backend integration. Could you please answer these questions?  \n\n1. **App Type**: Is this a business app, consumer app, marketplace, or something else?  \n2. **Core Features**: What key functionalities do you need? (e.g., user login, payment processing, real-time features)  \n3. **Target Audience**: B2B, B2C, or both?  \n4. **Additional Services**: Do you need UI/UX design, app store deployment, or ongoing maintenance?  \n\n*(Reply with your requirements)*', '2025-10-08 09:09:09'),
(161, 13, 'target audience is B2B   and  its a buiness app', NULL, '2025-10-08 09:09:30'),
(162, 13, 'target audience is B2B   and  its a buiness app', '\n\nTo provide an accurate estimate for your B2B business app, I need a few more details:\n\n1. **Core Features**: What specific functionalities will the app require? (e.g., user dashboards, payment processing, analytics, etc.)  \n2. **Third-Party Integrations**: Will you need CRM/ERP integrations (Salesforce, HubSpot, SAP)?  \n3. **User Authentication**: Should it include SSO, role-based access, or enterprise security protocols?  \n4. **Backend Complexity**: Will the backend require real-time data syncing, custom APIs, or heavy database operations?  \n\nWould you like to provide these details now?', '2025-10-08 09:09:45'),
(163, 13, 'no', NULL, '2025-10-08 09:09:55'),
(164, 13, 'no', '\nBased on your requirements for a Native (iOS/Android) business app with backend integration targeting B2B users:\n\n**Budget: $50,000–$100,000**  \n**Timeline: 4–7 months**  \n\n*Note: This qualifies as a high-value project due to native development + backend integration for B2B use.*  \n\nWould you like me to escalate this to our Sales Team for customized options? (Threshold protocol requires escalation for projects ≥$50k)\n\n\n???? **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• i want Native (iOS/Android) with bckend integration also\n• i want Native (iOS/Android) with bckend integration also\n• target audience is B2B   and  its a buiness app\n\n**Estimated Budget:** $50,000–$100,000\n**Estimated Timeline:** 4–7 months\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        \n\n???? **ESCALATION NOTICE**\n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your high-value project requirements. Goodbye!', '2025-10-08 09:10:07'),
(165, 14, 'hello how are you', NULL, '2025-10-08 12:20:48'),
(166, 14, 'hello how are you', '\nHello! Thank you for contacting Glaxit. How can I assist you with your project today? Could you please tell me what type of project you need help with? For example, web design, mobile app, game development, etc.', '2025-10-08 12:21:01'),
(167, 14, 'i want to make the mobile app for fitness tracking', NULL, '2025-10-08 12:21:16'),
(168, 14, 'i want to make the mobile app for fitness tracking', '\nGreat! Let\'s gather some details about your fitness tracking mobile app to provide an accurate estimate. Please answer the following questions:\n\n1. **Platforms needed**: iOS, Android, or both?\n2. **Key features**: (e.g., workout logging, GPS tracking, nutrition tracking, social sharing, wearable integration)\n3. **User authentication**: Email/password, social media login, or biometric?\n4. **Third-party integrations**: Any wearables (Fitbit/Apple Watch) or health platforms (Google Fit/Apple HealthKit)?\n5. **Design complexity**: Custom UI/UX with animations, or simpler design?\n6. **Monetization strategy**: Ads, subscriptions, in-app purchases, or paid app?\n7. **Data storage**: Local storage only, or cloud sync (user profiles/workout history)?\n8. **Real-time features**: Live workouts, progress tracking, or social challenges?\n\nWould you like help clarifying any of these points?', '2025-10-08 12:21:34'),
(169, 14, 'use andriod platform only with adding these features workout logging, GPS tracking, nutrition tracking, social sharing, wearable integration use local storage only', NULL, '2025-10-08 12:22:23'),
(170, 14, 'use andriod platform only with adding these features workout logging, GPS tracking, nutrition tracking, social sharing, wearable integration use local storage only', '\nBased on your requirements for an Android fitness tracking app with workout logging, GPS tracking, nutrition tracking, social sharing, wearable integration, and local storage only, here is the estimate:\n\n**Budget: $50,000–$100,000**  \n**Timeline: 4–7 months**  \n\nSince the lower bound of this estimate meets our $50,000 threshold, we must escalate this to our Sales Team for further review.  \n\n**REJECTED PROPOSAL**  \n*Reason: Threshold exceed concerns*  \n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your project in detail. Goodbye!  \n\n*[Conversation ended]*', '2025-10-08 12:22:37'),
(171, 14, 'hi i want to design webiste', NULL, '2025-10-08 12:23:14'),
(172, 14, 'hi i want to design webiste', '\nGreat! I\'d be happy to help you design a website. To provide an accurate estimate, I need to ask a few quick questions:\n\n1. **What type of website do you need?**  \n   (e.g., business site, e-commerce store, portfolio, SaaS platform, etc.)\n\n2. **Do you require any special features?**  \n   (e.g., user accounts, payment processing, CMS integration, etc.)\n\n3. **Do you need branding/logo design services?**  \n   (Or do you already have existing brand assets?)\n\n4. **Who is your target audience?**\n\n5. **What is your ideal timeline?**  \n\nPlease share these details so I can match you with the perfect solution!', '2025-10-08 12:23:34'),
(173, 14, 'i want e-commerce website for my skin care', NULL, '2025-10-08 12:24:05'),
(174, 14, 'i want e-commerce website for my skin care', '\nGreat! To provide an accurate estimate for your skincare e-commerce website, I need a few more details:\n\n1. **Number of products**  \n   Will you have under 50 products, 50-200 products, or over 200 products?\n\n2. **Payment methods**  \n   Do you need basic options (credit cards + PayPal) or advanced integrations (Apple Pay, Klarna, etc.)?\n\n3. **User accounts**  \n   Should customers be able to create profiles with order history/wishlists?\n\n4. **Inventory management**  \n   Do you require real-time stock tracking and automated low-stock alerts?\n\n5. **Special features**  \n   Any unique needs like AI skincare recommendations, subscription boxes, or virtual consultations?', '2025-10-08 12:24:17'),
(175, 14, 'number of product are almost 50 also create user accounts', NULL, '2025-10-08 12:24:46'),
(176, 14, 'number of product are almost 50 also create user accounts', '\nGreat! Based on your requirements for an e-commerce skincare website with 50 products and user accounts, let\'s confirm if there\'s anything else you\'d like to include. For example:  \n- Payment gateway integration (Stripe, PayPal, etc.)  \n- Mobile responsiveness  \n- SEO optimization  \n- Inventory management system  \n- Admin dashboard  \n\nWould you like to add more requirements, or are you ready for the estimate?', '2025-10-08 12:25:10'),
(177, 14, 'no more', NULL, '2025-10-08 12:25:22'),
(178, 14, 'no more', '\n\nBudget: $15,000–$50,000, Timeline: 8–16 weeks\n\nDo you agree with this estimate?', '2025-10-08 12:25:31'),
(179, 14, 'No, I need some changes to the proposal', NULL, '2025-10-08 12:26:01'),
(180, 14, 'No, I need some changes to the proposal', 'Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?', '2025-10-08 12:26:01'),
(181, 14, 'budget issue', NULL, '2025-10-08 12:26:06');
INSERT INTO `chat_logs` (`id`, `user_id`, `user_message`, `bot_response`, `timestamp`) VALUES
(182, 14, 'budget issue', '\n\n**REJECTED PROPOSAL**  \n\n**Gathered Requirements:**  \n1. E-commerce website for a skincare business.  \n2. Support for ~50 products.  \n3. User account creation functionality.  \n\n**Estimated Budget:** $15,000–$50,000  \n**Estimated Timeline:** 8–16 weeks  \n\n**Status:** Rejected (Reason: Budget concerns)  \n\n---  \n**Proposal Summary:**  \nThank you for your interest in Glaxit! Our Sales Team will contact you within 24 hours to discuss flexible options tailored to your needs. Goodbye!  \n\n*[Conversation ended. Proposal stored as REJECTED in the database.]*\n\n❌ **REJECTED PROPOSAL SUMMARY**\n\nOur Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!', '2025-10-08 12:26:20'),
(183, 14, 'hello i want to deisgn website for my restaurant', NULL, '2025-10-08 12:27:21'),
(184, 14, 'hello i want to deisgn website for my restaurant', '\n\nGreat! Let\'s start by understanding your restaurant website needs. Could you please answer these questions:\n\n1. **Number of pages needed** (e.g., Home, Menu, About, Contact, etc.)  \n2. **Key features required** (e.g., online reservations, food ordering, gallery, etc.)  \n3. **Design preferences** (e.g., modern, minimalist, existing branding guidelines)  \n4. **Content readiness** (Do you have text/images ready, or need help creating them?)  \n\nJust say *\"I\'m unsure\"* if you need guidance on any point! ????️', '2025-10-08 12:27:44'),
(185, 14, 'add these pages Home, Menu, About, Contact with online reservation also', NULL, '2025-10-08 12:28:22'),
(186, 14, 'add these pages Home, Menu, About, Contact with online reservation also', '\n\nGreat! Let me confirm your requirements:  \nYou need a restaurant website with these pages:  \n- Home  \n- Menu  \n- About  \n- Contact  \nPlus online reservation functionality  \n\nWould you like to add any other features like:  \n1. Online ordering/payment system  \n2. Photo gallery  \n3. Customer reviews section  \n4. Social media integration  \n5. Blog/news section  \n\nOr would you like to finalize with these requirements?', '2025-10-08 12:28:35'),
(187, 14, 'also add photo gallery', NULL, '2025-10-08 12:28:49'),
(188, 14, 'also add photo gallery', '\n\nGreat! Let me confirm your requirements for the restaurant website:  \n- Pages: Home, Menu, About, Contact  \n- Online reservation system  \n- Photo gallery  \n\nWould you like to add any other features like:  \n- Food ordering/delivery integration  \n- Customer reviews section  \n- Social media feeds  \n- Loyalty program functionality  \n\nOr is this complete?', '2025-10-08 12:29:06'),
(189, 14, 'also add Customer reviews section', NULL, '2025-10-08 12:29:19'),
(190, 14, 'also add Customer reviews section', '\n\nGreat! Let me confirm your requirements for the restaurant website:  \n- Home, Menu, About, Contact pages  \n- Online reservation system  \n- Photo gallery  \n- Customer reviews section  \n\n**Would you like to add more requirements?**  \n*(Examples: Blog, loyalty program integration, multilingual support, etc.)*', '2025-10-08 12:29:32'),
(191, 14, 'no more', NULL, '2025-10-08 12:29:37'),
(192, 14, 'no more', '\n\nBased on your requirements for a restaurant website with Home, Menu, About, Contact pages, online reservations, photo gallery, and customer reviews section:  \n\n**Budget: $15,000–$20,000**  \n**Timeline: 8–10 weeks**\n\nDo you agree with this estimate?', '2025-10-08 12:29:51'),
(193, 14, 'Yes, I agree with the estimate', NULL, '2025-10-08 12:29:54'),
(194, 14, 'Yes, I agree with the estimate', '\n\n**ACCEPTED PROPOSAL**\n\n**Project Summary**  \nGlaxit will design and develop a custom website for your restaurant business, including all requested features and functionalities.\n\n---\n\n**Gathered Requirements:**  \n1. Website design for a restaurant  \n2. Core pages:  \n   - Home  \n   - Menu  \n   - About  \n   - Contact  \n3. Online reservation system  \n4. Photo gallery  \n5. Customer reviews section  \n\n---\n\n**Budget:**  \n$15,000–$20,000  \n\n**Timeline:**  \n8–10 weeks  \n\n---\n\n**Next Steps:**  \nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We’ll contact you within 24 hours to finalize details. Goodbye!  \n\n*(Conversation ended. Proposal stored as ACCEPTED in Glaxit’s database.)*\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-08 12:30:09'),
(195, 16, 'hi', NULL, '2025-10-08 12:42:37'),
(196, 16, 'hi', '\nHello! I\'m Glaxit\'s AI Sales Assistant. To help you get started, could you please tell me what type of project you\'re interested in?  \n\nFor example:  \n- Web Design & Development  \n- Mobile Apps  \n- Desktop Apps  \n- Games  \n- Branding  \n- SEO  \n- Cloud Security  \n- Digital Marketing  \n- Process Automation  \n\nLet me know if you need help narrowing it down!', '2025-10-08 12:42:55'),
(197, 16, 'what are your pricing list', NULL, '2025-10-08 12:43:11'),
(198, 16, 'what are your pricing list', '\nThank you for your interest in Glaxit! To provide accurate pricing, I\'ll need to understand your project requirements. Let\'s start with these key questions:\n\n1. **Project Type**  \n   Which service category best fits your needs?  \n   • Web Design & Development  \n   • Mobile Apps  \n   • Desktop Apps  \n   • Branding (Logos, Business Cards, Social Media Kits)  \n   • Digital Marketing  \n   • Other (Specify)  \n\n2. **Scope Details**  \n   - Are you requesting **branding services** (logos, business cards, social media assets)?  \n   - Do you need **printed materials** (flyers, standees, certificates)?  \n   - Will this include **digital items** (company profiles, payment slips)?  \n\n3. **Timeline**  \n   Is there a specific deadline? (e.g., 2 weeks / 1 month / flexible)  \n\n4. **Additional Notes**  \n   Any special requirements (e.g., QR codes, packaging design)?  \n\nLet me know these details, and I’ll match your project with our pricing tiers! ????', '2025-10-08 12:43:47'),
(199, 16, 'i want to deisgn saas plaform webite', NULL, '2025-10-08 12:44:50'),
(200, 16, 'i want to deisgn saas plaform webite', '\n\nThank you for choosing Glaxit! To provide an accurate estimate for your SaaS platform website, I need to ask a few structured questions:\n\n1. **User Scale**: Are you targeting SMBs (1k–10k users) or enterprises (10k+ users)?  \n2. **Core Features**: Will this include user dashboards, subscription billing, API integrations, or AI/ML components?  \n3. **Authentication**: Do you need SSO, social logins, or enterprise-grade security?  \n4. **Third-Party Integrations**: List any critical tools (e.g., payment gateways, CRM, cloud services).  \n5. **Data Handling**: Will the platform process sensitive/regulated data (e.g., healthcare, finance)?  \n\n*Note: SaaS platforms are inherently high-value projects. Your answers will help us refine the scope.*', '2025-10-08 12:45:11'),
(201, 16, 'enterprise and my budget is 5k-10k dollars and no timeline in mind', NULL, '2025-10-08 12:45:45'),
(202, 16, 'enterprise and my budget is 5k-10k dollars and no timeline in mind', '\n\nBudget: $50k–$100k, Timeline: 4–7m  \n\n**REJECTED PROPOSAL**  \nThank you for showing your interest! Our sales team will contact you within 24 hours. Goodbye!  \n\n*(Conversation ended - Threshold exceeded for SaaS platform development)*\n\n\n???? **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• what are your pricing list\n• i want to deisgn saas plaform webite\n\n**Estimated Budget:** $50k–$100k,\n**Estimated Timeline:** 4–7m  \n\n\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        \n\n???? **ESCALATION NOTICE**\n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your high-value project requirements. Goodbye!', '2025-10-08 12:45:58'),
(203, 17, 'hello whatsup', NULL, '2025-10-10 09:57:53'),
(204, 17, 'hello whatsup', '\n\nHello! I\'m Glaxit\'s AI Sales Assistant. How can I help you today?  \n\nTo get started, could you tell me:  \n1. **What type of project are you looking to develop?** (e.g., web app, mobile app, automation, branding, etc.)  \n2. **Do you have specific requirements or features in mind?**  \n\nLet me know—I’ll guide you through the next steps! ????', '2025-10-10 09:58:15'),
(205, 17, 'i want to develop website', NULL, '2025-10-10 09:58:53'),
(206, 17, 'i want to develop website', '\n\nGreat! Let\'s start by understanding your website project requirements. Could you please answer these questions:\n\n1. What type of website are you building?  \n   (Business site, E-commerce store, SaaS platform, etc.)\n\n2. How many pages/sections do you need?  \n   (e.g., 5-10 page business site, 20+ page content hub)\n\n3. Do you require custom design?  \n   (New UI/UX design from scratch vs. template customization)\n\n4. What key features are needed?  \n   (User accounts, payment system, booking system, etc.)\n\n5. Any special technical requirements?  \n   (Third-party integrations, database needs, etc.)\n\nTake your time – the more details you provide, the more accurate our estimate will be!', '2025-10-10 09:59:09'),
(207, 17, 'i want to make 5-10 pages of wbeiste its a busness website  add these  User accounts, payment system, booking system', NULL, '2025-10-10 09:59:42'),
(208, 17, 'i want to make 5-10 pages of wbeiste its a busness website  add these  User accounts, payment system, booking system', '\nBased on your requirements for a 5-10 page business website with user accounts, payment system, and booking system, here\'s your estimate:\n\n**Budget: $15,000–$50,000**  \n**Timeline: 8–16 weeks**  \n\nThis falls under our **E-Commerce Development** category due to the payment integration and user account functionality required. Would you like to add more requirements to this project?', '2025-10-10 09:59:55'),
(209, 17, 'no more', NULL, '2025-10-10 10:00:08'),
(210, 17, 'no more', '\n\nBased on your requirements for a business website with 5-10 pages, user accounts, payment system, and booking system:  \n\n**Budget: $15,000–$30,000**  \n**Timeline: 8–14 Weeks**\n\nDo you agree with this estimate?', '2025-10-10 10:00:21'),
(211, 17, 'Yes, I agree with the estimate', NULL, '2025-10-10 10:00:26'),
(212, 17, 'Yes, I agree with the estimate', '\n\n**ACCEPTED PROPOSAL**  \n\n**Project Overview**  \nThank you for choosing Glaxit! Below is your finalized proposal based on the requirements you provided.  \n\n---  \n\n**Gathered Requirements**:  \n1. Development of a business website  \n2. 5–10 pages  \n3. Key features:  \n   - User accounts  \n   - Payment system  \n   - Booking system  \n\n---  \n\n**Budget**: $15,000–$30,000  \n**Timeline**: 8–14 Weeks  \n\n---  \n\n**Next Steps**:  \nYour proposal has been **ACCEPTED** and stored in our system. Our Sales Team will contact you within 24 hours to finalize contracts and project kickoff.  \n\n**Thank you for trusting Glaxit!**  \n*This conversation is now concluded. Goodbye!*\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-10 10:00:38'),
(213, 18, 'hello', NULL, '2025-10-10 10:05:40'),
(214, 18, 'hello', 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.', '2025-10-10 10:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_members`
--

CREATE TABLE `marketing_members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marketing_members`
--

INSERT INTO `marketing_members` (`id`, `name`, `email`, `create_at`) VALUES
(1, 'Aimen', 'atifaimen21@gmail.com', '2025-10-10 08:05:01'),
(5, 'aleez', 'aimenatif080@gmail.com', '2025-10-10 09:04:44'),
(6, 'Pakistan', 'pakistan34@gmail.com', '2025-10-10 11:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE `proposals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `proposal_type` enum('ACCEPTED','REJECTED','ESCALATED') NOT NULL,
  `requirements` text DEFAULT NULL,
  `estimated_price` varchar(50) DEFAULT NULL,
  `estimated_timeline` varchar(50) DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `proposal_content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Sent') DEFAULT 'Pending',
  `assigned_member_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`id`, `user_id`, `proposal_type`, `requirements`, `estimated_price`, `estimated_timeline`, `rejection_reason`, `proposal_content`, `created_at`, `status`, `assigned_member_id`) VALUES
(1, 1, 'ACCEPTED', 'i want to develop  e commerece website', '$15,000–$50,000,', '8–16 weeks', NULL, 'ACCEPTED PROPOSAL\n\n**Requirements:**  \n- E-commerce website development\n\n**Budget:** $15,000–$50,000  \n**Timeline:** 8–16 weeks  \n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-06 15:58:19', 'Sent', 3),
(2, 2, 'REJECTED', 'i want to develop e comerece website', '$15,000–$50,000,', '8–16 weeks', 'Budget concerns', 'REJECTED PROPOSAL\n\n**Requirements:**  \n- E-commerce website development\n\n**Estimated Budget:** $15,000–$50,000  \n**Estimated Timeline:** 8–16 weeks  \n\n**Status:** Rejected due to budget concerns  \n\nThank you for showing your interest! Our Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!', '2025-10-06 16:00:26', 'Sent', 1),
(4, 4, 'ACCEPTED', 'i want chatgpt app\nusing Simple Tools', '$8,000–$20,000,', '1–2 Months', NULL, 'ACCEPTED PROPOSAL\n\nThank you for choosing Glaxit for your project. Below is your accepted proposal based on the requirements we\'ve gathered:\n\n**Gathered Requirements:**\n- i want chatgpt app\n- using Simple Tools\n\n**Budget:** $8,000–$20,000,\n**Timeline:** 1–2 Months\n\nThis proposal has been accepted and stored in our system. Our team will contact you within 24 hours to finalize the details and begin your project.\n\nThank you for your trust in Glaxit! Goodbye!', '2025-10-06 18:24:30', 'Sent', 1),
(5, 6, 'ACCEPTED', 'i want to make website for my buisness gym\nits a bsuiness site with online registering member\nyes also add the payment integration', '$15,000–$50,000,', '8–16 weeks', NULL, '**ACCEPTED PROPOSAL**\n\n**Project Requirements Summary:**  \n- Website for a gym business  \n- Business site with online member registration  \n- Payment integration  \n\n**Budget Estimate:**  \nPrice Range: $15,000–$50,000  \n\n**Timeline Estimate:**  \nTimeline: 8–16 weeks  \n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 10:59:34', 'Sent', 1),
(6, 6, 'REJECTED', 'i want to make mobile app\nits a business app using iOS olatform\nadd these customer engagement and tracking system', '$40,000–$100,000,', '4–7 months', 'Budget concerns', '**REJECTED PROPOSAL**\n\n**Gathered Requirements:**\n- Mobile app development\n- Business app using iOS platform\n- Customer engagement and tracking system\n\n**Estimated Budget:** $40,000–$100,000  \n**Estimated Timeline:** 4–7 months\n\n**Status:** Rejected due to budget concerns\n\nThank you for showing your interest! Our Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!', '2025-10-07 11:03:58', 'Sent', 1),
(8, 7, 'ACCEPTED', 'i want to develop simple e commerece website', NULL, NULL, NULL, 'Based on our conversation, I understand you\'re requesting a proposal for a simple e-commerce website development project. However, I notice that we haven\'t completed the requirements gathering process yet, and no price estimate has been calculated or locked in.\n\nLet me first ask some questions to properly understand your project requirements:\n\n1. What type of products will you be selling on your e-commerce website?\n2. Do you need any specific payment gateway integrations?\n3. Will you require inventory management features?\n4. Do you need customer account/login functionality?\n5. What about shipping and tax calculation features?\n\nOnce I understand your complete requirements, I can provide you with an accurate budget estimate and timeline. Would you like to provide these details?', '2025-10-07 17:56:24', '', 3),
(9, 7, 'ACCEPTED', 'what services you are offering\ni want to develop website\ni want an e-commerce store', '$15,000–$50,000,', '8–16 weeks', NULL, 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.', '2025-10-07 17:58:58', 'Sent', 5),
(10, 8, 'ACCEPTED', 'i want to develop website\ni want simple e commerece website\nwhat the hell\ni want a e commerece website', '$15,000–$50,000,', '8–16 weeks', NULL, 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.', '2025-10-07 18:03:31', 'Sent', 3),
(11, 9, 'ACCEPTED', 'i want to develop  website', '$2,000–$5,000,', '2–4 weeks\n\nWould you like to add more requirements', NULL, 'ACCEPTED PROPOSAL\n\n**Project Requirements:**\n- Develop a website\n\n**Budget Estimate:** $2,000–$5,000  \n**Timeline Estimate:** 2–4 weeks\n\n**Additional Notes:**  \nWould you like to add more requirements to your website project?\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 18:09:01', 'Sent', 1),
(12, 10, 'ESCALATED', '• i want to develop 3d car racing game\n• for pc and mobile both multi platforms', '$50,000–$150,000,', '6–12 Months', 'Threshold exceed concerns', '\n🚀 **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• i want to develop 3d car racing game\n• for pc and mobile both multi platforms\n\n**Estimated Budget:** $50,000–$150,000,\n**Estimated Timeline:** 6–12 Months\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        ', '2025-10-07 18:12:47', 'Sent', 3),
(13, 11, 'ACCEPTED', 'i want to develop cloud security  project  who monitring\ni also need real-time monitoring', '$2,000–$8,000,', '2–6 weeks', NULL, 'ACCEPTED PROPOSAL\n\n**Project Requirements:**\n- Cloud security project with monitoring capabilities\n- Real-time monitoring implementation\n\n**Budget Estimate:** $2,000–$8,000  \n**Timeline Estimate:** 2–6 weeks\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We\'ll contact you within 24 hours to finalize details. Goodbye!', '2025-10-07 18:18:14', 'Sent', 3),
(14, 13, 'ESCALATED', '• i want Native (iOS/Android) with bckend integration also\n• i want Native (iOS/Android) with bckend integration also\n• target audience is B2B   and  its a buiness app', '$50,000–$100,000', '4–7 months', 'Threshold exceed concerns', '\n???? **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• i want Native (iOS/Android) with bckend integration also\n• i want Native (iOS/Android) with bckend integration also\n• target audience is B2B   and  its a buiness app\n\n**Estimated Budget:** $50,000–$100,000\n**Estimated Timeline:** 4–7 months\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        ', '2025-10-08 09:10:07', '', 3),
(15, 14, 'REJECTED', 'hi i want to design webiste\ni want e-commerce website for my skin care\nnumber of product are almost 50 also create user accounts', '$15,000–$50,000,', '8–16 weeks', 'Budget concerns', '\n\n**REJECTED PROPOSAL**  \n\n**Gathered Requirements:**  \n1. E-commerce website for a skincare business.  \n2. Support for ~50 products.  \n3. User account creation functionality.  \n\n**Estimated Budget:** $15,000–$50,000  \n**Estimated Timeline:** 8–16 weeks  \n\n**Status:** Rejected (Reason: Budget concerns)  \n\n---  \n**Proposal Summary:**  \nThank you for your interest in Glaxit! Our Sales Team will contact you within 24 hours to discuss flexible options tailored to your needs. Goodbye!  \n\n*[Conversation ended. Proposal stored as REJECTED in the database.]*', '2025-10-08 12:26:20', 'Sent', 1),
(16, 14, 'ACCEPTED', 'hello i want to deisgn website for my restaurant\nadd these pages Home, Menu, About, Contact with online reservation also\nalso add photo gallery\nalso add Customer reviews section', '$15,000–$20,000', '8–10 weeks', NULL, '\n\n**ACCEPTED PROPOSAL**\n\n**Project Summary**  \nGlaxit will design and develop a custom website for your restaurant business, including all requested features and functionalities.\n\n---\n\n**Gathered Requirements:**  \n1. Website design for a restaurant  \n2. Core pages:  \n   - Home  \n   - Menu  \n   - About  \n   - Contact  \n3. Online reservation system  \n4. Photo gallery  \n5. Customer reviews section  \n\n---\n\n**Budget:**  \n$15,000–$20,000  \n\n**Timeline:**  \n8–10 weeks  \n\n---\n\n**Next Steps:**  \nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We’ll contact you within 24 hours to finalize details. Goodbye!  \n\n*(Conversation ended. Proposal stored as ACCEPTED in Glaxit’s database.)*', '2025-10-08 12:30:09', '', 1),
(17, 16, 'ESCALATED', '• what are your pricing list\n• i want to deisgn saas plaform webite', '$50k–$100k,', '4–7m  \n\n', 'Threshold exceed concerns', '\n???? **ESCALATION PROPOSAL - HIGH VALUE PROJECT**\n\n**Client Requirements:**\n• what are your pricing list\n• i want to deisgn saas plaform webite\n\n**Estimated Budget:** $50k–$100k,\n**Estimated Timeline:** 4–7m  \n\n\n\n**Reason for Escalation:** Project budget exceeds the $50,000 threshold for automated processing.\n\n**Next Steps:**\n- Your project has been flagged as high-value\n- Our specialized sales team will contact you within 24 hours\n- They will discuss customized solutions and premium service options\n- You\'ll receive personalized attention from our enterprise specialists\n\nThank you for your interest in Glaxit\'s premium services! Our team is excited to work on your high-value project.\n                        ', '2025-10-08 12:45:58', '', 5),
(18, 17, 'ACCEPTED', 'i want to develop website\ni want to make 5-10 pages of wbeiste its a busness website  add these  User accounts, payment system, booking system', '$15,000–$30,000', '8–14 Weeks', NULL, '\n\n**ACCEPTED PROPOSAL**  \n\n**Project Overview**  \nThank you for choosing Glaxit! Below is your finalized proposal based on the requirements you provided.  \n\n---  \n\n**Gathered Requirements**:  \n1. Development of a business website  \n2. 5–10 pages  \n3. Key features:  \n   - User accounts  \n   - Payment system  \n   - Booking system  \n\n---  \n\n**Budget**: $15,000–$30,000  \n**Timeline**: 8–14 Weeks  \n\n---  \n\n**Next Steps**:  \nYour proposal has been **ACCEPTED** and stored in our system. Our Sales Team will contact you within 24 hours to finalize contracts and project kickoff.  \n\n**Thank you for trusting Glaxit!**  \n*This conversation is now concluded. Goodbye!*', '2025-10-10 10:00:38', 'Sent', 6);

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
) ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category`, `service_name`, `description`, `min_price`, `max_price`, `min_timeline`, `max_timeline`, `created_at`, `updated_at`) VALUES
(39, 'Branding', 'Enterprise Rebrand', 'Large-scale enterprise rebranding projects', 50000.00, 150000.00, 80, 180, '2025-10-10 10:59:53', '2025-10-10 11:00:36'),
(41, 'Branding', 'Logo Pack', 'Complete logo package with multiple variations and formats', 1000.00, 3000.00, 7, 21, '2025-10-10 11:02:12', '2025-10-10 11:02:12'),
(42, 'SEO', 'Local Setup', 'Local SEO setup and optimization for small businesses', 2000.00, 4000.00, 28, 42, '2025-10-10 11:03:12', '2025-10-10 11:03:12'),
(43, 'SEO', 'National SEO', 'Monthly national SEO services with ongoing optimization', 2000.00, 10000.00, 180, 360, '2025-10-10 11:03:56', '2025-10-10 11:03:56'),
(44, 'SEO', 'Enterprise SEO', 'Enterprise-level SEO strategy and implementation', 50000.00, 150000.00, 180, 360, '2025-10-10 11:04:44', '2025-10-10 11:04:44'),
(45, 'Cloud Security', 'Audit', 'Comprehensive cloud security audit and assessment', 5000.00, 15000.00, 14, 42, '2025-10-10 11:05:29', '2025-10-10 11:05:29'),
(46, 'Cloud Security', 'Monitoring', 'Monthly cloud security monitoring and threat detection', 2000.00, 8000.00, 30, 60, '2025-10-10 11:06:31', '2025-10-10 11:06:31'),
(47, 'Cloud Security', 'Enterprise Security', 'Enterprise cloud security implementation and management', 50000.00, 150000.00, 90, 180, '2025-10-10 11:07:25', '2025-10-10 11:07:25'),
(48, 'Digital Marketing', 'Social Media', 'Monthly social media management and content creation', 1000.00, 3000.00, 30, 60, '2025-10-10 11:08:27', '2025-10-10 11:08:27'),
(49, 'Digital Marketing', 'Paid Campaigns', 'Monthly paid advertising campaign management', 2000.00, 10000.00, 30, 60, '2025-10-10 11:09:09', '2025-10-10 11:09:09'),
(50, 'Digital Marketing', 'Enterprise Campaigns', 'Enterprise digital marketing campaigns and strategy', 50000.00, 150000.00, 90, 180, '2025-10-10 11:09:53', '2025-10-10 11:09:53'),
(51, 'Process Automation (AI/RPA)', 'Basic Workflow', 'Basic workflow automation and process optimization', 5000.00, 15000.00, 14, 42, '2025-10-10 11:10:37', '2025-10-10 11:10:37'),
(52, 'Process Automation (AI/RPA)', 'Custom RPA', 'Custom robotic process automation solutions', 20000.00, 50000.00, 60, 120, '2025-10-10 11:11:19', '2025-10-10 11:11:19'),
(53, 'Process Automation (AI/RPA)', 'AI Chatbots', 'Advanced AI-powered chatbot development', 50000.00, 150000.00, 90, 180, '2025-10-10 11:12:14', '2025-10-10 11:12:14'),
(54, 'Process Automation (AI/RPA)', 'Enterprise Automation', 'Enterprise-level automation systems and integration', 50000.00, 200000.00, 120, 240, '2025-10-10 11:13:08', '2025-10-10 11:13:08'),
(55, 'Games', '2D Mobile', '2D mobile games for iOS and Android platforms', 10000.00, 30000.00, 90, 180, '2025-10-10 11:13:58', '2025-10-10 11:13:58'),
(56, 'Games', '3D Mobile', '3D mobile games with advanced graphics and gameplay', 30000.00, 50000.00, 180, 360, '2025-10-10 11:14:43', '2025-10-10 11:14:43'),
(57, 'Games', 'PC/Console Indie', 'Indie games for PC and console platforms', 50000.00, 100000.00, 180, 360, '2025-10-10 11:15:33', '2025-10-10 11:15:33'),
(58, 'Games', 'AAA Games', 'Triple-A quality games with high-end graphics and features', 100000.00, 150000.00, 360, 720, '2025-10-10 11:16:42', '2025-10-10 11:16:42'),
(59, 'Desktop Apps', 'Simple Tools', 'Basic desktop applications and utility tools', 8000.00, 20000.00, 30, 60, '2025-10-10 11:17:49', '2025-10-10 11:17:49'),
(60, 'Desktop Apps', 'Business Apps', 'Business desktop applications with database integration', 30000.00, 50000.00, 90, 180, '2025-10-10 11:18:35', '2025-10-10 11:18:35'),
(61, 'Desktop Apps', 'Enterprise Software', 'Large-scale enterprise desktop solutions', 50000.00, 150000.00, 120, 240, '2025-10-10 11:19:20', '2025-10-10 11:19:20'),
(62, 'Mobile Apps', 'MVP', 'Minimum viable product mobile applications with core features', 10000.00, 20000.00, 42, 70, '2025-10-10 11:20:22', '2025-10-10 11:20:22'),
(63, 'Mobile Apps', 'Cross-Platform', 'Cross-platform mobile apps working on both iOS and Android', 20000.00, 30000.00, 90, 180, '2025-10-10 11:21:09', '2025-10-10 11:21:09'),
(64, 'Mobile Apps', 'Native Apps', 'Native mobile applications optimized for specific platforms', 30000.00, 50000.00, 120, 210, '2025-10-10 11:23:53', '2025-10-10 11:23:53'),
(65, 'Mobile Apps', 'Complex Apps', 'Advanced mobile applications with complex features and integrations', 50000.00, 100000.00, 150, 240, '2025-10-10 11:25:06', '2025-10-10 11:25:06'),
(66, 'Web Design & Development', 'Basic Sites', 'Professional basic website development with responsive design', 2000.00, 5000.00, 14, 28, '2025-10-10 11:26:26', '2025-10-10 11:26:26'),
(67, 'Web Design & Development', 'Business Sites', 'Advanced business websites with custom features and CMS', 8000.00, 20000.00, 42, 70, '2025-10-10 11:27:16', '2025-10-10 11:27:16'),
(68, 'Web Design & Development', 'E-Commerce', 'Full-featured online stores with payment integration and inventory management', 15000.00, 50000.00, 56, 112, '2025-10-10 11:28:07', '2025-10-10 11:28:07'),
(69, 'Web Design & Development', 'SaaS Platforms', 'Custom software-as-a-service platforms with subscription management', 50000.00, 150000.00, 90, 180, '2025-10-10 11:31:56', '2025-10-10 11:31:56'),
(70, 'Branding', 'Full Identity', 'Complete brand identity including logo, colors, typography, and guidelines', 5000.00, 15000.00, 28, 56, '2025-10-10 11:37:48', '2025-10-10 11:37:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `created_at`) VALUES
(1, '2025-10-06 15:57:41'),
(2, '2025-10-06 15:59:33'),
(3, '2025-10-06 16:03:05'),
(4, '2025-10-06 18:23:28'),
(5, '2025-10-07 09:09:41'),
(6, '2025-10-07 10:57:39'),
(7, '2025-10-07 17:54:26'),
(8, '2025-10-07 18:01:08'),
(9, '2025-10-07 18:08:05'),
(10, '2025-10-07 18:11:13'),
(11, '2025-10-07 18:14:26'),
(12, '2025-10-08 09:07:33'),
(13, '2025-10-08 09:07:33'),
(14, '2025-10-08 12:20:29'),
(15, '2025-10-08 12:37:49'),
(16, '2025-10-08 12:42:34'),
(17, '2025-10-10 09:57:45'),
(18, '2025-10-10 10:05:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `marketing_members`
--
ALTER TABLE `marketing_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `proposals`
--
ALTER TABLE `proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_member_id` (`assigned_member_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_name` (`service_name`),
  ADD KEY `idx_price_range` (`min_price`,`max_price`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_logs`
--
ALTER TABLE `chat_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `marketing_members`
--
ALTER TABLE `marketing_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `proposals`
--
ALTER TABLE `proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD CONSTRAINT `chat_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `proposals`
--
ALTER TABLE `proposals`
  ADD CONSTRAINT `proposals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
