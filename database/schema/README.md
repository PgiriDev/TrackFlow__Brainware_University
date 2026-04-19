# 🗄️ TrackFlow Database Schema

Complete MySQL/MariaDB database schema for TrackFlow financial management system.

## 📊 Database Overview

**Database Name:** `trackflow`  
**Character Set:** `utf8mb4`  
**Collation:** `utf8mb4_unicode_ci`  
**Total Tables:** 20+  
**Total Views:** 3  
**Total Triggers:** 5

---

## 🏗️ Table Structure

### Core Tables

#### 1. **users**

User accounts with authentication and settings.

| Column             | Type            | Description                      |
| ------------------ | --------------- | -------------------------------- |
| id                 | BIGINT UNSIGNED | Primary key                      |
| uuid               | CHAR(36)        | Unique identifier                |
| name               | VARCHAR(255)    | User full name                   |
| email              | VARCHAR(255)    | Unique email address             |
| phone              | VARCHAR(30)     | Phone number                     |
| password           | VARCHAR(255)    | Hashed password                  |
| email_verified_at  | DATETIME        | Email verification timestamp     |
| two_factor_enabled | TINYINT(1)      | 2FA status                       |
| two_factor_secret  | VARCHAR(255)    | 2FA secret key                   |
| currency           | CHAR(3)         | Default currency (USD, EUR, INR) |
| timezone           | VARCHAR(50)     | User timezone                    |
| is_admin           | TINYINT(1)      | Admin flag                       |

**Indexes:** `idx_users_email`, `idx_users_uuid`

---

#### 2. **bank_accounts**

Connected bank accounts and financial accounts.

| Column              | Type            | Description                       |
| ------------------- | --------------- | --------------------------------- |
| id                  | BIGINT UNSIGNED | Primary key                       |
| user_id             | BIGINT UNSIGNED | Foreign key to users              |
| provider            | VARCHAR(100)    | Provider name (finvu, plaid, etc) |
| provider_account_id | VARCHAR(255)    | External account ID               |
| bank_name           | VARCHAR(255)    | Bank name                         |
| account_number      | VARCHAR(255)    | Encrypted account number          |
| account_mask        | VARCHAR(50)     | Masked number (\*\*\*\*1234)      |
| account_type        | VARCHAR(50)     | Type (savings, checking, credit)  |
| balance             | DECIMAL(16,2)   | Current balance                   |
| available_balance   | DECIMAL(16,2)   | Available balance                 |
| status              | ENUM            | active, inactive, error, pending  |
| last_synced_at      | DATETIME        | Last sync timestamp               |

**Indexes:** `idx_bank_accounts_user`, `idx_bank_accounts_status`

---

#### 3. **account_tokens**

Encrypted OAuth tokens for bank connections.

| Column                 | Type            | Description                  |
| ---------------------- | --------------- | ---------------------------- |
| id                     | BIGINT UNSIGNED | Primary key                  |
| bank_account_id        | BIGINT UNSIGNED | Foreign key to bank_accounts |
| provider_token         | VARCHAR(1024)   | Encrypted access token       |
| provider_refresh_token | VARCHAR(1024)   | Encrypted refresh token      |
| token_type             | VARCHAR(50)     | Token type (bearer)          |
| expires_at             | DATETIME        | Token expiration             |
| scopes                 | TEXT            | OAuth scopes                 |

**Security:** All tokens are encrypted at rest.

---

#### 4. **transactions**

All financial transactions (debits & credits).

| Column           | Type            | Description                    |
| ---------------- | --------------- | ------------------------------ |
| id               | BIGINT UNSIGNED | Primary key                    |
| uuid             | CHAR(36)        | Unique identifier              |
| user_id          | BIGINT UNSIGNED | Foreign key to users           |
| bank_account_id  | BIGINT UNSIGNED | Foreign key to bank_accounts   |
| provider_tx_id   | VARCHAR(255)    | External transaction ID        |
| date             | DATE            | Transaction date               |
| posted_at        | DATETIME        | Posting timestamp              |
| description      | VARCHAR(1000)   | Transaction description        |
| merchant         | VARCHAR(255)    | Merchant name                  |
| amount           | DECIMAL(16,2)   | Transaction amount             |
| currency         | CHAR(3)         | Currency code                  |
| type             | ENUM            | debit, credit                  |
| category_id      | BIGINT UNSIGNED | Foreign key to categories      |
| tags             | JSON            | Custom tags                    |
| notes            | TEXT            | User notes                     |
| is_recurring     | TINYINT(1)      | Recurring flag                 |
| is_duplicate     | TINYINT(1)      | Duplicate detection flag       |
| is_excluded      | TINYINT(1)      | Exclude from reports           |
| status           | ENUM            | pending, completed, reconciled |
| confidence_score | DECIMAL(5,2)    | Auto-categorization confidence |
| raw              | JSON            | Raw provider payload           |

