-- OnPoint backend schema. Run once per site, against that site's own database:
--   mysql -u USER -p DB_NAME < schema.sql

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title           VARCHAR(255) NOT NULL,
  slug            VARCHAR(190) NOT NULL UNIQUE,
  excerpt         VARCHAR(500) NULL,
  body_html       MEDIUMTEXT NOT NULL,
  cover_image     VARCHAR(255) NULL,
  cover_alt       VARCHAR(255) NULL,
  status          ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at    DATETIME NULL,
  seo_title       VARCHAR(255) NULL,
  seo_description VARCHAR(320) NULL,
  takeaways       TEXT NULL,           -- 'Key takeaways', one per line (shown on the article, used by search & AI answers)
  faq_json        MEDIUMTEXT NULL,     -- [{"q":"...","a":"..."}] (FAQ block + FAQPage structured data)
  author_id       INT UNSIGNED NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_posts_public (status, published_at),
  CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Used only when FEATURE_PROJECTS=true (engineering)
CREATE TABLE IF NOT EXISTS projects (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(255) NOT NULL,
  slug         VARCHAR(190) NOT NULL UNIQUE,
  client       VARCHAR(190) NULL,
  location     VARCHAR(190) NULL,
  category     VARCHAR(120) NULL,
  start_date   DATE NULL,
  end_date     DATE NULL,
  status       ENUM('planned','ongoing','completed','on_hold') NOT NULL DEFAULT 'ongoing',
  summary      VARCHAR(500) NULL,
  body_html    MEDIUMTEXT NULL,
  cover_image  VARCHAR(255) NULL,
  cover_alt    VARCHAR(255) NULL,
  featured     TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  sort_order   INT NOT NULL DEFAULT 0,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_projects_public (is_published, featured, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Old addresses of renamed posts/projects, so links keep working (301) and Google keeps the ranking
CREATE TABLE IF NOT EXISTS redirects (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kind     ENUM('post','project') NOT NULL,
  old_slug VARCHAR(190) NOT NULL,
  target_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_redirect (kind, old_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(190) NULL,
  phone      VARCHAR(40) NULL,
  subject    VARCHAR(190) NULL,
  message    TEXT NOT NULL,
  status     ENUM('new','read','archived') NOT NULL DEFAULT 'new',
  ip         VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_messages_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_settings (
  setting_key   VARCHAR(64) PRIMARY KEY,
  setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS throttle (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bucket       VARCHAR(32) NOT NULL,
  throttle_key VARCHAR(255) NOT NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_throttle (bucket, throttle_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
  ('company_name', ''),
  ('email', ''),
  ('phone', ''),
  ('address', ''),
  ('notify_email', ''),
  ('same_as', '');   -- social / directory profile URLs, one per line (used in structured data)
