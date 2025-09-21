<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    private ?string $apiToken;
    private ?string $zoneId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token');
        $this->zoneId = config('services.cloudflare.zone_id');
        $this->baseUrl = 'https://api.cloudflare.com/client/v4';
    }

    /**
     * Check if Cloudflare is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiToken) && !empty($this->zoneId);
    }

    /**
     * Create a DNS record for a subdomain
     */
    public function createDnsRecord(string $subdomain, string $ipAddress = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $ipAddress = $ipAddress ?: $this->getServerIpAddress();
            $recordName = $subdomain;
            $recordType = 'A';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/zones/{$this->zoneId}/dns_records", [
                'type' => $recordType,
                'name' => $recordName,
                'content' => $ipAddress,
                'ttl' => 1, // Auto TTL
                'proxied' => true, // Enable Cloudflare proxy
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('DNS record created successfully', [
                    'subdomain' => $subdomain,
                    'record_id' => $data['result']['id'],
                    'ip_address' => $ipAddress
                ]);

                return [
                    'success' => true,
                    'record_id' => $data['result']['id'],
                    'message' => 'DNS record created successfully'
                ];
            }

            $error = $response->json();
            Log::error('Failed to create DNS record', [
                'subdomain' => $subdomain,
                'error' => $error
            ]);

            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Failed to create DNS record'
            ];

        } catch (\Exception $e) {
            Log::error('Cloudflare API error', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Cloudflare API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a DNS record
     */
    public function updateDnsRecord(string $recordId, string $subdomain, string $ipAddress = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $ipAddress = $ipAddress ?: $this->getServerIpAddress();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->put("{$this->baseUrl}/zones/{$this->zoneId}/dns_records/{$recordId}", [
                'type' => 'A',
                'name' => $subdomain,
                'content' => $ipAddress,
                'ttl' => 1,
                'proxied' => true,
            ]);

            if ($response->successful()) {
                Log::info('DNS record updated successfully', [
                    'subdomain' => $subdomain,
                    'record_id' => $recordId,
                    'ip_address' => $ipAddress
                ]);

                return [
                    'success' => true,
                    'message' => 'DNS record updated successfully'
                ];
            }

            $error = $response->json();
            Log::error('Failed to update DNS record', [
                'subdomain' => $subdomain,
                'record_id' => $recordId,
                'error' => $error
            ]);

            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Failed to update DNS record'
            ];

        } catch (\Exception $e) {
            Log::error('Cloudflare API error', [
                'subdomain' => $subdomain,
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Cloudflare API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a DNS record
     */
    public function deleteDnsRecord(string $recordId): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->delete("{$this->baseUrl}/zones/{$this->zoneId}/dns_records/{$recordId}");

            if ($response->successful()) {
                Log::info('DNS record deleted successfully', [
                    'record_id' => $recordId
                ]);

                return [
                    'success' => true,
                    'message' => 'DNS record deleted successfully'
                ];
            }

            $error = $response->json();
            Log::error('Failed to delete DNS record', [
                'record_id' => $recordId,
                'error' => $error
            ]);

            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Failed to delete DNS record'
            ];

        } catch (\Exception $e) {
            Log::error('Cloudflare API error', [
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Cloudflare API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get DNS records for a subdomain
     */
    public function getDnsRecords(string $subdomain): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get("{$this->baseUrl}/zones/{$this->zoneId}/dns_records", [
                'name' => $subdomain,
                'type' => 'A'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'records' => $data['result'] ?? []
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Failed to get DNS records'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Cloudflare API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Purge cache for a subdomain
     */
    public function purgeCache(string $subdomain): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/zones/{$this->zoneId}/purge_cache", [
                'purge_everything' => true
            ]);

            if ($response->successful()) {
                Log::info('Cache purged successfully', [
                    'subdomain' => $subdomain
                ]);

                return [
                    'success' => true,
                    'message' => 'Cache purged successfully'
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Failed to purge cache'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Cloudflare API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get server IP address
     */
    private function getServerIpAddress(): string
    {
        // Try to get the server's public IP
        $ip = file_get_contents('https://api.ipify.org');
        return $ip ?: '127.0.0.1';
    }

    /**
     * Test Cloudflare connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Cloudflare not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get("{$this->baseUrl}/zones/{$this->zoneId}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'zone_name' => $data['result']['name'] ?? 'Unknown'
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['errors'][0]['message'] ?? 'Connection failed'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }
}
