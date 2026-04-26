CREATE DATABASE IF NOT EXISTS pase_evita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pase_evita;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS redemptions;
DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS benefits;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS settings;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  dni VARCHAR(20) NOT NULL,
  phone VARCHAR(25) NOT NULL,
  birthdate DATE NOT NULL,
  email VARCHAR(160) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('client', 'staff', 'admin') NOT NULL DEFAULT 'client',
  level TINYINT UNSIGNED NOT NULL DEFAULT 1,
  visits_count INT UNSIGNED NOT NULL DEFAULT 0,
  qr_token CHAR(64) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uniq_users_dni (dni),
  UNIQUE KEY uniq_users_email (email),
  UNIQUE KEY uniq_users_phone (phone),
  UNIQUE KEY uniq_users_qr_token (qr_token),
  KEY idx_users_level (level),
  KEY idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE visits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  staff_id BIGINT UNSIGNED NULL,
  visit_date DATETIME NOT NULL,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_visits_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_visits_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_visits_user_date (user_id, visit_date)
) ENGINE=InnoDB;

CREATE TABLE benefits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  required_level TINYINT UNSIGNED NOT NULL DEFAULT 1,
  conditions VARCHAR(255) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  KEY idx_benefits_filters (active, required_level, sort_order)
) ENGINE=InnoDB;

CREATE TABLE redemptions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  benefit_id BIGINT UNSIGNED NOT NULL,
  staff_id BIGINT UNSIGNED NULL,
  redeemed_at DATETIME NOT NULL,
  notes VARCHAR(255) NULL,
  CONSTRAINT fk_redemptions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_benefit FOREIGN KEY (benefit_id) REFERENCES benefits(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_redemptions_user_date (user_id, redeemed_at)
) ENGINE=InnoDB;

CREATE TABLE settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL,
  setting_value VARCHAR(255) NOT NULL,
  UNIQUE KEY uniq_settings_key (setting_key)
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('program_name', 'Pase Evita'),
('level_1_min', '0'),
('level_2_min', '4'),
('level_3_min', '8'),
('maintenance_period_months', '3'),
('maintain_level_2', '2'),
('maintain_level_3', '3'),
('visit_block_minutes', '120');

INSERT INTO users (name, dni, phone, birthdate, email, password_hash, role, level, visits_count, qr_token, created_at, updated_at) VALUES
('Ana Mercado', '27111222', '+543834001111', '1990-05-10', 'ana@paseevita.local', '$2y$12$.EYWleQDpUjZarjlClwFnOf9VeIIVlOeD5sPgPFGuy9XijU5rAWfO', 'client', 1, 2, '4e3251f79d5d25e7f70a6920d66315bd8af738f8c71df3b5964d5359b0f7b03e', NOW(), NOW()),
('Beto Brizuela', '30111222', '+543834002222', '1987-09-18', 'beto@paseevita.local', '$2y$12$.EYWleQDpUjZarjlClwFnOf9VeIIVlOeD5sPgPFGuy9XijU5rAWfO', 'client', 2, 5, 'd44710f2a4dddb0df772e4e4c2d496f7305c60ccf8871ef7b66f8e14f3f9969f', NOW(), NOW()),
('Carla Páez', '32111222', '+543834003333', '1994-12-02', 'carla@paseevita.local', '$2y$12$.EYWleQDpUjZarjlClwFnOf9VeIIVlOeD5sPgPFGuy9XijU5rAWfO', 'client', 3, 11, 'ea4624acbf8f7b57a5d85f2607b2197f9ec1f6ff5adf8f0f58ad1eb289662f34', NOW(), NOW()),
('Admin Evita', '22111000', '+543834009999', '1983-07-01', 'admin@paseevita.local', '$2y$12$.EYWleQDpUjZarjlClwFnOf9VeIIVlOeD5sPgPFGuy9XijU5rAWfO', 'admin', 1, 0, '61f93b7d687f0f095c99fbb95fdb59bb2db1e69210298e012f1d813ad6b9d2c4', NOW(), NOW()),
('Staff Evita', '25111000', '+543834008888', '1991-11-23', 'staff@paseevita.local', '$2y$12$.EYWleQDpUjZarjlClwFnOf9VeIIVlOeD5sPgPFGuy9XijU5rAWfO', 'staff', 1, 0, '5c98d3d8ebf0f754f3ecbce53f4c4dc901ed8f35f1b9eaeb7347f4f8f71a8458', NOW(), NOW());

INSERT INTO benefits (title, description, required_level, conditions, active, sort_order, created_at, updated_at) VALUES
('Café de bienvenida', 'Un café de cortesía para arrancar la visita.', 1, 'Usalo una vez por visita.', 1, 10, NOW(), NOW()),
('Beneficio de cumpleaños', 'Un mimo especial durante tu semana de cumpleaños.', 1, 'Presentar DNI. Válido 7 días corridos.', 1, 20, NOW(), NOW()),
('Dulce Federal', 'Descuento en postres de la carta como El Dulce Federal, Vigilante Justicialista o La Unidad Básica.', 1, 'Válido en consumos de mesa.', 1, 30, NOW(), NOW()),
('Empanada Santa Evita', 'Una empanada de la casa sin cargo.', 2, 'Válido con consumo principal. Una vez por visita.', 1, 40, NOW(), NOW()),
('Sándwich Cabecita Negra', 'Descuento en sándwiches como El Descamisado, El Militante, El Conductor o El General.', 2, 'Válido de martes a jueves.', 1, 50, NOW(), NOW()),
('Vino Tinto Nacional', 'Una copa de vino tinto nacional de la carta.', 3, 'Solo mayores de 18. Válido con consumición principal.', 1, 60, NOW(), NOW());

INSERT INTO visits (user_id, staff_id, visit_date, notes, created_at) VALUES
(1, 5, DATE_SUB(NOW(), INTERVAL 20 DAY), 'Visita demo', NOW()),
(1, 5, DATE_SUB(NOW(), INTERVAL 5 DAY), 'Visita demo', NOW()),
(2, 5, DATE_SUB(NOW(), INTERVAL 40 DAY), 'Visita demo', NOW()),
(2, 5, DATE_SUB(NOW(), INTERVAL 20 DAY), 'Visita demo', NOW()),
(2, 5, DATE_SUB(NOW(), INTERVAL 8 DAY), 'Visita demo', NOW()),
(2, 5, DATE_SUB(NOW(), INTERVAL 2 DAY), 'Visita demo', NOW()),
(2, 5, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 70 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 60 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 45 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 30 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 20 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 15 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 10 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 7 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 5 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Visita demo', NOW()),
(3, 5, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Visita demo', NOW());

INSERT INTO redemptions (user_id, benefit_id, staff_id, redeemed_at, notes) VALUES
(2, 4, 5, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Canje demo vermut'),
(3, 6, 5, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Canje demo cena');