**Indexes:**

-   `idx_transactions_user`
-   `idx_transactions_date`
-   `idx_transactions_category`
-   `idx_tx_user_date_amount` (duplicate detection)

---

#### 5. **categories**

Transaction categories (system & user-defined).

| Column     | Type            | Description                     |
| ---------- | --------------- | ------------------------------- |
| id         | BIGINT UNSIGNED | Primary key                     |
| user_id    | BIGINT UNSIGNED | NULL = system category          |
| parent_id  | BIGINT UNSIGNED | Parent category (subcategories) |
| name       | VARCHAR(150)    | Category name                   |
| slug       | VARCHAR(150)    | URL-friendly slug               |
| type       | ENUM            | expense, income, transfer       |
| color      | VARCHAR(7)      | Hex color code                  |
| icon       | VARCHAR(255)    | Icon class (Font Awesome)       |
| is_system  | TINYINT(1)      | System category flag            |
| is_active  | TINYINT(1)      | Active status                   |
| sort_order | INT             | Display order                   |

**Default Categories:** 20+ system categories included (Food, Transport, etc.)

---

#### 6. **categorization_rules**

Auto-categorization rules using patterns.

| Column       | Type            | Description                   |
| ------------ | --------------- | ----------------------------- |
| id           | BIGINT UNSIGNED | Primary key                   |
| user_id      | BIGINT UNSIGNED | NULL = system rule            |
| category_id  | BIGINT UNSIGNED | Target category               |
| pattern      | VARCHAR(500)    | Match pattern (keyword/regex) |
| pattern_type | ENUM            | keyword, regex, merchant      |
| priority     | INT             | Rule priority (higher first)  |
| is_active    | TINYINT(1)      | Active status                 |
| match_count  | INT             | Success counter               |

---

#### 7. **budgets**

Budget definitions.

| Column          | Type            | Description                        |
| --------------- | --------------- | ---------------------------------- |
| id              | BIGINT UNSIGNED | Primary key                        |
| user_id         | BIGINT UNSIGNED | Foreign key to users               |
| name            | VARCHAR(255)    | Budget name                        |
| period          | ENUM            | weekly, monthly, quarterly, yearly |
| start_date      | DATE            | Budget start                       |
| end_date        | DATE            | Budget end                         |
| total_limit     | DECIMAL(16,2)   | Total budget limit                 |
| currency        | CHAR(3)         | Currency code                      |
| alert_threshold | DECIMAL(5,2)    | Alert at X% (default 80%)          |
| is_recurring    | TINYINT(1)      | Auto-renew flag                    |
| status          | ENUM            | active, completed, cancelled       |

---

#### 8. **budget_items**

Category-specific budget allocations.

| Column           | Type            | Description               |
| ---------------- | --------------- | ------------------------- |
| id               | BIGINT UNSIGNED | Primary key               |
| budget_id        | BIGINT UNSIGNED | Foreign key to budgets    |
| category_id      | BIGINT UNSIGNED | Foreign key to categories |
| limit_amount     | DECIMAL(16,2)   | Category limit            |
| spent_amount     | DECIMAL(16,2)   | Current spending          |
| rollover_enabled | TINYINT(1)      | Rollover unused amount    |
| rollover_amount  | DECIMAL(16,2)   | Rolled over amount        |

**Auto-Update:** Triggers automatically update `spent_amount` when transactions are created/updated.

---

#### 9. **recurring_transactions**

Scheduled recurring transactions.

