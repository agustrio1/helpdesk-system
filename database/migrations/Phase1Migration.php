<?php
namespace Database\migrations;

class Phase1Migration {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create migrations tracking table
     */
    public function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
    }

    /**
     * Check if migration has been run
     */
    private function hasRun($migration) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->execute([$migration]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Mark migration as run
     */
    private function markAsRun($migration) {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (?) ON DUPLICATE KEY UPDATE id=id");
        $stmt->execute([$migration]);
    }

    /**
     * 1. Create categories table
     */
    public function createCategoriesTable() {
        $migrationName = 'create_categories_table';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            color VARCHAR(7) DEFAULT '#3B82F6',
            icon VARCHAR(50) DEFAULT 'folder',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 2. Add category_id to tickets table
     */
    public function addCategoryToTickets() {
        $migrationName = 'add_category_to_tickets';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        // Check if column exists
        $stmt = $this->pdo->query("SHOW COLUMNS FROM tickets LIKE 'category_id'");
        if ($stmt->rowCount() > 0) {
            $this->markAsRun($migrationName);
            return false;
        }

        $sql = "ALTER TABLE tickets 
                ADD COLUMN category_id INT NULL AFTER priority,
                ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                ADD INDEX idx_category_id (category_id)";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 3. Create activity_logs table
     */
    public function createActivityLogsTable() {
        $migrationName = 'create_activity_logs_table';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ticket_id INT NOT NULL,
            user_id INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            field_name VARCHAR(50) NULL,
            old_value TEXT NULL,
            new_value TEXT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_ticket_id (ticket_id),
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 4. Create email_notifications table
     */
    public function createEmailNotificationsTable() {
        $migrationName = 'create_email_notifications_table';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS email_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ticket_id INT NULL,
            type VARCHAR(50) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            sent_at TIMESTAMP NULL,
            error_message TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_ticket_id (ticket_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 5. Create notification_preferences table
     */
    public function createNotificationPreferencesTable() {
        $migrationName = 'create_notification_preferences_table';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS notification_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            ticket_created BOOLEAN DEFAULT TRUE,
            ticket_updated BOOLEAN DEFAULT TRUE,
            ticket_assigned BOOLEAN DEFAULT TRUE,
            comment_added BOOLEAN DEFAULT TRUE,
            status_changed BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 6. Add profile fields to users table
     */
    public function addProfileFieldsToUsers() {
        $migrationName = 'add_profile_fields_to_users';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        // Check if columns exist
        $stmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'avatar'");
        if ($stmt->rowCount() > 0) {
            $this->markAsRun($migrationName);
            return false;
        }

        $sql = "ALTER TABLE users 
                ADD COLUMN avatar VARCHAR(255) NULL AFTER role,
                ADD COLUMN phone VARCHAR(20) NULL AFTER avatar,
                ADD COLUMN bio TEXT NULL AFTER phone,
                ADD COLUMN timezone VARCHAR(50) DEFAULT 'UTC' AFTER bio,
                ADD COLUMN last_login TIMESTAMP NULL AFTER timezone";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * 7. Add full-text search index
     */
    public function addFullTextSearchIndex() {
        $migrationName = 'add_fulltext_search_index';
        
        if ($this->hasRun($migrationName)) {
            return false;
        }

        // Check if fulltext index exists
        $stmt = $this->pdo->query("SHOW INDEX FROM tickets WHERE Key_name = 'idx_fulltext_search'");
        if ($stmt->rowCount() > 0) {
            $this->markAsRun($migrationName);
            return false;
        }

        $sql = "ALTER TABLE tickets 
                ADD FULLTEXT INDEX idx_fulltext_search (title, description)";

        $this->pdo->exec($sql);
        $this->markAsRun($migrationName);
        return true;
    }

    /**
     * Seed default categories
     */
    public function seedCategories() {
        $categories = [
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues, bugs, and software problems',
                'color' => '#3B82F6',
                'icon' => 'cpu'
            ],
            [
                'name' => 'Billing & Payment',
                'slug' => 'billing-payment',
                'description' => 'Payment issues, invoices, and billing questions',
                'color' => '#10B981',
                'icon' => 'credit-card'
            ],
            [
                'name' => 'Account Management',
                'slug' => 'account-management',
                'description' => 'Account settings, password resets, and profile issues',
                'color' => '#F59E0B',
                'icon' => 'user'
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'Suggestions for new features or improvements',
                'color' => '#8B5CF6',
                'icon' => 'lightbulb'
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general-inquiry',
                'description' => 'General questions and other topics',
                'color' => '#6B7280',
                'icon' => 'help-circle'
            ]
        ];

        $sql = "INSERT INTO categories (name, slug, description, color, icon) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE id=id";

        $stmt = $this->pdo->prepare($sql);

        foreach ($categories as $category) {
            $stmt->execute([
                $category['name'],
                $category['slug'],
                $category['description'],
                $category['color'],
                $category['icon']
            ]);
        }
    }

    /**
     * Run all Phase 1 migrations
     */
    public function runAll() {
        $results = [];

        echo "Starting Phase 1 migrations...\n\n";

        // Create migrations tracking table first
        $this->createMigrationsTable();
        echo "✓ Migrations tracking table ready\n";

        // Run each migration
        if ($this->createCategoriesTable()) {
            echo "✓ Categories table created\n";
            $results[] = 'categories_created';
        } else {
            echo "⊘ Categories table already exists\n";
        }

        if ($this->addCategoryToTickets()) {
            echo "✓ Category field added to tickets\n";
            $results[] = 'category_added';
        } else {
            echo "⊘ Category field already exists in tickets\n";
        }

        if ($this->createActivityLogsTable()) {
            echo "✓ Activity logs table created\n";
            $results[] = 'activity_logs_created';
        } else {
            echo "⊘ Activity logs table already exists\n";
        }

        if ($this->createEmailNotificationsTable()) {
            echo "✓ Email notifications table created\n";
            $results[] = 'email_notifications_created';
        } else {
            echo "⊘ Email notifications table already exists\n";
        }

        if ($this->createNotificationPreferencesTable()) {
            echo "✓ Notification preferences table created\n";
            $results[] = 'notification_prefs_created';
        } else {
            echo "⊘ Notification preferences table already exists\n";
        }

        if ($this->addProfileFieldsToUsers()) {
            echo "✓ Profile fields added to users\n";
            $results[] = 'profile_fields_added';
        } else {
            echo "⊘ Profile fields already exist in users\n";
        }

        if ($this->addFullTextSearchIndex()) {
            echo "✓ Full-text search index created\n";
            $results[] = 'fulltext_index_created';
        } else {
            echo "⊘ Full-text search index already exists\n";
        }

        // Seed categories
        echo "\nSeeding data...\n";
        $this->seedCategories();
        echo "✓ Default categories seeded\n";

        return $results;
    }
}