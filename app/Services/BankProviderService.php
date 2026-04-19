<?php

namespace App\Services;

use App\Models\AccountToken;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BankProviderService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;
    protected string $provider;

    public function __construct()
    {
        $this->apiKey = config("services.{$this->provider}.api_key");
        $this->apiSecret = config("services.{$this->provider}.api_secret");
        $this->baseUrl = config("services.{$this->provider}.base_url");
    }

    /**
     * Generate a link session for the user to connect their bank account
     */
    abstract public function createLinkSession(int $userId, array $options = []): array;

    /**
     * Exchange authorization code for access token
     */
    abstract public function exchangeToken(string $authCode): array;

    /**
     * Fetch account details
     */
    abstract public function fetchAccounts(string $accessToken): array;

    /**
     * Fetch transactions for an account
     */
    abstract public function fetchTransactions(
        string $accessToken,
        string $accountId,
        ?\DateTime $fromDate = null,
        ?\DateTime $toDate = null
    ): array;

    /**
     * Refresh expired access token
     */
    abstract public function refreshAccessToken(string $refreshToken): array;

    /**
     * Revoke access to linked account
     */
    abstract public function revokeAccess(string $accessToken): bool;

    /**
     * Refresh token if needed
     */
    public function refreshIfNeeded(AccountToken $token): AccountToken
    {
        if (!$token->needsRefresh()) {
            return $token;
        }

        try {
            $response = $this->refreshAccessToken($token->refresh_token);

            $token->update([
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'] ?? $token->refresh_token,
                'expires_at' => now()->addSeconds($response['expires_in'] ?? 3600),
            ]);

            Log::info("Token refreshed for bank account", [
                'bank_account_id' => $token->bank_account_id
            ]);

            return $token->fresh();
        } catch (\Exception $e) {
            Log::error("Failed to refresh token", [
                'bank_account_id' => $token->bank_account_id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Make HTTP request with error handling
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = [])
    {
        $url = $this->baseUrl . $endpoint;

        $defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = Http::withHeaders($headers)
                        ->timeout(30)
                ->$method($url, $data);

            if ($response->failed()) {
                Log::error("API request failed", [
                    'provider' => $this->provider,
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                throw new \Exception("API request failed: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("API request exception", [
                'provider' => $this->provider,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Normalize transaction data from provider format
     */
    protected function normalizeTransaction(array $providerData): array
    {
        // Override in provider-specific implementations
        return $providerData;
    }

    /**
     * Normalize account data from provider format
     */
    protected function normalizeAccount(array $providerData): array
    {
        // Override in provider-specific implementations
        return $providerData;
    }
}
