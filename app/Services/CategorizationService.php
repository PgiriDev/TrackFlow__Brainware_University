<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryRule;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class CategorizationService
{
    protected array $ruleCache = [];

    public function categorizeTransaction(Transaction $transaction): ?int
    {
        // Skip if already categorized
        if ($transaction->category_id) {
            return $transaction->category_id;
        }

        $categoryId = null;

        // Try rule-based matching
        $categoryId = $this->matchByRules($transaction);

        // Try similarity matching if no rule matched
        if (!$categoryId) {
            $categoryId = $this->matchBySimilarity($transaction);
        }

        // Update transaction if category found
        if ($categoryId) {
            $transaction->update(['category_id' => $categoryId]);

            Log::debug("Transaction categorized", [
                'transaction_id' => $transaction->id,
                'category_id' => $categoryId,
            ]);
        }

        return $categoryId;
    }

    public function categorizeMultiple(int $userId, array $transactionIds): array
    {
        $transactions = Transaction::whereIn('id', $transactionIds)
            ->where('user_id', $userId)
            ->get();

        $categorized = 0;
        $uncategorized = 0;

        foreach ($transactions as $transaction) {
            $result = $this->categorizeTransaction($transaction);
            if ($result) {
                $categorized++;
            } else {
                $uncategorized++;
            }
        }

        return [
            'categorized' => $categorized,
            'uncategorized' => $uncategorized,
            'total' => count($transactions),
        ];
    }

    protected function matchByRules(Transaction $transaction): ?int
    {
        $rules = $this->getRules($transaction->user_id);

        foreach ($rules as $rule) {
            if ($rule->matches($transaction->merchant ?? '', $transaction->description)) {
                return $rule->category_id;
            }
        }

        return null;
    }

    protected function matchBySimilarity(Transaction $transaction): ?int
    {
        // Find similar transactions that are already categorized
        $similarTransaction = Transaction::where('user_id', $transaction->user_id)
            ->whereNotNull('category_id')
            ->where('id', '!=', $transaction->id)
            ->where(function ($query) use ($transaction) {
                if ($transaction->merchant) {
                    $query->where('merchant', 'LIKE', '%' . $this->normalize($transaction->merchant) . '%');
                } else {
                    $query->where('description', 'LIKE', '%' . $this->normalize($transaction->description) . '%');
                }
            })
            ->orderBy('date', 'desc')
            ->first();

        return $similarTransaction ? $similarTransaction->category_id : null;
    }

    protected function getRules(int $userId): array
    {
        if (!isset($this->ruleCache[$userId])) {
            $this->ruleCache[$userId] = CategoryRule::forUser($userId)->get();
        }

        return $this->ruleCache[$userId];
    }

    protected function normalize(string $text): string
    {
        // Normalize text for matching
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    public function suggestCategory(Transaction $transaction): array
    {
        $suggestions = [];

        // Rule-based suggestion
        $ruleMatch = $this->matchByRules($transaction);
        if ($ruleMatch) {
            $suggestions[] = [
                'category_id' => $ruleMatch,
                'confidence' => 0.95,
                'reason' => 'rule_match',
            ];
        }

        // Similarity-based suggestion
        $similarMatch = $this->matchBySimilarity($transaction);
        if ($similarMatch && $similarMatch !== $ruleMatch) {
            $suggestions[] = [
                'category_id' => $similarMatch,
                'confidence' => 0.75,
                'reason' => 'similar_transaction',
            ];
        }

        // Keyword-based suggestions
        $keywordMatches = $this->matchByKeywords($transaction);
        foreach ($keywordMatches as $match) {
            if (!in_array($match['category_id'], is_array(array_column($suggestions, 'category_id')) ? array_column($suggestions, 'category_id') : [])) {
                $suggestions[] = $match;
            }
        }

        usort($suggestions, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        return array_slice($suggestions, 0, 3);
    }

    protected function matchByKeywords(Transaction $transaction): array
    {
        $keywords = [
            'food' => ['zomato', 'swiggy', 'restaurant', 'cafe', 'food', 'dominos', 'pizza', 'mcdonalds', 'kfc'],
            'transport' => ['uber', 'ola', 'rapido', 'metro', 'petrol', 'diesel', 'fuel', 'parking'],
            'shopping' => ['amazon', 'flipkart', 'myntra', 'ajio', 'mall', 'store'],
            'entertainment' => ['netflix', 'prime', 'hotstar', 'movie', 'cinema', 'spotify', 'youtube'],
            'bills' => ['electricity', 'water', 'gas', 'internet', 'broadband', 'phone bill'],
            'health' => ['hospital', 'clinic', 'pharmacy', 'medicine', 'doctor', 'apollo', 'fortis'],
            'groceries' => ['grocery', 'supermarket', 'bigbasket', 'dmart', 'reliance fresh'],
        ];

        $text = $this->normalize($transaction->merchant . ' ' . $transaction->description);
        $matches = [];

        foreach ($keywords as $categorySlug => $words) {
            foreach ($words as $word) {
                if (str_contains($text, $word)) {
                    $category = Category::where('user_id', $transaction->user_id)
                        ->orWhereNull('user_id')
                        ->where('name', 'LIKE', '%' . $categorySlug . '%')
                        ->first();

                    if ($category) {
                        $matches[] = [
                            'category_id' => $category->id,
                            'confidence' => 0.6,
                            'reason' => 'keyword_match',
                        ];
                        break 2;
                    }
                }
            }
        }

        return $matches;
    }
}
