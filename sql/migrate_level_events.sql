SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS level_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  previous_level TINYINT UNSIGNED NOT NULL,
  new_level TINYINT UNSIGNED NOT NULL,
  reason VARCHAR(60) NOT NULL,
  recent_visits INT UNSIGNED NOT NULL DEFAULT 0,
  maintenance_period_months INT UNSIGNED NOT NULL DEFAULT 3,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_level_events_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY idx_level_events_user_date (user_id, created_at)
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value)
VALUES ('maintenance_warning_days', '10')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
