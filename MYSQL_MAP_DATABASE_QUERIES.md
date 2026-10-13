# MySQL Database Queries: Master DB & Tenant DB

This document contains all MySQL queries required for the **Map Tracking, Quota System, and Bring Your Own API (BYO-API)** architecture.

---

## 1. Architecture Summary

| Database Type | Database Connection / Name | Table Name | Purpose |
|---|---|---|---|
| **Master Database** | `mysql` (`workorio`) | `map_api_usages` | Central product quota tracking across all tenants (Google & Ola). |
| **Tenant Database** | `tenant_{id}` (e.g. `tenant_4`) | `map_settings` | Per-tenant BYO-API keys, provider priority, individual limits, and usage counters. |

---

## 2. MASTER DATABASE QUERIES (`workorio`)

> [!IMPORTANT]
> Run these queries **ONLY ONCE** on the Master Database (`workorio`).  
> **Do NOT run this table on tenant databases.**

### A. Create Table: `map_api_usages`
```sql
USE `workorio`;

CREATE TABLE IF NOT EXISTS `map_api_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Provider name: google, ola, osm',
  `request_count` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'API calls consumed in the current billing cycle',
  `billing_cycle_start` date NOT NULL COMMENT 'Start date of the billing month (e.g. 2026-10-01)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `map_api_usages_provider_billing_cycle_start_unique` (`provider`, `billing_cycle_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### B. Verify Master Table Structure
```sql
DESCRIBE `map_api_usages`;
```

### C. Useful Diagnostic & Maintenance Queries (Master DB)
```sql
-- 1. View all recorded usage across billing cycles
SELECT 
  `id`, 
  `provider`, 
  `request_count`, 
  `billing_cycle_start`, 
  `updated_at` 
FROM `map_api_usages` 
ORDER BY `billing_cycle_start` DESC, `provider` ASC;

-- 2. View current month usage only
SELECT * FROM `map_api_usages` 
WHERE `billing_cycle_start` = DATE_FORMAT(CURDATE(), '%Y-%m-01');

-- 3. Reset quota usage for current month (if testing or manually resetting)
UPDATE `map_api_usages` 
SET `request_count` = 0 
WHERE `billing_cycle_start` = DATE_FORMAT(CURDATE(), '%Y-%m-01');
```

---

## 3. TENANT DATABASE QUERIES (`tenant_4`, etc.)

> [!IMPORTANT]
> Run these queries on **each individual Tenant Database** (e.g. `tenant_4`).  
> **Do NOT run this table on the Master DB (`workorio`).**

### A. Fresh Setup (If `map_settings` does NOT exist yet)
```sql
USE `tenant_4`; -- Replace with your active tenant database name

CREATE TABLE IF NOT EXISTS `map_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_byo_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = Enable BYO custom API, 0 = Use product master quota',
  `preferred_provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google' COMMENT 'Primary attempt: google or ola',
  `google_api_key` text COLLATE utf8mb4_unicode_ci NULL COMMENT 'Tenant Google Maps API Key',
  `google_limit` bigint unsigned NOT NULL DEFAULT '10000' COMMENT 'Monthly request limit for Google Maps (0 = unlimited)',
  `google_usage` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'Current month requests used for Google Maps',
  `ola_api_key` text COLLATE utf8mb4_unicode_ci NULL COMMENT 'Tenant Ola Maps API Key',
  `ola_limit` bigint unsigned NOT NULL DEFAULT '50000' COMMENT 'Monthly request limit for Ola Maps (0 = unlimited)',
  `ola_usage` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'Current month requests used for Ola Maps',
  `billing_cycle_start` date NULL DEFAULT NULL COMMENT 'Current month cycle date (e.g. 2026-10-01) for auto-reset',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### B. Upgrade Existing Table (If `map_settings` was already created previously)
If you already created `map_settings` earlier with only the basic columns, run this `ALTER TABLE` query to add the quota and usage tracking columns:

```sql
USE `tenant_4`; -- Replace with your active tenant database name

