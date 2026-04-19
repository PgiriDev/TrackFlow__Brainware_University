<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations - Add performance indexes for faster queries.
     */
    public function up(): void
    {
        // Transactions table indexes
        Schema::table('transactions', function (Blueprint $table) {
            // Only add indexes if they don't exist
            if (!$this->hasIndex('transactions', 'transactions_user_id_index')) {
                $table->index('user_id', 'transactions_user_id_index');
            }
            if (!$this->hasIndex('transactions', 'transactions_category_id_index')) {
                $table->index('category_id', 'transactions_category_id_index');
            }
            if (!$this->hasIndex('transactions', 'transactions_date_index')) {
                $table->index('date', 'transactions_date_index');
            }
            if (!$this->hasIndex('transactions', 'transactions_type_index')) {
                $table->index('type', 'transactions_type_index');
            }
            if (!$this->hasIndex('transactions', 'transactions_user_date_index')) {
                $table->index(['user_id', 'date'], 'transactions_user_date_index');
            }
            if (!$this->hasIndex('transactions', 'transactions_user_type_index')) {
                $table->index(['user_id', 'type'], 'transactions_user_type_index');
            }
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            if (!$this->hasIndex('categories', 'categories_user_id_index')) {
                $table->index('user_id', 'categories_user_id_index');
            }
            if (!$this->hasIndex('categories', 'categories_user_type_index')) {
                $table->index(['user_id', 'type'], 'categories_user_type_index');
            }
        });

        // Budgets table indexes
        Schema::table('budgets', function (Blueprint $table) {
            if (!$this->hasIndex('budgets', 'budgets_user_id_index')) {
                $table->index('user_id', 'budgets_user_id_index');
            }
            if (!$this->hasIndex('budgets', 'budgets_user_year_month_index')) {
                $table->index(['user_id', 'year', 'month'], 'budgets_user_year_month_index');
            }
        });

        // Goals table indexes
        Schema::table('goals', function (Blueprint $table) {
            if (!$this->hasIndex('goals', 'goals_user_id_index')) {
                $table->index('user_id', 'goals_user_id_index');
            }
        });

        // Notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->hasIndex('notifications', 'notifications_user_id_index')) {
                $table->index('user_id', 'notifications_user_id_index');
            }
            if (!$this->hasIndex('notifications', 'notifications_user_read_index')) {
                $table->index(['user_id', 'is_read'], 'notifications_user_read_index');
            }
        });

        // Group members table indexes
        if (Schema::hasTable('group_members')) {
            Schema::table('group_members', function (Blueprint $table) {
                if (!$this->hasIndex('group_members', 'group_members_group_id_index')) {
                    $table->index('group_id', 'group_members_group_id_index');
                }
                if (!$this->hasIndex('group_members', 'group_members_user_id_index')) {
                    $table->index('user_id', 'group_members_user_id_index');
                }
            });
        }

        // User settings table indexes
        if (Schema::hasTable('user_settings')) {
            Schema::table('user_settings', function (Blueprint $table) {
                if (!$this->hasIndex('user_settings', 'user_settings_user_id_index')) {
                    $table->index('user_id', 'user_settings_user_id_index');
                }
            });
        }

        // User preferences table indexes
        if (Schema::hasTable('user_preferences')) {
            Schema::table('user_preferences', function (Blueprint $table) {
                if (!$this->hasIndex('user_preferences', 'user_preferences_user_id_index')) {
                    $table->index('user_id', 'user_preferences_user_id_index');
                }
            });
        }

        // Bank accounts table indexes
        if (Schema::hasTable('bank_accounts')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                if (!$this->hasIndex('bank_accounts', 'bank_accounts_user_id_index')) {
                    $table->index('user_id', 'bank_accounts_user_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_id_index');
            $table->dropIndex('transactions_category_id_index');
            $table->dropIndex('transactions_date_index');
            $table->dropIndex('transactions_type_index');
            $table->dropIndex('transactions_user_date_index');
            $table->dropIndex('transactions_user_type_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_user_id_index');
            $table->dropIndex('categories_user_type_index');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex('budgets_user_id_index');
            $table->dropIndex('budgets_user_year_month_index');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex('goals_user_id_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_id_index');
            $table->dropIndex('notifications_user_read_index');
        });

        if (Schema::hasTable('group_members')) {
            Schema::table('group_members', function (Blueprint $table) {
                $table->dropIndex('group_members_group_id_index');
                $table->dropIndex('group_members_user_id_index');
            });
        }

        if (Schema::hasTable('user_settings')) {
            Schema::table('user_settings', function (Blueprint $table) {
                $table->dropIndex('user_settings_user_id_index');
            });
        }

        if (Schema::hasTable('user_preferences')) {
            Schema::table('user_preferences', function (Blueprint $table) {
                $table->dropIndex('user_preferences_user_id_index');
            });
        }

        if (Schema::hasTable('bank_accounts')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->dropIndex('bank_accounts_user_id_index');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
