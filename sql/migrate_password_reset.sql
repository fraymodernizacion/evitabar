SET NAMES utf8mb4;

ALTER TABLE users
  ADD COLUMN password_reset_required TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash,
  ADD COLUMN password_reset_at DATETIME NULL AFTER password_reset_required,
  ADD COLUMN password_reset_by BIGINT UNSIGNED NULL AFTER password_reset_at;
