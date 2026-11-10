-- Run ONCE in phpMyAdmin on a site that was created from an older schema.sql
-- (a fresh import of the current schema.sql already includes all of this).
ALTER TABLE posts ADD COLUMN takeaways TEXT NULL AFTER seo_description;
ALTER TABLE posts ADD COLUMN faq_json MEDIUMTEXT NULL AFTER takeaways;
CREATE TABLE IF NOT EXISTS redirects (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kind     ENUM('post','project') NOT NULL,
  old_slug VARCHAR(190) NOT NULL,
  target_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_redirect (kind, old_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES ('same_as', '');
