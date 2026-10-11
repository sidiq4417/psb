-- ============================================================
-- UPGRADE SPMB v1.0.2 ke v1.0.3
-- ------------------------------------------------------------
-- File ini berisi semua perubahan database untuk upgrade.
-- Database: spmb  (pastikan Anda memilih database yang benar)
--
-- Cara menjalankan di phpMyAdmin:
--   1. Pilih database SPMB di panel kiri.
--   2. Klik menu "Import".
--   3. Pilih file UPGRADE.sql ini, lalu klik "Go".
--
-- Catatan: Jika muncul pesan "Column already exists" / "Duplicate
-- column", artinya kolom tersebut sudah ada dan aman untuk
-- dilanjutkan.
-- ============================================================

-- ------------------------------------------------------------
-- 1) Tabel users: rapikan kolom nama ibu
--    Di v1.0.3 kolom `mother_name_parent` diubah namanya menjadi
--    `mother_name`. Data pada kolom `mother_name` lama dipindah
--    terlebih dahulu agar nama ibu tidak hilang.
-- ------------------------------------------------------------
ALTER TABLE `users` CHANGE COLUMN `mother_name` `mother_name_old` VARCHAR(255) NULL;
ALTER TABLE `users` CHANGE COLUMN `mother_name_parent` `mother_name` VARCHAR(255) NULL;

UPDATE `users`
   SET `mother_name` = `mother_name_old`
 WHERE (`mother_name` IS NULL OR `mother_name` = '')
   AND (`mother_name_old` IS NOT NULL AND `mother_name_old` <> '');

ALTER TABLE `users` DROP COLUMN `mother_name_old`;

-- ------------------------------------------------------------
-- 2) Tabel users: tambah kolom alamat baru
-- ------------------------------------------------------------
ALTER TABLE `users`
    ADD COLUMN `address_country_id` VARCHAR(2) AFTER `address`,
    ADD COLUMN `address_province_id` VARCHAR(100) AFTER `address_country_id`,
    ADD COLUMN `address_district_id` VARCHAR(100) AFTER `address_province_id`,
    ADD COLUMN `address_subdistrict_id` VARCHAR(100) AFTER `address_district_id`,
    ADD COLUMN `street_address` TEXT AFTER `address_subdistrict_id`;

-- ------------------------------------------------------------
-- 3) Tabel application_settings: tambah kolom logo & favicon
-- ------------------------------------------------------------
ALTER TABLE `application_settings`
    ADD COLUMN `logo` VARCHAR(255) AFTER `turnstile_secret_key`,
    ADD COLUMN `favicon` VARCHAR(255) AFTER `logo`;
