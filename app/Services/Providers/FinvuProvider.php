<?php

namespace App\Services\Providers;

use App\Services\BankProviderService;

class FinvuProvider extends BankProviderService
{
    protected string $provider = 'finvu';

    public function createLinkSession(int $userId, array $options = []): array
    {
        $response = $this->makeRequest('POST', '/consent/create', [
            'user_id' => $userId,
            'redirect_url' => route('bank.callback'),
            'purposes' => ['transactions', 'account_details'],
            ...($options['country'] ? ['country' => $options['country']] : []),
        ], [
            'X-API-Key' => $this->apiKey,
        ]);

        return [
            'link_url' => $response['consent_url'],
            'link_token' => $response['consent_id'],
            'expires_at' => $response['expires_at'] ?? now()->addMinutes(15)->toISOString(),
        ];
    }

    public function exchangeToken(string $consentId): array
    {
        $response = $this->makeRequest('POST', '/token/exchange', [
            'consent_id' => $consentId,
        ], [
            'X-API-Key' => $this->apiKey,
        ]);

        return [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? null,
            'expires_in' => $response['expires_in'] ?? 3600,
            'account_id' => $response['account_id'],
        ];
    }

    public function fetchAccounts(string $accessToken): array
    {
        $response = $this->makeRequest('GET', '/accounts', [], [
            'Authorization' => "Bearer {$accessToken}",
            'X-API-Key' => $this->apiKey,
        ]);

        return array_map(fn($account) => $this->normalizeAccount($account), $response['accounts'] ?? []);
    }

    public function fetchTransactions(
        string $accessToken,
        string $accountId,
        ?\DateTime $fromDate = null,
        ?\DateTime $toDate = null
    ): array {
        $params = [
            'account_id' => $accountId,
            'from_date' => $fromDate?->format('Y-m-d') ?? now()->subDays(90)->format('Y-m-d'),
            'to_date' => $toDate?->format('Y-m-d') ?? now()->format('Y-m-d'),
        ];

        $response = $this->makeRequest('GET', '/transactions?' . http_build_query($params), [], [
            'Authorization' => "Bearer {$accessToken}",
            'X-API-Key' => $this->apiKey,
        ]);

        return array_map(fn($tx) => $this->normalizeTransaction($tx), $response['transactions'] ?? []);
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $response = $this->makeRequest('POST', '/token/refresh', [
            'refresh_token' => $refreshToken,
        ], [
            'X-API-Key' => $this->apiKey,
        ]);

        return [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? $refreshToken,
            'expires_in' => $response['expires_in'] ?? 3600,
        ];
    }

    public function revokeAccess(string $accessToken): bool
    {
        try {
            $this->makeRequest('POST', '/consent/revoke', [], [
                'Authorization' => "Bearer {$accessToken}",
                'X-API-Key' => $this->apiKey,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function normalizeAccount(array $providerData): array
    {
        return [
            'provider_account_id' => $providerData['account_id'],
            'bank_name' => $providerData['bank_name'] ?? $providerData['institution_name'],
            'account_number_masked' => $providerData['masked_account_number'],
            'account_type' => $providerData['account_type'],
            'balance' => $providerData['current_balance'],
            'currency' => $providerData['currency'] ?? 'INR',
        ];
    }

    protected function normalizeTransaction(array $providerData): array
    {
        return [
            'provider_tx_id' => $providerData['transaction_id'],
            'date' => $providerData['date'],
            'description' => $providerData['description'] ?? $providerData['narration'],
            'merchant' => $providerData['merchant_name'] ?? $this->extractMerchant($providerData['description']),
            'amount' => abs((float) $providerData['amount']),
            'currency' => $providerData['currency'] ?? 'INR',
            'type' => ($providerData['amount'] < 0 || $providerData['type'] === 'debit') ? 'debit' : 'credit',
            'raw_data' => $providerData,
        ];
    }

    private function extractMerchant(string $description): string
    {
        // Simple merchant extraction logic
        $parts = explode(' ', $description);
        return implode(' ', array_slice($parts, 0, 3));
    }
}
