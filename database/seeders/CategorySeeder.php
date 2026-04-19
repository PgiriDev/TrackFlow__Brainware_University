<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 40 Expense Categories (20 existing + 20 new)
        $expenseCategories = [
            // Original 20
            ['name' => 'Food & Dining', 'color' => '#EF4444', 'icon' => 'fa-utensils', 'type' => 'expense'],
            ['name' => 'Groceries', 'color' => '#84CC16', 'icon' => 'fa-shopping-cart', 'type' => 'expense'],
            ['name' => 'Transportation', 'color' => '#3B82F6', 'icon' => 'fa-car', 'type' => 'expense'],
            ['name' => 'Fuel & Gas', 'color' => '#F97316', 'icon' => 'fa-gas-pump', 'type' => 'expense'],
            ['name' => 'Shopping', 'color' => '#8B5CF6', 'icon' => 'fa-bag-shopping', 'type' => 'expense'],
            ['name' => 'Bills & Utilities', 'color' => '#F59E0B', 'icon' => 'fa-file-invoice', 'type' => 'expense'],
            ['name' => 'Electricity', 'color' => '#FBBF24', 'icon' => 'fa-bolt', 'type' => 'expense'],
            ['name' => 'Internet & Phone', 'color' => '#06B6D4', 'icon' => 'fa-wifi', 'type' => 'expense'],
            ['name' => 'Rent & Housing', 'color' => '#A855F7', 'icon' => 'fa-house', 'type' => 'expense'],
            ['name' => 'Insurance', 'color' => '#14B8A6', 'icon' => 'fa-shield-halved', 'type' => 'expense'],
            ['name' => 'Healthcare', 'color' => '#10B981', 'icon' => 'fa-heart-pulse', 'type' => 'expense'],
            ['name' => 'Medicine & Pharmacy', 'color' => '#34D399', 'icon' => 'fa-pills', 'type' => 'expense'],
            ['name' => 'Entertainment', 'color' => '#FB7185', 'icon' => 'fa-gamepad', 'type' => 'expense'],
            ['name' => 'Subscriptions', 'color' => '#E879F9', 'icon' => 'fa-repeat', 'type' => 'expense'],
            ['name' => 'Education', 'color' => '#2DD4BF', 'icon' => 'fa-graduation-cap', 'type' => 'expense'],
            ['name' => 'Travel & Vacation', 'color' => '#38BDF8', 'icon' => 'fa-plane', 'type' => 'expense'],
            ['name' => 'Personal Care', 'color' => '#FDA4AF', 'icon' => 'fa-spa', 'type' => 'expense'],
            ['name' => 'Gifts & Donations', 'color' => '#FB923C', 'icon' => 'fa-gift', 'type' => 'expense'],
            ['name' => 'Kids & Family', 'color' => '#C084FC', 'icon' => 'fa-baby', 'type' => 'expense'],
            ['name' => 'Other Expenses', 'color' => '#6B7280', 'icon' => 'fa-ellipsis', 'type' => 'expense'],
            // New 20 Expense Categories
            ['name' => 'Public Transit', 'color' => '#0EA5E9', 'icon' => 'fa-bus', 'type' => 'expense'],
            ['name' => 'Taxi & Rideshare', 'color' => '#0284C7', 'icon' => 'fa-taxi', 'type' => 'expense'],
            ['name' => 'Parking', 'color' => '#0369A1', 'icon' => 'fa-square-parking', 'type' => 'expense'],
            ['name' => 'Vehicle Maintenance', 'color' => '#075985', 'icon' => 'fa-car-side', 'type' => 'expense'],
            ['name' => 'Clothing & Apparel', 'color' => '#EC4899', 'icon' => 'fa-shirt', 'type' => 'expense'],
            ['name' => 'Electronics', 'color' => '#6366F1', 'icon' => 'fa-laptop', 'type' => 'expense'],
            ['name' => 'Home Furnishing', 'color' => '#7C3AED', 'icon' => 'fa-couch', 'type' => 'expense'],
            ['name' => 'Water Bill', 'color' => '#22D3EE', 'icon' => 'fa-droplet', 'type' => 'expense'],
            ['name' => 'Gas Bill', 'color' => '#F43F5E', 'icon' => 'fa-fire-flame-simple', 'type' => 'expense'],
            ['name' => 'Home Maintenance', 'color' => '#D946EF', 'icon' => 'fa-screwdriver-wrench', 'type' => 'expense'],
            ['name' => 'Fitness & Gym', 'color' => '#22C55E', 'icon' => 'fa-dumbbell', 'type' => 'expense'],
            ['name' => 'Doctor Visits', 'color' => '#059669', 'icon' => 'fa-user-doctor', 'type' => 'expense'],
            ['name' => 'Movies & Shows', 'color' => '#F43F5E', 'icon' => 'fa-film', 'type' => 'expense'],
            ['name' => 'Music & Audio', 'color' => '#A855F7', 'icon' => 'fa-music', 'type' => 'expense'],
            ['name' => 'Books & Courses', 'color' => '#5EEAD4', 'icon' => 'fa-book', 'type' => 'expense'],
            ['name' => 'Hotels & Accommodation', 'color' => '#7DD3FC', 'icon' => 'fa-hotel', 'type' => 'expense'],
            ['name' => 'Pets', 'color' => '#A3E635', 'icon' => 'fa-paw', 'type' => 'expense'],
            ['name' => 'Charity', 'color' => '#F472B6', 'icon' => 'fa-hand-holding-heart', 'type' => 'expense'],
            ['name' => 'Bank Fees', 'color' => '#94A3B8', 'icon' => 'fa-building-columns', 'type' => 'expense'],
            ['name' => 'Taxes', 'color' => '#64748B', 'icon' => 'fa-landmark-dome', 'type' => 'expense'],
        ];

        // 49 Income Categories (30 existing + 19 new)
        $incomeCategories = [
            // Original 30
            ['name' => 'Salary', 'color' => '#22C55E', 'icon' => 'fa-briefcase', 'type' => 'income'],
            ['name' => 'Bonus', 'color' => '#10B981', 'icon' => 'fa-gift', 'type' => 'income'],
            ['name' => 'Overtime Pay', 'color' => '#059669', 'icon' => 'fa-clock', 'type' => 'income'],
            ['name' => 'Commission', 'color' => '#D946EF', 'icon' => 'fa-hand-holding-dollar', 'type' => 'income'],
            ['name' => 'Tips & Gratuity', 'color' => '#F472B6', 'icon' => 'fa-coins', 'type' => 'income'],
            ['name' => 'Freelance', 'color' => '#14B8A6', 'icon' => 'fa-laptop-code', 'type' => 'income'],
            ['name' => 'Consulting', 'color' => '#0EA5E9', 'icon' => 'fa-handshake', 'type' => 'income'],
            ['name' => 'Contract Work', 'color' => '#0284C7', 'icon' => 'fa-file-signature', 'type' => 'income'],
            ['name' => 'Business Income', 'color' => '#06B6D4', 'icon' => 'fa-store', 'type' => 'income'],
            ['name' => 'Self Employment', 'color' => '#0891B2', 'icon' => 'fa-user-tie', 'type' => 'income'],
            ['name' => 'Side Hustle', 'color' => '#EC4899', 'icon' => 'fa-rocket', 'type' => 'income'],
            ['name' => 'Part-time Job', 'color' => '#BE185D', 'icon' => 'fa-business-time', 'type' => 'income'],
            ['name' => 'Investments', 'color' => '#3B82F6', 'icon' => 'fa-chart-line', 'type' => 'income'],
            ['name' => 'Dividends', 'color' => '#6366F1', 'icon' => 'fa-chart-pie', 'type' => 'income'],
            ['name' => 'Interest Income', 'color' => '#8B5CF6', 'icon' => 'fa-percent', 'type' => 'income'],
            ['name' => 'Capital Gains', 'color' => '#7C3AED', 'icon' => 'fa-arrow-trend-up', 'type' => 'income'],
            ['name' => 'Stock Sales', 'color' => '#4F46E5', 'icon' => 'fa-money-bill-trend-up', 'type' => 'income'],
            ['name' => 'Crypto Income', 'color' => '#F59E0B', 'icon' => 'fa-bitcoin-sign', 'type' => 'income'],
            ['name' => 'Rental Income', 'color' => '#A855F7', 'icon' => 'fa-building', 'type' => 'income'],
            ['name' => 'Property Sale', 'color' => '#9333EA', 'icon' => 'fa-house-circle-check', 'type' => 'income'],
            ['name' => 'Royalties', 'color' => '#F43F5E', 'icon' => 'fa-crown', 'type' => 'income'],
            ['name' => 'Cashback & Rewards', 'color' => '#F97316', 'icon' => 'fa-piggy-bank', 'type' => 'income'],
            ['name' => 'Refunds', 'color' => '#EA580C', 'icon' => 'fa-rotate-left', 'type' => 'income'],
            ['name' => 'Reimbursements', 'color' => '#DC2626', 'icon' => 'fa-receipt', 'type' => 'income'],
            ['name' => 'Gifts Received', 'color' => '#EAB308', 'icon' => 'fa-heart', 'type' => 'income'],
            ['name' => 'Inheritance', 'color' => '#CA8A04', 'icon' => 'fa-scroll', 'type' => 'income'],
            ['name' => 'Pension', 'color' => '#84CC16', 'icon' => 'fa-user-clock', 'type' => 'income'],
            ['name' => 'Government Benefits', 'color' => '#65A30D', 'icon' => 'fa-landmark', 'type' => 'income'],
            ['name' => 'Tax Refund', 'color' => '#34D399', 'icon' => 'fa-file-invoice-dollar', 'type' => 'income'],
            ['name' => 'Other Income', 'color' => '#9CA3AF', 'icon' => 'fa-plus', 'type' => 'income'],
            // New 19 Income Categories
            ['name' => 'Allowance', 'color' => '#10B981', 'icon' => 'fa-money-bill-wave', 'type' => 'income'],
            ['name' => 'Alimony', 'color' => '#14B8A6', 'icon' => 'fa-scale-balanced', 'type' => 'income'],
            ['name' => 'Child Support', 'color' => '#06B6D4', 'icon' => 'fa-children', 'type' => 'income'],
            ['name' => 'Scholarship', 'color' => '#0EA5E9', 'icon' => 'fa-award', 'type' => 'income'],
            ['name' => 'Grant', 'color' => '#3B82F6', 'icon' => 'fa-hand-holding-medical', 'type' => 'income'],
            ['name' => 'Prize & Winnings', 'color' => '#FBBF24', 'icon' => 'fa-trophy', 'type' => 'income'],
            ['name' => 'Lottery', 'color' => '#F59E0B', 'icon' => 'fa-ticket', 'type' => 'income'],
            ['name' => 'Sponsorship', 'color' => '#6366F1', 'icon' => 'fa-bullhorn', 'type' => 'income'],
            ['name' => 'Affiliate Income', 'color' => '#8B5CF6', 'icon' => 'fa-link', 'type' => 'income'],
            ['name' => 'YouTube Revenue', 'color' => '#EF4444', 'icon' => 'fa-video', 'type' => 'income'],
            ['name' => 'Content Creation', 'color' => '#EC4899', 'icon' => 'fa-pen-fancy', 'type' => 'income'],
            ['name' => 'Online Sales', 'color' => '#A855F7', 'icon' => 'fa-cart-shopping', 'type' => 'income'],
            ['name' => 'Selling Items', 'color' => '#2DD4BF', 'icon' => 'fa-tag', 'type' => 'income'],
            ['name' => 'Insurance Payout', 'color' => '#22C55E', 'icon' => 'fa-shield-heart', 'type' => 'income'],
            ['name' => 'Legal Settlement', 'color' => '#64748B', 'icon' => 'fa-gavel', 'type' => 'income'],
            ['name' => 'Loan Received', 'color' => '#475569', 'icon' => 'fa-money-check', 'type' => 'income'],
            ['name' => 'Crowdfunding', 'color' => '#FB7185', 'icon' => 'fa-users', 'type' => 'income'],
            ['name' => 'Tutoring', 'color' => '#38BDF8', 'icon' => 'fa-chalkboard-user', 'type' => 'income'],
            ['name' => 'Music Earnings', 'color' => '#C084FC', 'icon' => 'fa-compact-disc', 'type' => 'income'],
        ];

        $order = 0;

        // Insert expense categories
        foreach ($expenseCategories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name'], 'user_id' => null],
                [
                    ...$category,
                    'user_id' => null,
                    'is_system' => true,
                    'order' => $order++,
                ]
            );
        }

        // Insert income categories
        foreach ($incomeCategories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name'], 'user_id' => null],
                [
                    ...$category,
                    'user_id' => null,
                    'is_system' => true,
                    'order' => $order++,
                ]
            );
        }
    }
}