| Column          | Type            | Description                 |
| --------------- | --------------- | --------------------------- |
| id              | BIGINT UNSIGNED | Primary key                 |
| user_id         | BIGINT UNSIGNED | Foreign key to users        |
| description     | VARCHAR(1000)   | Transaction description     |
| amount          | DECIMAL(16,2)   | Transaction amount          |
| type            | ENUM            | debit, credit               |
| frequency       | ENUM            | daily, weekly, monthly, etc |
| start_date      | DATE            | First occurrence            |
| end_date        | DATE            | Last occurrence (nullable)  |
| next_occurrence | DATE            | Next scheduled date         |
| auto_create     | TINYINT(1)      | Auto-create transactions    |

---

#### 10. **goals**

Financial goals tracking.

| Column         | Type            | Description                    |
| -------------- | --------------- | ------------------------------ |
| id             | BIGINT UNSIGNED | Primary key                    |
| user_id        | BIGINT UNSIGNED | Foreign key to users           |
| name           | VARCHAR(255)    | Goal name                      |
| description    | TEXT            | Goal description               |
| target_amount  | DECIMAL(16,2)   | Target amount                  |
| current_amount | DECIMAL(16,2)   | Current progress               |
| target_date    | DATE            | Target completion date         |
| category       | ENUM            | savings, debt, investment, etc |
| priority       | ENUM            | low, medium, high              |
| status         | ENUM            | active, completed, cancelled   |

---

### System Tables

#### 11. **roles** & **role_user**

Role-based access control (RBAC).

**Default Roles:**

-   `admin` - Full system access
-   `user` - Standard user access
-   `premium` - Premium features
-   `readonly` - Read-only access

---

#### 12. **user_settings**

User preferences and settings (JSON storage).

---

#### 13. **sync_logs**

Bank synchronization logs and history.

| Key Fields           | Description                         |
| -------------------- | ----------------------------------- |
| action               | fetch_transactions, refresh_balance |
| status               | ok, error, partial                  |
| transactions_fetched | Count of fetched transactions       |
| transactions_new     | Count of new transactions           |
| duration_ms          | Sync duration                       |

---

#### 14. **notifications**

In-app notifications and alerts.

| Key Fields | Description                                 |
| ---------- | ------------------------------------------- |
| type       | budget_alert, sync_complete, security_alert |
| channel    | inapp, email, push, sms                     |
| priority   | low, normal, high, urgent                   |
| read_at    | Read timestamp (NULL = unread)              |

---

#### 15. **reports**

Generated reports (PDF/CSV/Excel).

| Key Fields | Description                                  |
| ---------- | -------------------------------------------- |
| type       | monthly_summary, category_report, tax_report |
| format     | pdf, csv, excel, json                        |
| file_path  | Storage path                                 |
| status     | ready, processing, failed                    |
| expires_at | Auto-delete date                             |

---

#### 16. **audits**

Security and action audit trail.

| Key Fields     | Description                              |
| -------------- | ---------------------------------------- |
| action         | login, password_change, transaction_edit |
| auditable_type | Model name                               |
| auditable_id   | Model ID                                 |
| old_values     | Previous state (JSON)                    |
| new_values     | New state (JSON)                         |
| ip_address     | User IP                                  |
| user_agent     | Browser/client info                      |

---

#### 17. **webhooks**

Provider webhook payloads.

---

#### 18. **personal_access_tokens**

Laravel Sanctum API tokens.

---

#### 19. **password_reset_tokens**

Password reset tokens.

---

## 🔍 Database Views

### 1. **v_monthly_spending**

Monthly spending and income summary per user.

```sql
SELECT user_id, month, total_expenses, total_income, transaction_count
FROM v_monthly_spending
WHERE user_id = 1;
```

---

### 2. **v_category_spending**

Category-wise spending analysis.

```sql
SELECT category_name, total_spent, transaction_count, avg_amount
FROM v_category_spending
WHERE user_id = 1
ORDER BY total_spent DESC;
```

---

### 3. **v_budget_progress**

Real-time budget progress tracking.

```sql
SELECT budget_name, category_name, limit_amount, spent_amount,
       percent_used, remaining
FROM v_budget_progress
WHERE user_id = 1;
```

---

## ⚡ Database Triggers

### 1. **trg_users_uuid**

Auto-generates UUID for new users.

### 2. **trg_transactions_uuid**

Auto-generates UUID for new transactions.

### 3. **trg_transaction_insert_update_budget**

Updates budget `spent_amount` when new transaction is created.