ALTER TABLE `map_settings`
  ADD COLUMN IF NOT EXISTS `google_limit` bigint unsigned NOT NULL DEFAULT '10000' AFTER `google_api_key`,
  ADD COLUMN IF NOT EXISTS `google_usage` bigint unsigned NOT NULL DEFAULT '0' AFTER `google_limit`,
  ADD COLUMN IF NOT EXISTS `ola_limit` bigint unsigned NOT NULL DEFAULT '50000' AFTER `ola_api_key`,
  ADD COLUMN IF NOT EXISTS `ola_usage` bigint unsigned NOT NULL DEFAULT '0' AFTER `ola_limit`,
  ADD COLUMN IF NOT EXISTS `billing_cycle_start` date NULL DEFAULT NULL AFTER `ola_usage`;
```

### C. Verify Tenant Table Structure
```sql
DESCRIBE `map_settings`;
```

### D. Optional: Seed Initial Default Settings Row
If you want an initial configuration row populated immediately:

```sql
USE `tenant_4`;

INSERT INTO `map_settings` (
  `id`, 
  `is_byo_enabled`, 
  `preferred_provider`, 
  `google_api_key`, 
  `google_limit`, 
  `google_usage`, 
  `ola_api_key`, 
  `ola_limit`, 
  `ola_usage`, 
  `billing_cycle_start`, 
  `created_at`, 
  `updated_at`
) VALUES (
  1, 
  0, 
  'google', 
  NULL, 
  10000, 
  0, 
  NULL, 
  50000, 
  0, 
  DATE_FORMAT(CURDATE(), '%Y-%m-01'), 
  NOW(), 
  NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW();
```

### E. Useful Diagnostic & Maintenance Queries (Tenant DB)
```sql
-- 1. View current tenant settings and usage counts
SELECT * FROM `map_settings` LIMIT 1;

-- 2. Check if failover is currently triggered
SELECT 
  `preferred_provider`,
  `google_usage`, 
  `google_limit`, 
  CASE WHEN `google_usage` >= `google_limit` THEN 'EXHAUSTED' ELSE 'ACTIVE' END AS `google_status`,
  `ola_usage`, 
  `ola_limit`,
  CASE WHEN `ola_usage` >= `ola_limit` THEN 'EXHAUSTED' ELSE 'ACTIVE' END AS `ola_status`,
  `billing_cycle_start`
FROM `map_settings`
LIMIT 1;

-- 3. Manually reset tenant usage counters for the current month
UPDATE `map_settings` 
SET 
  `google_usage` = 0, 
  `ola_usage` = 0, 
  `billing_cycle_start` = DATE_FORMAT(CURDATE(), '%Y-%m-01') 
WHERE `id` = 1;

-- 4. Enable BYO mode with custom keys directly via SQL
UPDATE `map_settings`
SET 
  `is_byo_enabled` = 1,
  `preferred_provider` = 'google',
  `google_api_key` = 'YOUR_GOOGLE_KEY_HERE',
  `google_limit` = 10000,
  `ola_api_key` = 'YOUR_OLA_KEY_HERE',
  `ola_limit` = 50000
WHERE `id` = 1;
```

---

## 4. How the Runtime Failover Mechanism Works

1. **Page Load (Overview):**
   - Renders **100% Free OpenStreetMap** (`0` API quota consumed).

2. **On-Demand "View Full Route":**
   - **Tier 1 (Tenant BYO - Primary):** If `is_byo_enabled = 1` and primary key has quota left (`usage < limit`), loads primary provider (Google/Ola) and increments tenant's usage counter. **Master DB quota is 0**.
   - **Tier 2 (Tenant BYO - Failover):** If primary key reaches its limit (`usage >= limit`), automatically switches to the secondary key and increments its counter. **Master DB quota is 0**.
   - **Tier 3 (Zero-Cost OSM):** If both BYO keys are exhausted or BYO is disabled and master quota is exhausted, seamlessly loads OpenStreetMap with **0 downtime guarantee**.
