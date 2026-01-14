-- Master Schema Document
-- This file contains the CREATE TABLE statements for master entities
-- used in the application: branches, departments, designations,
-- employment_types, countries, and employees.

/* ============================================================
   1. branches
   ============================================================ */
CREATE TABLE `branches` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`          VARCHAR(255)    NOT NULL,
  `name`          VARCHAR(255)    NOT NULL,
  `location`      VARCHAR(255)    NULL,
  `contact_person` VARCHAR(255)   NULL,
  `contact_phone` VARCHAR(255)    NULL,
  `status`        VARCHAR(255)    NOT NULL DEFAULT 'active',
  `notes`         TEXT            NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ============================================================
   2. departments
   ============================================================ */
CREATE TABLE `departments` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id`  BIGINT UNSIGNED NOT NULL,
  `code`       VARCHAR(255)    NOT NULL,
  `name`       VARCHAR(255)    NOT NULL,
  `status`     VARCHAR(255)    NOT NULL DEFAULT 'active',
  `notes`      TEXT            NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `departments_branch_id_foreign` (`branch_id`),
  CONSTRAINT `departments_branch_id_foreign`
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `departments_branch_id_code_unique` (`branch_id`,`code`),
  UNIQUE KEY `departments_branch_id_name_unique` (`branch_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ============================================================
   3. designations
   ============================================================ */
CREATE TABLE `designations` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`       VARCHAR(255)    NOT NULL,
  `title`      VARCHAR(255)    NOT NULL,
  `status`     VARCHAR(255)    NOT NULL DEFAULT 'active',
  `notes`      TEXT            NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `designations_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ============================================================
   4. employment_types
   ============================================================ */
CREATE TABLE `employment_types` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`       VARCHAR(255)    NOT NULL,
  `name`       VARCHAR(255)    NOT NULL,
  `status`     VARCHAR(255)    NOT NULL DEFAULT 'active',
  `notes`      TEXT            NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employment_types_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ============================================================
   5. countries
   ============================================================ */
CREATE TABLE `countries` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`       VARCHAR(5)      NOT NULL,
  `name`       VARCHAR(255)    NOT NULL,
  `status`     VARCHAR(255)    NOT NULL DEFAULT 'active',
  `notes`      TEXT            NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_code_unique` (`code`),
  UNIQUE KEY `countries_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* Optional seed data for countries */
INSERT INTO `countries` (`code`, `name`, `status`) VALUES
('IN', 'India', 'active'),
('US', 'United States', 'active'),
('CA', 'Canada', 'active'),
('UK', 'United Kingdom', 'active'),
('AU', 'Australia', 'active');


/* ============================================================
   6. employees
   ============================================================ */
CREATE TABLE `employees` (
  `id`                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_code`            VARCHAR(255)    NOT NULL,
  `name`                     VARCHAR(255)    NOT NULL,
  `email`                    VARCHAR(255)    NULL,
  `personal_email`           VARCHAR(255)    NULL,
  `phone`                    VARCHAR(255)    NULL,

  `designation`              VARCHAR(255)    NULL,
  `department`               VARCHAR(255)    NULL,
  `employment_type`          VARCHAR(255)    NULL,

  `date_of_joining`          DATE           NULL,
  `status`                   VARCHAR(255)   NOT NULL DEFAULT 'active',
  `work_location`            VARCHAR(255)   NULL,

  `date_of_birth`            DATE           NULL,
  `blood_group`              VARCHAR(255)   NULL,
  `marital_status`           VARCHAR(255)   NULL,
  `spouse_name`              VARCHAR(255)   NULL,
  `number_of_dependents`     INT UNSIGNED   NULL,
  `emergency_contact_name`   VARCHAR(255)   NULL,
  `emergency_contact_relation` VARCHAR(255) NULL,
  `emergency_contact_phone`  VARCHAR(255)   NULL,

  `passport_number`          VARCHAR(255)   NULL,
  `passport_expiry`          DATE           NULL,
  `aadhaar_number`           VARCHAR(255)   NULL,
  `pan_number`               VARCHAR(255)   NULL,

  `highest_qualification`    VARCHAR(255)   NULL,
  `institution_name`         VARCHAR(255)   NULL,
  `field_of_study`           VARCHAR(255)   NULL,
  `graduation_year`          VARCHAR(255)   NULL,
  `grade`                    VARCHAR(255)   NULL,

  `previous_employer`        VARCHAR(255)   NULL,
  `previous_job_title`       VARCHAR(255)   NULL,
  `experience_years`         DECIMAL(5,2)   NULL,
  `skills`                   TEXT           NULL,

  `bank_name`                VARCHAR(255)   NULL,
  `bank_account_number`      VARCHAR(255)   NULL,
  `ifsc_code`                VARCHAR(255)   NULL,
  `uan_number`               VARCHAR(255)   NULL,
  `pf_number`                VARCHAR(255)   NULL,
  `esi_number`               VARCHAR(255)   NULL,
  `insurance_provider`       VARCHAR(255)   NULL,
  `insurance_policy_number`  VARCHAR(255)   NULL,
  `insurance_valid_till`     DATE           NULL,
  `medical_conditions`       TEXT           NULL,
  `allergies`                TEXT           NULL,

  `branch_id`                BIGINT UNSIGNED NULL,
  `department_id`            BIGINT UNSIGNED NULL,
  `designation_id`           BIGINT UNSIGNED NULL,
  `employment_type_id`       BIGINT UNSIGNED NULL,
  `state_id`                 BIGINT UNSIGNED NULL,
  `city_id`                  BIGINT UNSIGNED NULL,
  `country_id`               BIGINT UNSIGNED NULL,

  `address_line`             VARCHAR(255)   NULL,
  `city`                     VARCHAR(255)   NULL,
  `state`                    VARCHAR(255)   NULL,
  `country`                  VARCHAR(255)   NULL,
  `postal_code`              VARCHAR(255)   NULL,

  `notes`                    TEXT           NULL,
  `created_at`               TIMESTAMP NULL,
  `updated_at`               TIMESTAMP NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  UNIQUE KEY `employees_email_unique` (`email`),

  KEY `employees_branch_department_index` (`branch_id`,`department_id`),
  KEY `employees_designation_id_foreign` (`designation_id`),
  KEY `employees_employment_type_id_foreign` (`employment_type_id`),
  KEY `employees_state_id_foreign` (`state_id`),
  KEY `employees_city_id_foreign` (`city_id`),
  KEY `employees_country_id_foreign` (`country_id`),

  CONSTRAINT `employees_branch_id_foreign`
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_department_id_foreign`
    FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_designation_id_foreign`
    FOREIGN KEY (`designation_id`) REFERENCES `designations`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_employment_type_id_foreign`
    FOREIGN KEY (`employment_type_id`) REFERENCES `employment_types`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_state_id_foreign`
    FOREIGN KEY (`state_id`) REFERENCES `states`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_city_id_foreign`
    FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_country_id_foreign`
    FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


