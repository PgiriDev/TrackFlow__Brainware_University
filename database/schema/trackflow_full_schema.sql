-- ============================================
-- TrackFlow - Complete Database Schema
-- MySQL / MariaDB
-- Created: December 5, 2025
-- ============================================

-- Create Database
DROP DATABASE IF EXISTS trackflow;
CREATE DATABASE trackflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE trackflow;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  name VARCHAR(255),
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password VARCHAR(255),
  email_verified_at DATETIME NULL,
  two_factor_enabled TINYINT(1) DEFAULT 0,
  two_factor_secret VARCHAR(255) NULL,
  two_factor_recovery_codes TEXT NULL,
  currency CHAR(3) DEFAULT 'INR',
  timezone VARCHAR(50) DEFAULT 'UTC',
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_email (email),
  INDEX idx_users_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ROLES (RBAC - Role Based Access Control)
-- ============================================
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) UNIQUE NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_user (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id INT NOT NULL,
  PRIMARY KEY(user_id, role_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER SETTINGS
-- ============================================
CREATE TABLE user_settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  settings JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_settings (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BANK ACCOUNTS
-- ============================================
CREATE TABLE bank_accounts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  provider VARCHAR(100) NOT NULL COMMENT 'e.g., finvu, saltedge, plaid',
  provider_account_id VARCHAR(255) NULL COMMENT 'External provider account ID',
  bank_name VARCHAR(255),
  account_number VARCHAR(255) NULL COMMENT 'Encrypted full account number',
  account_mask VARCHAR(50) COMMENT 'e.g., ****1234',
  account_type VARCHAR(50) COMMENT 'savings, checking, credit_card, etc',
  currency CHAR(3) DEFAULT 'INR',
  balance DECIMAL(16,2) DEFAULT 0.00,
  available_balance DECIMAL(16,2) DEFAULT 0.00,
  status ENUM('active','inactive','error','pending') DEFAULT 'active',
  last_synced_at DATETIME NULL,
  sync_frequency VARCHAR(50) DEFAULT 'daily' COMMENT 'daily, weekly, manual',
  metadata JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_bank_accounts_user (user_id),
  INDEX idx_bank_accounts_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ACCOUNT TOKENS (Encrypted)
-- ============================================
CREATE TABLE account_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bank_account_id BIGINT UNSIGNED NOT NULL,
  provider_token VARCHAR(1024) COMMENT 'Encrypted access token',
  provider_refresh_token VARCHAR(1024) COMMENT 'Encrypted refresh token',
  token_type VARCHAR(50) DEFAULT 'bearer',
  expires_at DATETIME NULL,
  scopes TEXT NULL,
  meta JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE CASCADE,
  INDEX idx_account_tokens_bank (bank_account_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CATEGORIES
-- ============================================
CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL COMMENT 'NULL = system category',
  parent_id BIGINT UNSIGNED NULL COMMENT 'For subcategories',
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NULL,
  type ENUM('expense','income','transfer') DEFAULT 'expense',
  color VARCHAR(7) DEFAULT '#cccccc',
  icon VARCHAR(255) NULL,
  is_system TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_categories_user (user_id),
  INDEX idx_categories_slug (slug),
  INDEX idx_categories_system (is_system)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TRANSACTIONS
-- ============================================
CREATE TABLE transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  user_id BIGINT UNSIGNED NOT NULL,
  bank_account_id BIGINT UNSIGNED NULL,
  provider_tx_id VARCHAR(255) NULL COMMENT 'External provider transaction ID',
  date DATE NOT NULL,
  posted_at DATETIME NULL,
  description VARCHAR(1000),
  merchant VARCHAR(255),
  amount DECIMAL(16,2) NOT NULL,
  currency CHAR(3) DEFAULT 'INR',
  type ENUM('debit','credit') NOT NULL,
  category_id BIGINT UNSIGNED NULL,
  tags JSON NULL,
  notes TEXT NULL,
  location VARCHAR(255) NULL,
  is_recurring TINYINT(1) DEFAULT 0,
  is_duplicate TINYINT(1) DEFAULT 0,
  is_excluded TINYINT(1) DEFAULT 0 COMMENT 'Exclude from reports/budgets',
  status ENUM('pending','completed','reconciled','failed') DEFAULT 'completed',
  confidence_score DECIMAL(5,2) NULL COMMENT 'Auto-categorization confidence',
  raw JSON NULL COMMENT 'Raw payload from provider',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_transactions_user (user_id),
  INDEX idx_transactions_date (date),
  INDEX idx_transactions_category (category_id),
  INDEX idx_transactions_merchant (merchant),
  INDEX idx_tx_user_date_amount (user_id, date, amount) COMMENT 'For duplicate detection',
  INDEX idx_transactions_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CATEGORIZATION RULES
-- ============================================
CREATE TABLE categorization_rules (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL COMMENT 'NULL = system rule',
  category_id BIGINT UNSIGNED NOT NULL,
  pattern VARCHAR(500) NOT NULL COMMENT 'Regex or keyword pattern',
  pattern_type ENUM('keyword','regex','merchant') DEFAULT 'keyword',
  priority INT DEFAULT 0 COMMENT 'Higher priority rules checked first',
  is_active TINYINT(1) DEFAULT 1,
  match_count INT DEFAULT 0 COMMENT 'Number of times rule matched',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX idx_rules_user (user_id),
  INDEX idx_rules_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BUDGETS
-- ============================================
CREATE TABLE budgets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  period ENUM('weekly','monthly','quarterly','yearly','custom') DEFAULT 'monthly',
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  total_limit DECIMAL(16,2) NOT NULL,
  currency CHAR(3) DEFAULT 'INR',
  alert_threshold DECIMAL(5,2) DEFAULT 80.00 COMMENT 'Alert at X% of budget',
  is_recurring TINYINT(1) DEFAULT 0,
  status ENUM('active','completed','cancelled') DEFAULT 'active',
  meta JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_budgets_user (user_id),
  INDEX idx_budgets_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE budget_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  budget_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  limit_amount DECIMAL(16,2) NOT NULL,
  spent_amount DECIMAL(16,2) DEFAULT 0.00,
  rollover_enabled TINYINT(1) DEFAULT 0,
  rollover_amount DECIMAL(16,2) DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX idx_budget_items_budget (budget_id),
  INDEX idx_budget_items_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- REPORTS (Generated Exports)
-- ============================================
CREATE TABLE reports (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(100) NOT NULL COMMENT 'monthly_summary, category_report, tax_report, etc',
  name VARCHAR(255),
  format ENUM('pdf','csv','excel','json') DEFAULT 'pdf',
  parameters JSON COMMENT 'Report generation parameters',
  file_path VARCHAR(1024) COMMENT 'Storage path',
  file_size INT NULL COMMENT 'File size in bytes',
  status ENUM('ready','processing','failed') DEFAULT 'processing',
  error_message TEXT NULL,
  downloaded_at DATETIME NULL,
  expires_at DATETIME NULL COMMENT 'Auto-delete after this date',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_reports_user (user_id),
  INDEX idx_reports_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SYNC LOGS (Bank Synchronization)
-- ============================================
CREATE TABLE sync_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED,
  bank_account_id BIGINT UNSIGNED,
  action VARCHAR(100) NOT NULL COMMENT 'fetch_transactions, refresh_balance, etc',
  status ENUM('ok','error','partial') NOT NULL,
  transactions_fetched INT DEFAULT 0,
  transactions_new INT DEFAULT 0,
  transactions_updated INT DEFAULT 0,
  message TEXT,
  error_code VARCHAR(50) NULL,
  duration_ms INT NULL COMMENT 'Sync duration in milliseconds',
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
  INDEX idx_sync_logs_user (user_id),
  INDEX idx_sync_logs_bank (bank_account_id),
  INDEX idx_sync_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- NOTIFICATIONS (In-App)
-- ============================================
CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(100) NOT NULL COMMENT 'budget_alert, sync_complete, security_alert, etc',
  title VARCHAR(255) NOT NULL,
  body TEXT,
  action_url VARCHAR(1024) NULL,
  read_at DATETIME NULL,
  channel ENUM('inapp','email','push','sms') DEFAULT 'inapp',
  priority ENUM('low','normal','high','urgent') DEFAULT 'normal',
  meta JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_notifications_user (user_id),
  INDEX idx_notifications_read (read_at),
  INDEX idx_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- AUDIT LOGS (Security & Important Actions)
-- ============================================
CREATE TABLE audits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(255) NOT NULL COMMENT 'login, logout, password_change, transaction_edit, etc',
  auditable_type VARCHAR(100) NULL COMMENT 'Model name',
  auditable_id BIGINT UNSIGNED NULL COMMENT 'Model ID',
  old_values JSON NULL,
  new_values JSON NULL,
  ip_address VARCHAR(50),
  user_agent VARCHAR(500),
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_audits_user (user_id),
  INDEX idx_audits_action (action),
  INDEX idx_audits_auditable (auditable_type, auditable_id),
  INDEX idx_audits_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- RECURRING TRANSACTIONS
-- ============================================
CREATE TABLE recurring_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  bank_account_id BIGINT UNSIGNED NULL,
  category_id BIGINT UNSIGNED NULL,
  description VARCHAR(1000) NOT NULL,
  merchant VARCHAR(255),
  amount DECIMAL(16,2) NOT NULL,
  currency CHAR(3) DEFAULT 'INR',
  type ENUM('debit','credit') NOT NULL,
  frequency ENUM('daily','weekly','biweekly','monthly','quarterly','yearly') NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  next_occurrence DATE NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  auto_create TINYINT(1) DEFAULT 1 COMMENT 'Auto-create transaction on occurrence',
  last_created_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_recurring_user (user_id),
  INDEX idx_recurring_next (next_occurrence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- GOALS (Financial Goals)
-- ============================================
CREATE TABLE goals (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  target_amount DECIMAL(16,2) NOT NULL,
  current_amount DECIMAL(16,2) DEFAULT 0.00,
  currency CHAR(3) DEFAULT 'INR',
  target_date DATE NULL,
  category ENUM('savings','debt','investment','emergency','other') DEFAULT 'savings',
  priority ENUM('low','medium','high') DEFAULT 'medium',
  status ENUM('active','completed','cancelled') DEFAULT 'active',
  icon VARCHAR(255) NULL,
  color VARCHAR(7) DEFAULT '#4CAF50',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_goals_user (user_id),
  INDEX idx_goals_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- WEBHOOKS (For provider callbacks)
-- ============================================
CREATE TABLE webhooks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  provider VARCHAR(100) NOT NULL,
  event_type VARCHAR(100) NOT NULL,
  payload JSON NOT NULL,
  signature VARCHAR(500) NULL,
  ip_address VARCHAR(50),
  processed TINYINT(1) DEFAULT 0,
  processed_at DATETIME NULL,
  error_message TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_webhooks_provider (provider),
  INDEX idx_webhooks_processed (processed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PERSONAL ACCESS TOKENS (Laravel Sanctum)
-- ============================================
CREATE TABLE personal_access_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tokenable_type VARCHAR(255) NOT NULL,
  tokenable_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  abilities TEXT NULL,
  last_used_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_tokenable (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PASSWORD RESET TOKENS
-- ============================================
CREATE TABLE password_reset_tokens (
  email VARCHAR(255) PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DEFAULT SYSTEM ROLES
-- ============================================
INSERT INTO roles (name, description) VALUES
('admin', 'Full system access'),
('user', 'Standard user access'),
('premium', 'Premium user with additional features'),
('readonly', 'Read-only access');

-- ============================================
-- INSERT DEFAULT SYSTEM CATEGORIES
-- ============================================
INSERT INTO categories (user_id, name, slug, type, color, icon, is_system, sort_order) VALUES
-- Income Categories
(NULL, 'Salary', 'salary', 'income', '#4CAF50', 'fas fa-money-bill-wave', 1, 1),
(NULL, 'Freelance', 'freelance', 'income', '#8BC34A', 'fas fa-laptop-code', 1, 2),
(NULL, 'Investment', 'investment', 'income', '#009688', 'fas fa-chart-line', 1, 3),
(NULL, 'Other Income', 'other-income', 'income', '#00BCD4', 'fas fa-hand-holding-usd', 1, 4),

-- Expense Categories
(NULL, 'Food & Dining', 'food-dining', 'expense', '#FF5722', 'fas fa-utensils', 1, 10),
(NULL, 'Groceries', 'groceries', 'expense', '#FF9800', 'fas fa-shopping-cart', 1, 11),
(NULL, 'Transportation', 'transportation', 'expense', '#2196F3', 'fas fa-car', 1, 12),
(NULL, 'Shopping', 'shopping', 'expense', '#E91E63', 'fas fa-shopping-bag', 1, 13),
(NULL, 'Entertainment', 'entertainment', 'expense', '#9C27B0', 'fas fa-film', 1, 14),
(NULL, 'Bills & Utilities', 'bills-utilities', 'expense', '#607D8B', 'fas fa-file-invoice-dollar', 1, 15),
(NULL, 'Healthcare', 'healthcare', 'expense', '#F44336', 'fas fa-heartbeat', 1, 16),
(NULL, 'Education', 'education', 'expense', '#3F51B5', 'fas fa-graduation-cap', 1, 17),
(NULL, 'Travel', 'travel', 'expense', '#00BCD4', 'fas fa-plane', 1, 18),
(NULL, 'Insurance', 'insurance', 'expense', '#795548', 'fas fa-shield-alt', 1, 19),
(NULL, 'Personal Care', 'personal-care', 'expense', '#E91E63', 'fas fa-spa', 1, 20),
(NULL, 'Home & Garden', 'home-garden', 'expense', '#8BC34A', 'fas fa-home', 1, 21),
(NULL, 'Gifts & Donations', 'gifts-donations', 'expense', '#FF6F00', 'fas fa-gift', 1, 22),
(NULL, 'Subscriptions', 'subscriptions', 'expense', '#673AB7', 'fas fa-sync-alt', 1, 23),
(NULL, 'Fees & Charges', 'fees-charges', 'expense', '#9E9E9E', 'fas fa-receipt', 1, 24),
(NULL, 'Uncategorized', 'uncategorized', 'expense', '#BDBDBD', 'fas fa-question-circle', 1, 99);

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

-- Full-text search on transaction descriptions
-- ALTER TABLE transactions ADD FULLTEXT INDEX idx_transaction_search (description, merchant);

-- Composite indexes for common queries
CREATE INDEX idx_transactions_user_date_type ON transactions(user_id, date, type);
CREATE INDEX idx_transactions_user_category_date ON transactions(user_id, category_id, date);
CREATE INDEX idx_bank_accounts_user_status ON bank_accounts(user_id, status);

-- ============================================
-- VIEWS FOR COMMON QUERIES
-- ============================================

-- Monthly spending summary
CREATE VIEW v_monthly_spending AS
SELECT 
  user_id,
  DATE_FORMAT(date, '%Y-%m') AS month,
  SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) AS total_expenses,
  SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) AS total_income,
  COUNT(*) AS transaction_count
FROM transactions
WHERE is_excluded = 0
GROUP BY user_id, DATE_FORMAT(date, '%Y-%m');

-- Category spending summary
CREATE VIEW v_category_spending AS
SELECT 
  t.user_id,
  t.category_id,
  c.name AS category_name,
  c.color,
  SUM(t.amount) AS total_spent,
  COUNT(*) AS transaction_count,
  AVG(t.amount) AS avg_amount
FROM transactions t
JOIN categories c ON t.category_id = c.id
WHERE t.type = 'debit' AND t.is_excluded = 0
GROUP BY t.user_id, t.category_id, c.name, c.color;

-- Budget progress
CREATE VIEW v_budget_progress AS
SELECT 
  b.id AS budget_id,
  b.user_id,
  b.name AS budget_name,
  b.total_limit,
  bi.category_id,
  c.name AS category_name,
  bi.limit_amount,
  bi.spent_amount,
  ROUND((bi.spent_amount / bi.limit_amount) * 100, 2) AS percent_used,
  (bi.limit_amount - bi.spent_amount) AS remaining
FROM budgets b
JOIN budget_items bi ON b.id = bi.budget_id
JOIN categories c ON bi.category_id = c.id
WHERE b.status = 'active';

-- ============================================
-- TRIGGERS FOR AUTOMATIC UPDATES
-- ============================================

-- Update budget spent amount when transaction is inserted
DELIMITER $$

CREATE TRIGGER trg_transaction_insert_update_budget
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
  IF NEW.type = 'debit' AND NEW.category_id IS NOT NULL AND NEW.is_excluded = 0 THEN
    UPDATE budget_items bi
    JOIN budgets b ON bi.budget_id = b.id
    SET bi.spent_amount = bi.spent_amount + NEW.amount
    WHERE bi.category_id = NEW.category_id
      AND b.user_id = NEW.user_id
      AND NEW.date BETWEEN b.start_date AND b.end_date
      AND b.status = 'active';
  END IF;
END$$

-- Update budget spent amount when transaction is updated
CREATE TRIGGER trg_transaction_update_update_budget
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
  IF OLD.type = 'debit' AND OLD.category_id IS NOT NULL AND OLD.is_excluded = 0 THEN
    UPDATE budget_items bi
    JOIN budgets b ON bi.budget_id = b.id
    SET bi.spent_amount = bi.spent_amount - OLD.amount
    WHERE bi.category_id = OLD.category_id
      AND b.user_id = OLD.user_id
      AND OLD.date BETWEEN b.start_date AND b.end_date
      AND b.status = 'active';
  END IF;
  
  IF NEW.type = 'debit' AND NEW.category_id IS NOT NULL AND NEW.is_excluded = 0 THEN
    UPDATE budget_items bi
    JOIN budgets b ON bi.budget_id = b.id
    SET bi.spent_amount = bi.spent_amount + NEW.amount
    WHERE bi.category_id = NEW.category_id
      AND b.user_id = NEW.user_id
      AND NEW.date BETWEEN b.start_date AND b.end_date
      AND b.status = 'active';
  END IF;
END$$

-- Update budget spent amount when transaction is deleted
CREATE TRIGGER trg_transaction_delete_update_budget
AFTER DELETE ON transactions
FOR EACH ROW
BEGIN
  IF OLD.type = 'debit' AND OLD.category_id IS NOT NULL AND OLD.is_excluded = 0 THEN
    UPDATE budget_items bi
    JOIN budgets b ON bi.budget_id = b.id
    SET bi.spent_amount = bi.spent_amount - OLD.amount
    WHERE bi.category_id = OLD.category_id
      AND b.user_id = OLD.user_id
      AND OLD.date BETWEEN b.start_date AND b.end_date
      AND b.status = 'active';
  END IF;
END$$

-- Generate UUID for users
CREATE TRIGGER trg_users_uuid
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
  IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
    SET NEW.uuid = UUID();
  END IF;
END$$

-- Generate UUID for transactions
CREATE TRIGGER trg_transactions_uuid
BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
  IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
    SET NEW.uuid = UUID();
  END IF;
END$$

DELIMITER ;

-- ============================================
-- GRANT PERMISSIONS (Adjust as needed)
-- ============================================
-- CREATE USER 'trackflow_user'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT ALL PRIVILEGES ON trackflow.* TO 'trackflow_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- COMPLETION MESSAGE
-- ============================================
SELECT 'TrackFlow database created successfully!' AS Status;
SELECT COUNT(*) AS TableCount FROM information_schema.tables WHERE table_schema = 'trackflow';
