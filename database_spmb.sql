-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: spmb
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `foundation_id` bigint unsigned DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_academic_years_unique_id` (`unique_id`),
  KEY `idx_academic_years_foundation_id` (`foundation_id`),
  KEY `idx_academic_years_is_deleted` (`is_deleted`),
  KEY `idx_academic_years_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admission_documents`
--

DROP TABLE IF EXISTS `admission_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `max_size` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admission_documents_unique_id` (`unique_id`),
  KEY `idx_admission_documents_is_deleted` (`is_deleted`),
  KEY `idx_admission_documents_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_documents`
--

LOCK TABLES `admission_documents` WRITE;
/*!40000 ALTER TABLE `admission_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `admission_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admission_offerings`
--

DROP TABLE IF EXISTS `admission_offerings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_offerings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `admission_period_id` bigint unsigned NOT NULL DEFAULT '0',
  `branch_id` bigint unsigned NOT NULL DEFAULT '0',
  `entry_grade_id` bigint unsigned NOT NULL DEFAULT '0',
  `education_track_id` bigint unsigned NOT NULL DEFAULT '0',
  `education_program_id` bigint unsigned DEFAULT '0',
  `applicant_type_id` bigint unsigned DEFAULT '0',
  `admission_pathway_id` bigint unsigned DEFAULT '0',
  `quota` int DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admission_offerings_unique_id` (`unique_id`),
  KEY `idx_admission_offerings_period_id` (`admission_period_id`),
  KEY `idx_admission_offerings_branch_id` (`branch_id`),
  KEY `idx_admission_offerings_entry_grade_id` (`entry_grade_id`),
  KEY `idx_admission_offerings_track_id` (`education_track_id`),
  KEY `idx_admission_offerings_program_id` (`education_program_id`),
  KEY `idx_admission_offerings_applicant_type_id` (`applicant_type_id`),
  KEY `idx_admission_offerings_pathway_id` (`admission_pathway_id`),
  KEY `idx_admission_offerings_is_deleted` (`is_deleted`),
  KEY `idx_admission_offerings_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_offerings`
--

