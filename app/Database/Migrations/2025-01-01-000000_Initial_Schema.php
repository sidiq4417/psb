<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitialSchema extends Migration
{
    private array $tables = [
        'foundations',
        'branches',
        'academic_years',
        'entry_grades',
        'education_tracks',
        'education_programs',
        'subjects',
        'admission_documents',
        'admission_pathways',
        'applicant_types',
        'previous_schools',
        'admission_periods',
        'admission_offerings',
        'offering_document_requirements',
        'offering_subject_grade_requirements',
        'offering_entrance_exam_scores',
        'general_document_requirements',
        'general_subject_grade_requirements',
        'general_entrance_exam_scores',
        'application_settings',
        'users',
        'registrations',
        'registration_documents',
        'registration_subject_grades',
        'registration_exam_scores',
        'sessions',
        'login_attempts',
    ];

    public function up(): void
    {
        $this->createFoundations();
        $this->createBranches();
        $this->createAcademicYears();
        $this->createEntryGrades();
        $this->createEducationTracks();
        $this->createEducationPrograms();
        $this->createSubjects();
        $this->createAdmissionDocuments();
        $this->createAdmissionPathways();
        $this->createApplicantTypes();
        $this->createPreviousSchools();
        $this->createAdmissionPeriods();
        $this->createAdmissionOfferings();
        $this->createOfferingDocumentRequirements();
        $this->createOfferingSubjectGradeRequirements();
        $this->createOfferingEntranceExamScores();
        $this->createGeneralDocumentRequirements();
        $this->createGeneralSubjectGradeRequirements();
        $this->createGeneralEntranceExamScores();
        $this->createApplicationSettings();
        $this->createUsers();
        $this->createRegistrations();
        $this->createRegistrationDocuments();
        $this->createRegistrationSubjectGrades();
        $this->createRegistrationExamScores();
        $this->createSessions();
        $this->createLoginAttempts();
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            $this->forge->dropTable($table, true);
        }
    }

    private function createFoundations(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `foundations` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `code`        VARCHAR(50),
            `name`        VARCHAR(255) NOT NULL,
            `address`     TEXT,
            `phone`       VARCHAR(50),
            `email`       VARCHAR(255),
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_foundations_unique_id` (`unique_id`),
            KEY `idx_foundations_is_deleted` (`is_deleted`),
            KEY `idx_foundations_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createBranches(): void
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `branches` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `foundation_id` BIGINT UNSIGNED DEFAULT 0,
            `code`        VARCHAR(50),
            `name`        VARCHAR(255) NOT NULL,
            `education_level` VARCHAR(20) COMMENT 'TK, RA, SD, MI, SMP, MTs, SMA, MA, SMK',
            `school_type` VARCHAR(20),
            `address`     TEXT,
            `phone`       VARCHAR(50),
            `email`       VARCHAR(255),
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_branches_unique_id` (`unique_id`),
            KEY `idx_branches_foundation_id` (`foundation_id`),
            KEY `idx_branches_is_deleted` (`is_deleted`),
            KEY `idx_branches_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
    }

    private function createAcademicYears(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `academic_years` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `foundation_id` BIGINT UNSIGNED DEFAULT 0,
            `name`        VARCHAR(20) NOT NULL,
            `start_date`  DATE,
            `end_date`    DATE,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_academic_years_unique_id` (`unique_id`),
            KEY `idx_academic_years_foundation_id` (`foundation_id`),
            KEY `idx_academic_years_is_deleted` (`is_deleted`),
            KEY `idx_academic_years_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createEntryGrades(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `entry_grades` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `branch_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `code`        VARCHAR(50),
            `name`        VARCHAR(100) NOT NULL,
            `grade_number` INT,
            `sort_order`  INT,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_entry_grades_unique_id` (`unique_id`),
            KEY `idx_entry_grades_branch_id` (`branch_id`),
            KEY `idx_entry_grades_is_deleted` (`is_deleted`),
            KEY `idx_entry_grades_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createEducationTracks(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `education_tracks` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `branch_id`   BIGINT UNSIGNED DEFAULT 0,
            `code`        VARCHAR(50),
            `name`        VARCHAR(100) NOT NULL,
            `description` TEXT,
            `is_default`  TINYINT(1) DEFAULT 0,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_education_tracks_unique_id` (`unique_id`),
            KEY `idx_education_tracks_branch_id` (`branch_id`),
            KEY `idx_education_tracks_is_deleted` (`is_deleted`),
            KEY `idx_education_tracks_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createEducationPrograms(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `education_programs` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `branch_id`   BIGINT UNSIGNED DEFAULT 0,
            `code`        VARCHAR(50),
            `name`        VARCHAR(255) NOT NULL,
            `category`    VARCHAR(30),
            `display_label` VARCHAR(100),
            `description` TEXT,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_education_programs_unique_id` (`unique_id`),
            KEY `idx_education_programs_branch_id` (`branch_id`),
            KEY `idx_education_programs_is_deleted` (`is_deleted`),
            KEY `idx_education_programs_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createSubjects(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `subjects` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `name`        VARCHAR(255) NOT NULL,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_subjects_unique_id` (`unique_id`),
            KEY `idx_subjects_is_deleted` (`is_deleted`),
            KEY `idx_subjects_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createAdmissionDocuments(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `admission_documents` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `name`        VARCHAR(255) NOT NULL,
            `max_size`    INT,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_admission_documents_unique_id` (`unique_id`),
            KEY `idx_admission_documents_is_deleted` (`is_deleted`),
            KEY `idx_admission_documents_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createAdmissionPathways(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `admission_pathways` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `name`        VARCHAR(255) NOT NULL,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_admission_pathways_unique_id` (`unique_id`),
            KEY `idx_admission_pathways_is_deleted` (`is_deleted`),
            KEY `idx_admission_pathways_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createApplicantTypes(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `applicant_types` (
            `id`          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`   VARCHAR(120),
            `name`        VARCHAR(255) NOT NULL,
            `is_deleted`  TINYINT(1) DEFAULT 0,
            `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`  DATETIME,
            `created_by`  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`  BIGINT UNSIGNED DEFAULT 0,
            `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_applicant_types_unique_id` (`unique_id`),
            KEY `idx_applicant_types_is_deleted` (`is_deleted`),
            KEY `idx_applicant_types_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createPreviousSchools(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `previous_schools` (
            `id`             BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`      VARCHAR(120),
            `school_name`    VARCHAR(255) NOT NULL,
            `school_address` TEXT,
            `is_deleted`     TINYINT(1) DEFAULT 0,
            `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`     DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`     DATETIME,
            `created_by`     BIGINT UNSIGNED DEFAULT 0,
            `updated_by`     BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`     BIGINT UNSIGNED DEFAULT 0,
            `modified_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_previous_schools_unique_id` (`unique_id`),
            KEY `idx_previous_schools_is_deleted` (`is_deleted`),
            KEY `idx_previous_schools_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createAdmissionPeriods(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `admission_periods` (
            `id`                    BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`             VARCHAR(120),
            `foundation_id`         BIGINT UNSIGNED DEFAULT 0,
            `academic_year_id`      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `code`                  VARCHAR(50),
            `name`                  VARCHAR(255) NOT NULL,
            `registration_start_at` DATE,
            `registration_end_at`   DATE,
            `announcement_at`       DATE,
            `registration_fee`      DECIMAL(15,2),
            `is_deleted`            TINYINT(1) DEFAULT 0,
            `created_at`            DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`            DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`            DATETIME,
            `created_by`            BIGINT UNSIGNED DEFAULT 0,
            `updated_by`            BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`            BIGINT UNSIGNED DEFAULT 0,
            `modified_at`           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_admission_periods_unique_id` (`unique_id`),
            KEY `idx_admission_periods_academic_year_id` (`academic_year_id`),
            KEY `idx_admission_periods_foundation_id` (`foundation_id`),
            KEY `idx_admission_periods_is_deleted` (`is_deleted`),
            KEY `idx_admission_periods_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createAdmissionOfferings(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `admission_offerings` (
            `id`                   BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`            VARCHAR(120),
            `admission_period_id`  BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `branch_id`            BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `entry_grade_id`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `education_track_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `education_program_id` BIGINT UNSIGNED DEFAULT 0,
            `applicant_type_id`    BIGINT UNSIGNED DEFAULT 0,
            `admission_pathway_id` BIGINT UNSIGNED DEFAULT 0,
            `quota`                INT,
            `sort_order`           INT,
            `is_deleted`           TINYINT(1) DEFAULT 0,
            `created_at`           DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`           DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`           DATETIME,
            `created_by`           BIGINT UNSIGNED DEFAULT 0,
            `updated_by`           BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`           BIGINT UNSIGNED DEFAULT 0,
            `modified_at`          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createOfferingDocumentRequirements(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `offering_document_requirements` (
            `id`                    BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`             VARCHAR(120),
            `admission_offering_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `document_id`           BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `required`              TINYINT(1) DEFAULT 1,
            `is_deleted`            TINYINT(1) DEFAULT 0,
            `created_at`            DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`            DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`            DATETIME,
            `created_by`            BIGINT UNSIGNED DEFAULT 0,
            `updated_by`            BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`            BIGINT UNSIGNED DEFAULT 0,
            `modified_at`           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_offering_doc_req_unique_id` (`unique_id`),
            KEY `idx_offering_doc_req_offering_id` (`admission_offering_id`),
            KEY `idx_offering_doc_req_document_id` (`document_id`),
            KEY `idx_offering_doc_req_is_deleted` (`is_deleted`),
            KEY `idx_offering_doc_req_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createOfferingSubjectGradeRequirements(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `offering_subject_grade_requirements` (
            `id`                    BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`             VARCHAR(120),
            `admission_offering_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `subject_id`            BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `threshold_score`       DECIMAL(10,2),
            `is_deleted`            TINYINT(1) DEFAULT 0,
            `created_at`            DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`            DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`            DATETIME,
            `created_by`            BIGINT UNSIGNED DEFAULT 0,
            `updated_by`            BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`            BIGINT UNSIGNED DEFAULT 0,
            `modified_at`           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_offering_subj_grade_unique_id` (`unique_id`),
            KEY `idx_offering_subj_grade_offering_id` (`admission_offering_id`),
            KEY `idx_offering_subj_grade_subject_id` (`subject_id`),
            KEY `idx_offering_subj_grade_is_deleted` (`is_deleted`),
            KEY `idx_offering_subj_grade_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createOfferingEntranceExamScores(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `offering_entrance_exam_scores` (
            `id`                    BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`             VARCHAR(120),
            `admission_offering_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `subject_id`            BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `threshold_score`       INT,
            `is_deleted`            TINYINT(1) DEFAULT 0,
            `created_at`            DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`            DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`            DATETIME,
            `created_by`            BIGINT UNSIGNED DEFAULT 0,
            `updated_by`            BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`            BIGINT UNSIGNED DEFAULT 0,
            `modified_at`           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_offering_exam_score_unique_id` (`unique_id`),
            KEY `idx_offering_exam_score_offering_id` (`admission_offering_id`),
            KEY `idx_offering_exam_score_subject_id` (`subject_id`),
            KEY `idx_offering_exam_score_is_deleted` (`is_deleted`),
            KEY `idx_offering_exam_score_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createGeneralDocumentRequirements(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `general_document_requirements` (
            `id`               BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`        VARCHAR(120),
            `academic_year_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `document_id`      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `required`         TINYINT(1) DEFAULT 1,
            `is_deleted`       TINYINT(1) DEFAULT 0,
            `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`       DATETIME,
            `created_by`       BIGINT UNSIGNED DEFAULT 0,
            `updated_by`       BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`       BIGINT UNSIGNED DEFAULT 0,
            `modified_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_general_doc_req_unique_id` (`unique_id`),
            KEY `idx_general_doc_req_academic_year_id` (`academic_year_id`),
            KEY `idx_general_doc_req_document_id` (`document_id`),
            KEY `idx_general_doc_req_is_deleted` (`is_deleted`),
            KEY `idx_general_doc_req_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createGeneralSubjectGradeRequirements(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `general_subject_grade_requirements` (
            `id`               BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`        VARCHAR(120),
            `academic_year_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `subject_id`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `threshold_score`  INT,
            `is_deleted`       TINYINT(1) DEFAULT 0,
            `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`       DATETIME,
            `created_by`       BIGINT UNSIGNED DEFAULT 0,
            `updated_by`       BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`       BIGINT UNSIGNED DEFAULT 0,
            `modified_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_general_subj_grade_unique_id` (`unique_id`),
            KEY `idx_general_subj_grade_academic_year_id` (`academic_year_id`),
            KEY `idx_general_subj_grade_subject_id` (`subject_id`),
            KEY `idx_general_subj_grade_is_deleted` (`is_deleted`),
            KEY `idx_general_subj_grade_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createGeneralEntranceExamScores(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `general_entrance_exam_scores` (
            `id`               BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`        VARCHAR(120),
            `academic_year_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `subject_id`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `threshold_score`  INT,
            `is_deleted`       TINYINT(1) DEFAULT 0,
            `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`       DATETIME,
            `created_by`       BIGINT UNSIGNED DEFAULT 0,
            `updated_by`       BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`       BIGINT UNSIGNED DEFAULT 0,
            `modified_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_general_exam_score_unique_id` (`unique_id`),
            KEY `idx_general_exam_score_academic_year_id` (`academic_year_id`),
            KEY `idx_general_exam_score_subject_id` (`subject_id`),
            KEY `idx_general_exam_score_is_deleted` (`is_deleted`),
            KEY `idx_general_exam_score_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createApplicationSettings(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `application_settings` (
            `id`                        BIGINT UNSIGNED AUTO_INCREMENT,
            `institution_name`          VARCHAR(255),
            `tagline`                   VARCHAR(255),
            `address`                   TEXT,
            `email`                     VARCHAR(255),
            `telephone`                 VARCHAR(50),
            `mobile_phone`              VARCHAR(50),
            `website`                   VARCHAR(255),
            `timezone`                  VARCHAR(50),
            `smtp_host`                 VARCHAR(255),
            `smtp_user`                 VARCHAR(255),
            `smtp_pass`                 VARCHAR(255),
            `smtp_port`                 VARCHAR(10),
            `turnstile_site_key`        VARCHAR(255),
            `turnstile_secret_key`      VARCHAR(255),
            `logo`                      VARCHAR(255),
            `favicon`                   VARCHAR(255),
            `payment_method`            TEXT,
            `registration_terms`        TEXT,
            `allow_multiple_registration` TINYINT(1) DEFAULT 0,
            `max_registrations`         TINYINT UNSIGNED DEFAULT 3,
            `created_at`                DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`                DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createUsers(): void
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `users` (
            `id`                          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`                   VARCHAR(120),
            `email`                       VARCHAR(255) NOT NULL,
            `password`                    VARCHAR(255) NOT NULL,
            `full_name`                   VARCHAR(255),
            `gender`                      VARCHAR(1),
            `birth_place`                 VARCHAR(100),
            `birth_place_country_id`      VARCHAR(2),
            `birth_place_province_id`     VARCHAR(100),
            `birth_place_district_id`     VARCHAR(100),
            `birth_date`                  DATE,
            `nik`                         VARCHAR(20),
            `npwp`                        VARCHAR(30),
            `citizenship`                 VARCHAR(2),
            `religion_id`                 TINYINT UNSIGNED,
            `kps_receiver`                TINYINT(1) DEFAULT 0,
            `kps`                         VARCHAR(50),
            `mobile_phone`                VARCHAR(50),
            `address`                     TEXT,
            `address_country_id`          VARCHAR(2),
            `address_province_id`         VARCHAR(100),
            `address_district_id`         VARCHAR(100),
            `address_subdistrict_id`      VARCHAR(100),
            `street_address`              TEXT,
            `village`                     VARCHAR(100),
            `rt`                          VARCHAR(5),
            `rw`                          VARCHAR(5),
            `urban_village`               VARCHAR(100),
            `district_id`                 VARCHAR(100),
            `residence_id`                TINYINT UNSIGNED,
            `transportation_id`           TINYINT UNSIGNED,
            `post_code`                   VARCHAR(10),
            `telephone`                   VARCHAR(50),
            `whatsapp`                    VARCHAR(50),
            `telegram`                    VARCHAR(50),
            `facebook`                    VARCHAR(100),
            `twitter`                     VARCHAR(100),
            `instagram`                   VARCHAR(100),
            `tiktok`                      VARCHAR(100),
            `is_admin`                    TINYINT(1) DEFAULT 0,
            `is_student`                  TINYINT(1) DEFAULT 0,
            `is_operator`                 TINYINT(1) DEFAULT 0,
            `account_verified`            TINYINT(1) DEFAULT 0,
            `account_verified_at`         DATETIME,
            `account_verified_by`         BIGINT UNSIGNED DEFAULT 0,
            `last_logged_in`              INT UNSIGNED DEFAULT 0,
            `last_logged_in_ip`           VARCHAR(50),
            `password_reset_token`        VARCHAR(255),
            `password_reset_token_request_at` DATETIME,
            `selector`                    VARCHAR(255) DEFAULT '',
            `hashed_validator`            VARCHAR(255) DEFAULT '',
            `expiry`                      DATETIME,
            `nisn`                        VARCHAR(50),
            `npsn`                        VARCHAR(50),
            `previous_school_id`          BIGINT UNSIGNED,
            `school_name`                 VARCHAR(255),
            `school_major`                VARCHAR(255),
            `school_graduation_year`      VARCHAR(4),
            `school_address`              TEXT,
            `father_nik`                  VARCHAR(20),
            `father_name`                 VARCHAR(255),
            `father_birth_date`           DATE,
            `father_education_id`         TINYINT UNSIGNED,
            `father_income_id`            TINYINT UNSIGNED,
            `father_occupation_id`        TINYINT UNSIGNED,
            `mother_nik`                  VARCHAR(20),
            `mother_name`                 VARCHAR(255),
            `mother_birth_date`           DATE,
            `mother_education_id`         TINYINT UNSIGNED,
            `mother_income_id`            TINYINT UNSIGNED,
            `mother_occupation_id`        TINYINT UNSIGNED,
            `guardian_nik`                VARCHAR(20),
            `guardian_name`               VARCHAR(255),
            `guardian_birth_date`         DATE,
            `guardian_education_id`       TINYINT UNSIGNED,
            `guardian_occupation_id`      TINYINT UNSIGNED,
            `guardian_income_id`          TINYINT UNSIGNED,
            `is_deleted`                  TINYINT(1) DEFAULT 0,
            `created_at`                  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`                  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`                  DATETIME,
            `restored_at`                 DATETIME,
            `modified_at`                 DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `created_by`                  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`                  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`                  BIGINT UNSIGNED DEFAULT 0,
            `restored_by`                 BIGINT UNSIGNED DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_users_email` (`email`),
            UNIQUE KEY `uk_users_unique_id` (`unique_id`),
            KEY `idx_users_is_deleted` (`is_deleted`),
            KEY `idx_users_modified_at` (`modified_at`),
            KEY `idx_users_is_student` (`is_student`),
            KEY `idx_users_is_admin` (`is_admin`),
            KEY `idx_users_is_operator` (`is_operator`),
            KEY `idx_users_religion_id` (`religion_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
    }

    private function createRegistrations(): void
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `registrations` (
            `id`                          BIGINT UNSIGNED AUTO_INCREMENT,
            `unique_id`                   VARCHAR(120),
            `student_id`                  BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `admission_offering_id`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `admission_period_id`         BIGINT UNSIGNED DEFAULT 0,
            `admission_year`              INT,
            `registration_number`         VARCHAR(100),
            `submitted`                   TINYINT(1) DEFAULT 0,
            `submitted_at`                DATETIME,
            `submitted_by`                BIGINT UNSIGNED DEFAULT 0,
            `selection_status`            VARCHAR(30) DEFAULT '0',
            `selection_notes`             TEXT,
            `admission_fee`               DECIMAL(15,2),
            `transfer_proof`              VARCHAR(255),
            `transfer_proof_uploaded_at`  DATETIME,
            `transfer_proof_verified`     TINYINT(1) DEFAULT 0,
            `transfer_proof_verified_at`  DATETIME,
            `is_deleted`                  TINYINT(1) DEFAULT 0,
            `created_at`                  DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`                  DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at`                  DATETIME,
            `restored_at`                 DATETIME,
            `modified_at`                 DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `created_by`                  BIGINT UNSIGNED DEFAULT 0,
            `updated_by`                  BIGINT UNSIGNED DEFAULT 0,
            `deleted_by`                  BIGINT UNSIGNED DEFAULT 0,
            `restored_by`                 BIGINT UNSIGNED DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_registrations_unique_id` (`unique_id`),
            KEY `idx_registrations_student_id` (`student_id`),
            KEY `idx_registrations_offering_id` (`admission_offering_id`),
            KEY `idx_registrations_period_id` (`admission_period_id`),
            KEY `idx_registrations_admission_year` (`admission_year`),
            KEY `idx_registrations_selection_status` (`selection_status`),
            KEY `idx_registrations_is_deleted` (`is_deleted`),
            KEY `idx_registrations_modified_at` (`modified_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
    }

    private function createRegistrationDocuments(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `registration_documents` (
            `id`              BIGINT UNSIGNED AUTO_INCREMENT,
            `registration_id` BIGINT UNSIGNED NOT NULL,
            `document_id`     BIGINT UNSIGNED NOT NULL,
            `file`            VARCHAR(255),
            `uploaded_at`     DATETIME,
            `verified`        TINYINT(1) DEFAULT 0,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_reg_doc_reg_id` (`registration_id`),
            KEY `idx_reg_doc_document_id` (`document_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createRegistrationSubjectGrades(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `registration_subject_grades` (
            `id`              BIGINT UNSIGNED AUTO_INCREMENT,
            `registration_id` BIGINT UNSIGNED NOT NULL,
            `subject_id`      BIGINT UNSIGNED NOT NULL,
            `score`           DECIMAL(10,2),
            `threshold_score` DECIMAL(10,2),
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_reg_subj_grade_reg_id` (`registration_id`),
            KEY `idx_reg_subj_grade_subject_id` (`subject_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createRegistrationExamScores(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `registration_exam_scores` (
            `id`              BIGINT UNSIGNED AUTO_INCREMENT,
            `registration_id` BIGINT UNSIGNED NOT NULL,
            `subject_id`      BIGINT UNSIGNED NOT NULL,
            `score`           INT,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_reg_exam_reg_id` (`registration_id`),
            KEY `idx_reg_exam_subject_id` (`subject_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createSessions(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `sessions` (
            `id`         VARCHAR(128) NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `timestamp`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `data`       BLOB NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_sessions_timestamp` (`timestamp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    private function createLoginAttempts(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `login_attempts` (
            `id`           BIGINT UNSIGNED AUTO_INCREMENT,
            `ip_address`   VARCHAR(45) NOT NULL,
            `identity`     VARCHAR(255) NOT NULL,
            `attempt_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_login_attempts_timestamp` (`attempt_time`),
            KEY `idx_login_attempts_ip` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }
}
