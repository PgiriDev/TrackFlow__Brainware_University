<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryRule;
use Illuminate\Database\Seeder;

class CategoryRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            // Food & Dining
            ['patterns' => ['zomato', 'swiggy', 'ubereats', 'dominos', 'pizza', 'mcdonalds', 'kfc', 'subway', 'starbucks', 'cafe coffee day'], 'category' => 'Food & Dining'],

            // Transport
            ['patterns' => ['uber', 'ola', 'rapido', 'metro', 'petrol', 'diesel', 'fuel', 'parking', 'toll'], 'category' => 'Transport'],

            // Shopping
            ['patterns' => ['amazon', 'flipkart', 'myntra', 'ajio', 'nykaa', 'meesho'], 'category' => 'Shopping'],

            // Entertainment
            ['patterns' => ['netflix', 'prime video', 'hotstar', 'spotify', 'youtube premium', 'bookmyshow', 'paytm movies'], 'category' => 'Entertainment'],

            // Bills & Utilities
            ['patterns' => ['electricity', 'water bill', 'gas bill', 'internet', 'broadband', 'wifi'], 'category' => 'Bills & Utilities'],

            // Health & Fitness
            ['patterns' => ['apollo', 'fortis', 'max hospital', 'pharmacy', 'medicine', 'doctor', 'gym', 'cult.fit'], 'category' => 'Health & Fitness'],

            // Groceries
            ['patterns' => ['bigbasket', 'grofers', 'blinkit', 'zepto', 'dmart', 'more supermarket', 'reliance fresh'], 'category' => 'Groceries'],

            // Recharge
            ['patterns' => ['mobile recharge', 'airtel', 'jio', 'vodafone', 'vi recharge'], 'category' => 'Recharge & Top-up'],

            // Transfer
            ['patterns' => ['upi', 'neft', 'imps', 'rtgs', 'transfer to'], 'category' => 'Transfer'],
        ];

        foreach ($rules as $ruleGroup) {
            $category = Category::whereNull('user_id')
                ->where('name', $ruleGroup['category'])
                ->first();

            if (!$category)
                continue;

            foreach ($ruleGroup['patterns'] as $index => $pattern) {
                CategoryRule::create([
                    'user_id' => null, // System rule
                    'category_id' => $category->id,
                    'merchant_pattern' => $pattern,
                    'is_regex' => false,
                    'priority' => 100 - $index, // Higher priority for earlier patterns
                    'is_system' => true,
                ]);
            }
        }
    }
}