LOCK TABLES `admission_offerings` WRITE;
/*!40000 ALTER TABLE `admission_offerings` DISABLE KEYS */;
/*!40000 ALTER TABLE `admission_offerings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admission_pathways`
--

DROP TABLE IF EXISTS `admission_pathways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_pathways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admission_pathways_unique_id` (`unique_id`),
  KEY `idx_admission_pathways_is_deleted` (`is_deleted`),
  KEY `idx_admission_pathways_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_pathways`
--

LOCK TABLES `admission_pathways` WRITE;
/*!40000 ALTER TABLE `admission_pathways` DISABLE KEYS */;
/*!40000 ALTER TABLE `admission_pathways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admission_periods`
--

DROP TABLE IF EXISTS `admission_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `foundation_id` bigint unsigned DEFAULT '0',
  `academic_year_id` bigint unsigned NOT NULL DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `registration_start_at` date DEFAULT NULL,
  `registration_end_at` date DEFAULT NULL,
  `announcement_at` date DEFAULT NULL,
  `registration_fee` decimal(15,2) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admission_periods_unique_id` (`unique_id`),
  KEY `idx_admission_periods_academic_year_id` (`academic_year_id`),
  KEY `idx_admission_periods_foundation_id` (`foundation_id`),
  KEY `idx_admission_periods_is_deleted` (`is_deleted`),
  KEY `idx_admission_periods_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_periods`
--

LOCK TABLES `admission_periods` WRITE;
/*!40000 ALTER TABLE `admission_periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `admission_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicant_types`
--

DROP TABLE IF EXISTS `applicant_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicant_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_applicant_types_unique_id` (`unique_id`),
  KEY `idx_applicant_types_is_deleted` (`is_deleted`),
  KEY `idx_applicant_types_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicant_types`
--

LOCK TABLES `applicant_types` WRITE;
/*!40000 ALTER TABLE `applicant_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `applicant_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_settings`
--

DROP TABLE IF EXISTS `application_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institution_name` varchar(255) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `mobile_phone` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(10) DEFAULT NULL,
  `turnstile_site_key` varchar(255) DEFAULT NULL,
  `turnstile_secret_key` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `payment_method` text,
  `registration_terms` text,
  `allow_multiple_registration` tinyint(1) DEFAULT '0',
  `max_registrations` tinyint unsigned DEFAULT '3',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_settings`
--

LOCK TABLES `application_settings` WRITE;
/*!40000 ALTER TABLE `application_settings` DISABLE KEYS */;
INSERT INTO `application_settings` VALUES (1,'Nama Instansi',NULL,'Jl. Pendidikan No. 1 Kel. Karang Raharja, Kec. Cikarang Utara Kab. Bekasi, Jawa Barat','info@sekolahku.web.id','+62-21 1234 5678','+62 813-1234-5678',NULL,'Asia/Jakarta',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,3,'2026-08-15 17:19:04',NULL);
/*!40000 ALTER TABLE `application_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `foundation_id` bigint unsigned DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `education_level` varchar(20) DEFAULT NULL COMMENT 'TK, RA, SD, MI, SMP, MTs, SMA, MA, SMK',
  `school_type` varchar(20) DEFAULT NULL,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_branches_unique_id` (`unique_id`),
  KEY `idx_branches_foundation_id` (`foundation_id`),
  KEY `idx_branches_is_deleted` (`is_deleted`),
  KEY `idx_branches_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_programs`
--

DROP TABLE IF EXISTS `education_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(30) DEFAULT NULL,
  `display_label` varchar(100) DEFAULT NULL,
  `description` text,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_education_programs_unique_id` (`unique_id`),
  KEY `idx_education_programs_branch_id` (`branch_id`),
  KEY `idx_education_programs_is_deleted` (`is_deleted`),
  KEY `idx_education_programs_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_programs`
--

LOCK TABLES `education_programs` WRITE;
/*!40000 ALTER TABLE `education_programs` DISABLE KEYS */;
/*!40000 ALTER TABLE `education_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_tracks`
--

DROP TABLE IF EXISTS `education_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_tracks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `is_default` tinyint(1) DEFAULT '0',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_education_tracks_unique_id` (`unique_id`),
  KEY `idx_education_tracks_branch_id` (`branch_id`),
  KEY `idx_education_tracks_is_deleted` (`is_deleted`),
  KEY `idx_education_tracks_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_tracks`
--

LOCK TABLES `education_tracks` WRITE;
/*!40000 ALTER TABLE `education_tracks` DISABLE KEYS */;
/*!40000 ALTER TABLE `education_tracks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entry_grades`
--

DROP TABLE IF EXISTS `entry_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entry_grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `grade_number` int DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entry_grades_unique_id` (`unique_id`),
  KEY `idx_entry_grades_branch_id` (`branch_id`),
  KEY `idx_entry_grades_is_deleted` (`is_deleted`),
  KEY `idx_entry_grades_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entry_grades`
--

LOCK TABLES `entry_grades` WRITE;
/*!40000 ALTER TABLE `entry_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `entry_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foundations`
--

DROP TABLE IF EXISTS `foundations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foundations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_foundations_unique_id` (`unique_id`),
  KEY `idx_foundations_is_deleted` (`is_deleted`),
  KEY `idx_foundations_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foundations`
--

LOCK TABLES `foundations` WRITE;
/*!40000 ALTER TABLE `foundations` DISABLE KEYS */;
/*!40000 ALTER TABLE `foundations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_document_requirements`
--

DROP TABLE IF EXISTS `general_document_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_document_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL DEFAULT '0',
  `document_id` bigint unsigned NOT NULL DEFAULT '0',
  `required` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_general_doc_req_unique_id` (`unique_id`),
  KEY `idx_general_doc_req_academic_year_id` (`academic_year_id`),
  KEY `idx_general_doc_req_document_id` (`document_id`),
  KEY `idx_general_doc_req_is_deleted` (`is_deleted`),
  KEY `idx_general_doc_req_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_document_requirements`
--

LOCK TABLES `general_document_requirements` WRITE;
/*!40000 ALTER TABLE `general_document_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_document_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_entrance_exam_scores`
--

DROP TABLE IF EXISTS `general_entrance_exam_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_entrance_exam_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL DEFAULT '0',
  `subject_id` bigint unsigned NOT NULL DEFAULT '0',
  `threshold_score` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_general_exam_score_unique_id` (`unique_id`),
  KEY `idx_general_exam_score_academic_year_id` (`academic_year_id`),
  KEY `idx_general_exam_score_subject_id` (`subject_id`),
  KEY `idx_general_exam_score_is_deleted` (`is_deleted`),
  KEY `idx_general_exam_score_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_entrance_exam_scores`
--

LOCK TABLES `general_entrance_exam_scores` WRITE;
/*!40000 ALTER TABLE `general_entrance_exam_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_entrance_exam_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_subject_grade_requirements`
--

DROP TABLE IF EXISTS `general_subject_grade_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_subject_grade_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL DEFAULT '0',
  `subject_id` bigint unsigned NOT NULL DEFAULT '0',
  `threshold_score` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_general_subj_grade_unique_id` (`unique_id`),
  KEY `idx_general_subj_grade_academic_year_id` (`academic_year_id`),
  KEY `idx_general_subj_grade_subject_id` (`subject_id`),
  KEY `idx_general_subj_grade_is_deleted` (`is_deleted`),
  KEY `idx_general_subj_grade_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_subject_grade_requirements`
--

LOCK TABLES `general_subject_grade_requirements` WRITE;
/*!40000 ALTER TABLE `general_subject_grade_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_subject_grade_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `identity` varchar(255) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_timestamp` (`attempt_time`),
  KEY `idx_login_attempts_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_document_requirements`
--

DROP TABLE IF EXISTS `offering_document_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offering_document_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `admission_offering_id` bigint unsigned NOT NULL DEFAULT '0',
  `document_id` bigint unsigned NOT NULL DEFAULT '0',
  `required` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_offering_doc_req_unique_id` (`unique_id`),
  KEY `idx_offering_doc_req_offering_id` (`admission_offering_id`),
  KEY `idx_offering_doc_req_document_id` (`document_id`),
  KEY `idx_offering_doc_req_is_deleted` (`is_deleted`),
  KEY `idx_offering_doc_req_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_document_requirements`
--

LOCK TABLES `offering_document_requirements` WRITE;
/*!40000 ALTER TABLE `offering_document_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_document_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_entrance_exam_scores`
--

DROP TABLE IF EXISTS `offering_entrance_exam_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offering_entrance_exam_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `admission_offering_id` bigint unsigned NOT NULL DEFAULT '0',
  `subject_id` bigint unsigned NOT NULL DEFAULT '0',
  `threshold_score` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_offering_exam_score_unique_id` (`unique_id`),
  KEY `idx_offering_exam_score_offering_id` (`admission_offering_id`),
  KEY `idx_offering_exam_score_subject_id` (`subject_id`),
  KEY `idx_offering_exam_score_is_deleted` (`is_deleted`),
  KEY `idx_offering_exam_score_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_entrance_exam_scores`
--

LOCK TABLES `offering_entrance_exam_scores` WRITE;
/*!40000 ALTER TABLE `offering_entrance_exam_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_entrance_exam_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_subject_grade_requirements`
--

DROP TABLE IF EXISTS `offering_subject_grade_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offering_subject_grade_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `admission_offering_id` bigint unsigned NOT NULL DEFAULT '0',
  `subject_id` bigint unsigned NOT NULL DEFAULT '0',
  `threshold_score` decimal(10,2) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_offering_subj_grade_unique_id` (`unique_id`),
  KEY `idx_offering_subj_grade_offering_id` (`admission_offering_id`),
  KEY `idx_offering_subj_grade_subject_id` (`subject_id`),
  KEY `idx_offering_subj_grade_is_deleted` (`is_deleted`),
  KEY `idx_offering_subj_grade_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_subject_grade_requirements`
--

LOCK TABLES `offering_subject_grade_requirements` WRITE;
/*!40000 ALTER TABLE `offering_subject_grade_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_subject_grade_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `previous_schools`
--

DROP TABLE IF EXISTS `previous_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `previous_schools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_address` text,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_previous_schools_unique_id` (`unique_id`),
  KEY `idx_previous_schools_is_deleted` (`is_deleted`),
  KEY `idx_previous_schools_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_schools`
--

LOCK TABLES `previous_schools` WRITE;
/*!40000 ALTER TABLE `previous_schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `previous_schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration_documents`
--

DROP TABLE IF EXISTS `registration_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reg_doc_reg_id` (`registration_id`),
  KEY `idx_reg_doc_document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration_documents`
--

LOCK TABLES `registration_documents` WRITE;
/*!40000 ALTER TABLE `registration_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `registration_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration_exam_scores`
--

DROP TABLE IF EXISTS `registration_exam_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_exam_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `score` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reg_exam_reg_id` (`registration_id`),
  KEY `idx_reg_exam_subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration_exam_scores`
--

LOCK TABLES `registration_exam_scores` WRITE;
/*!40000 ALTER TABLE `registration_exam_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `registration_exam_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration_subject_grades`
--

DROP TABLE IF EXISTS `registration_subject_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_subject_grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `threshold_score` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reg_subj_grade_reg_id` (`registration_id`),
  KEY `idx_reg_subj_grade_subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration_subject_grades`
--

LOCK TABLES `registration_subject_grades` WRITE;
/*!40000 ALTER TABLE `registration_subject_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `registration_subject_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `student_id` bigint unsigned NOT NULL DEFAULT '0',
  `admission_offering_id` bigint unsigned NOT NULL DEFAULT '0',
  `admission_period_id` bigint unsigned DEFAULT '0',
  `admission_year` int DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `submitted` tinyint(1) DEFAULT '0',
  `submitted_at` datetime DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT '0',
  `selection_status` varchar(30) DEFAULT '0',
  `selection_notes` text,
  `admission_fee` decimal(15,2) DEFAULT NULL,
  `transfer_proof` varchar(255) DEFAULT NULL,
  `transfer_proof_uploaded_at` datetime DEFAULT NULL,
  `transfer_proof_verified` tinyint(1) DEFAULT '0',
  `transfer_proof_verified_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `restored_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `restored_by` bigint unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_registrations_unique_id` (`unique_id`),
  KEY `idx_registrations_student_id` (`student_id`),
  KEY `idx_registrations_offering_id` (`admission_offering_id`),
  KEY `idx_registrations_period_id` (`admission_period_id`),
  KEY `idx_registrations_admission_year` (`admission_year`),
  KEY `idx_registrations_selection_status` (`selection_status`),
  KEY `idx_registrations_is_deleted` (`is_deleted`),
  KEY `idx_registrations_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subjects_unique_id` (`unique_id`),
  KEY `idx_subjects_is_deleted` (`is_deleted`),
  KEY `idx_subjects_modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(120) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `birth_place_country_id` varchar(2) DEFAULT NULL,
  `birth_place_province_id` varchar(100) DEFAULT NULL,
  `birth_place_district_id` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `npwp` varchar(30) DEFAULT NULL,
  `citizenship` varchar(2) DEFAULT NULL,
  `religion_id` tinyint unsigned DEFAULT NULL,
  `kps_receiver` tinyint(1) DEFAULT '0',
  `kps` varchar(50) DEFAULT NULL,
  `mobile_phone` varchar(50) DEFAULT NULL,
  `address` text,
  `address_country_id` varchar(2) DEFAULT NULL,
  `address_province_id` varchar(100) DEFAULT NULL,
  `address_district_id` varchar(100) DEFAULT NULL,
  `address_subdistrict_id` varchar(100) DEFAULT NULL,
  `street_address` text,
  `village` varchar(100) DEFAULT NULL,
  `rt` varchar(5) DEFAULT NULL,
  `rw` varchar(5) DEFAULT NULL,
  `urban_village` varchar(100) DEFAULT NULL,
  `district_id` varchar(100) DEFAULT NULL,
  `residence_id` tinyint unsigned DEFAULT NULL,
  `transportation_id` tinyint unsigned DEFAULT NULL,
  `post_code` varchar(10) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `telegram` varchar(50) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `tiktok` varchar(100) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `is_student` tinyint(1) DEFAULT '0',
  `is_operator` tinyint(1) DEFAULT '0',
  `account_verified` tinyint(1) DEFAULT '0',
  `account_verified_at` datetime DEFAULT NULL,
  `account_verified_by` bigint unsigned DEFAULT '0',
  `last_logged_in` int unsigned DEFAULT '0',
  `last_logged_in_ip` varchar(50) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_token_request_at` datetime DEFAULT NULL,
  `selector` varchar(255) DEFAULT '',
  `hashed_validator` varchar(255) DEFAULT '',
  `expiry` datetime DEFAULT NULL,
  `nisn` varchar(50) DEFAULT NULL,
  `npsn` varchar(50) DEFAULT NULL,
  `previous_school_id` bigint unsigned DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `school_major` varchar(255) DEFAULT NULL,
  `school_graduation_year` varchar(4) DEFAULT NULL,
  `school_address` text,
  `father_nik` varchar(20) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_birth_date` date DEFAULT NULL,
  `father_education_id` tinyint unsigned DEFAULT NULL,
  `father_income_id` tinyint unsigned DEFAULT NULL,
  `father_occupation_id` tinyint unsigned DEFAULT NULL,
  `mother_nik` varchar(20) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_birth_date` date DEFAULT NULL,
  `mother_education_id` tinyint unsigned DEFAULT NULL,
  `mother_income_id` tinyint unsigned DEFAULT NULL,
  `mother_occupation_id` tinyint unsigned DEFAULT NULL,
  `guardian_nik` varchar(20) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_birth_date` date DEFAULT NULL,
  `guardian_education_id` tinyint unsigned DEFAULT NULL,
  `guardian_occupation_id` tinyint unsigned DEFAULT NULL,
  `guardian_income_id` tinyint unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `restored_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` bigint unsigned DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT '0',
  `deleted_by` bigint unsigned DEFAULT '0',
  `restored_by` bigint unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  UNIQUE KEY `uk_users_unique_id` (`unique_id`),
  KEY `idx_users_is_deleted` (`is_deleted`),
  KEY `idx_users_modified_at` (`modified_at`),
  KEY `idx_users_is_student` (`is_student`),
  KEY `idx_users_is_admin` (`is_admin`),
  KEY `idx_users_is_operator` (`is_operator`),
  KEY `idx_users_religion_id` (`religion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'user:admin@sekolah.sch.id','admin@sekolah.sch.id','$2y$12$b27qocjFCtH0fqrJdPZOwuC60rZjyZ64.d2rRvHHTgw0.U/cl7fDa','Administrator',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'Jl. Pendidikan No. 1 Kel. Karang Raharja, Kec. Cikarang Utara Kab. Bekasi, Jawa Barat 17550 Indonesia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,1,NULL,0,0,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2026-08-15 17:19:03',NULL,NULL,NULL,'2026-08-15 17:19:03',0,0,0,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'spmb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-15 17:19:04
