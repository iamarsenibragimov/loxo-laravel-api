<?php

/**
 * Development testing script for Loxo API package
 * 
 * Usage:
 * php dev-bootstrap.php                     # Run all tests
 * make quick                                # Same as above
 * php -r "require 'dev-bootstrap.php'; testActivityTypes();"  # Test specific endpoint
 */

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Loxo\LaravelApi\Services\LoxoApiService;
use Loxo\LaravelApi\Exceptions\LoxoApiException;

// Load .env file if exists (simple version)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            putenv(trim($line));
        }
    }
}

// Environment variable helper function (Laravel compatibility)
function env($key, $default = null)
{
    return getenv($key) ?: $default;
}

// Load configuration from package config file
$config = require __DIR__ . '/config/loxo.php';

// Create a simplified service that doesn't depend on Laravel
class SimpleLoxoService
{
    private $client;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $baseUrl = str_replace(
            ['{domain}', '{agency_slug}'],
            [$this->config['domain'], $this->config['agency_slug']],
            $this->config['base_url']
        );

        $this->client = new Client([
            'base_uri' => $baseUrl . '/',
            'timeout' => $this->config['timeout'],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getActivityTypes(array $params = []): array
    {
        return $this->get('activity_types', $params);
    }

    public function getAddressTypes(array $params = []): array
    {
        return $this->get('address_types', $params);
    }

    public function get(string $endpoint, array $params = []): array
    {
        $response = $this->client->request('GET', $endpoint, ['query' => $params]);
        return json_decode($response->getBody(), true) ?? [];
    }

    public function getDomain(): string
    {
        return $this->config['domain'];
    }

    public function getAgencySlug(): string
    {
        return $this->config['agency_slug'];
    }

    public function getBaseUrl(): string
    {
        return str_replace(
            ['{domain}', '{agency_slug}'],
            [$this->config['domain'], $this->config['agency_slug']],
            $this->config['base_url']
        );
    }
}

// Create service instance
$loxoService = new SimpleLoxoService($config);



// Quick test functions using LoxoApiService
function testActivityTypes(): array
{
    global $loxoService;

    try {
        $data = $loxoService->getActivityTypes();
        echo "✅ Activity Types: " . count($data) . " records\n";
        return $data;
    } catch (LoxoApiException $e) {
        echo "❌ Activity Types Error: " . $e->getMessage() . "\n";
        return [];
    }
}

function testAddressTypes(): array
{
    global $loxoService;

    try {
        $data = $loxoService->getAddressTypes();
        echo "✅ Address Types: " . count($data) . " records\n";
        return $data;
    } catch (LoxoApiException $e) {
        echo "❌ Address Types Error: " . $e->getMessage() . "\n";
        return [];
    }
}

function testWithParams(array $params = []): array
{
    global $loxoService;

    try {
        $data = $loxoService->getActivityTypes($params);
        echo "✅ Activity Types (with params): " . count($data) . " records\n";
        echo "   Params: " . json_encode($params) . "\n";
        return $data;
    } catch (LoxoApiException $e) {
        echo "❌ Activity Types (with params) Error: " . $e->getMessage() . "\n";
        return [];
    }
}

function testCustomEndpoint(string $endpoint, array $params = []): array
{
    global $loxoService;

    try {
        $data = $loxoService->get($endpoint, $params);
        echo "✅ $endpoint: " . count($data) . " records\n";
        return $data;
    } catch (LoxoApiException $e) {
        echo "❌ $endpoint Error: " . $e->getMessage() . "\n";
        return [];
    }
}

function measureTime(callable $func): array
{
    $start = microtime(true);
    $result = $func();
    $duration = round((microtime(true) - $start) * 1000, 2);

    echo "⏱️  Duration: {$duration}ms\n";
    return $result;
}

function quickTest(): void
{
    echo "🚀 Quick Loxo API Test\n";
    echo "========================\n";

    try {
        echo "\n1. Testing Activity Types...\n";
        measureTime('testActivityTypes');

        echo "\n2. Testing Address Types...\n";
        measureTime('testAddressTypes');

        echo "\n3. Testing with parameters...\n";
        measureTime(function () {
            return testWithParams(['show_hidden' => true]);
        });

        echo "\n🎉 All tests passed!\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function debugConfig(): void
{
    global $loxoService;

    echo "🔧 Configuration Debug\n";
    echo "======================\n";
    echo "Domain: " . $loxoService->getDomain() . "\n";
    echo "Agency: " . $loxoService->getAgencySlug() . "\n";
    echo "API Key: ***" . substr(env('LOXO_API_KEY'), -8) . "\n";
    echo "Base URL: " . $loxoService->getBaseUrl() . "/\n";
}

// If script is run directly, run quick test
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    quickTest();
}