### 4. **trg_transaction_update_update_budget**

Updates budget `spent_amount` when transaction is modified.

### 5. **trg_transaction_delete_update_budget**

Updates budget `spent_amount` when transaction is deleted.

---

## 🚀 Setup Instructions

### Method 1: Using PowerShell Script (Recommended)

```powershell
# Run the setup script
.\setup-database.ps1
```

This will:

-   ✅ Check MySQL installation
-   ✅ Drop and recreate `trackflow` database
-   ✅ Create all 20+ tables
-   ✅ Create 3 views
-   ✅ Create 5 triggers
-   ✅ Insert default roles and categories

---

### Method 2: Manual Import

```bash
# Using MySQL command line
mysql -u root -p < database/schema/trackflow_full_schema.sql

# Or using phpMyAdmin
# Import: database/schema/trackflow_full_schema.sql
```

---

## 🔐 Security Features

### 1. **Encryption**

-   Account tokens encrypted at rest
-   Account numbers encrypted
-   Sensitive data protected

### 2. **Audit Trail**

-   All critical actions logged in `audits` table
-   IP address and user agent tracking
-   Before/after state comparison

### 3. **Access Control**

-   Role-based permissions (RBAC)
-   User-level data isolation
-   Foreign key constraints

---

## 📈 Performance Optimizations

### Indexes

-   **20+ indexes** for common queries
-   Composite indexes for multi-column lookups
-   Full-text search ready (commented)

### Views

-   Pre-aggregated data for reports
-   Reduced query complexity
-   Faster dashboard loading

### Triggers

-   Automatic budget updates
-   UUID generation
-   Data consistency enforcement

---

## 🧪 Testing the Database

### Check Tables

```sql
USE trackflow;
SHOW TABLES;
```

### View System Categories

```sql
SELECT * FROM categories WHERE is_system = 1;
```

### Check Default Roles

```sql
SELECT * FROM roles;
```

### Test Views

```sql
-- Monthly spending (will be empty initially)
SELECT * FROM v_monthly_spending;

-- Category spending (will be empty initially)
SELECT * FROM v_category_spending;
```

---

## 📊 Database Statistics

After setup, verify:

```sql
-- Count all tables
SELECT COUNT(*) FROM information_schema.tables
WHERE table_schema = 'trackflow';

-- Check table sizes
SELECT
  table_name,
  ROUND((data_length + index_length) / 1024, 2) AS size_kb
FROM information_schema.tables
WHERE table_schema = 'trackflow'
ORDER BY (data_length + index_length) DESC;
```

---

## 🔄 Update Laravel Configuration

After creating the database, update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trackflow
DB_USERNAME=root
DB_PASSWORD=
```

Then clear config cache:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📝 Migration vs Direct SQL

**Why we use direct SQL instead of migrations:**

✅ **Advantages:**

-   Complete database in one script
-   Includes views, triggers, and complex constraints
-   Faster setup (no sequential migrations)
-   Better for development and testing
-   Easy to share complete schema

❌ **Disadvantages:**

-   Not tracked by Laravel's migration system
-   Manual version control required
-   Need to sync with Eloquent models

**Best Practice:** Use this SQL for initial setup, then use Laravel migrations for future changes.

---

## 🎯 Next Steps

After database setup:

1. ✅ **Verify Installation**

    ```bash
    php artisan db:show
    ```

2. ✅ **Test Authentication**

    ```bash
    .\test-auth.ps1
    ```

3. ✅ **Create Admin User**

    ```bash
    php artisan tinker
    User::create([...])
    ```

4. ✅ **Seed Additional Data**
    ```bash
    php artisan db:seed
    ```

---

## 📚 Related Documentation

-   `AUTH_COMPLETE.md` - Authentication API documentation
-   `test-auth.ps1` - API testing script
-   `app/Models/` - Eloquent models
-   `database/migrations/` - Laravel migrations (if used)

---

## 🤝 Support

For issues or questions:

1. Check table structure: `DESCRIBE table_name;`
2. Check foreign keys: `SHOW CREATE TABLE table_name;`
3. Review error logs in `sync_logs` and `audits`

---

**Database Version:** 1.0.0  
**Last Updated:** December 5, 2025  
**Compatibility:** MySQL 5.7+, MariaDB 10.3+
