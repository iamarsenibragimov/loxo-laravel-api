<?php

require_once __DIR__ . '/vendor/autoload.php';

use Loxo\LaravelApi\Services\LoxoApiService;
use Loxo\LaravelApi\Exceptions\LoxoApiException;
use Loxo\LaravelApi\Exceptions\ConfigurationException;

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Set Laravel-like config
$config = [
    'loxo.domain' => $_ENV['LOXO_DOMAIN'] ?? 'app.loxo.co',
    'loxo.agency_slug' => $_ENV['LOXO_AGENCY_SLUG'] ?? null,
    'loxo.api_key' => $_ENV['LOXO_API_KEY'] ?? null,
    'loxo.timeout' => $_ENV['LOXO_TIMEOUT'] ?? 30,
    'loxo.retry_attempts' => $_ENV['LOXO_RETRY_ATTEMPTS'] ?? 3,
    'loxo.retry_delay' => $_ENV['LOXO_RETRY_DELAY'] ?? 1000,
    'loxo.base_url' => $_ENV['LOXO_BASE_URL'] ?? 'https://{domain}/api/{agency_slug}',
];

// Mock Laravel Config facade
if (!class_exists('Config')) {
    class Config {
        public static function get($key, $default = null) {
            global $config;
            return $config[$key] ?? $default;
        }
    }
}

function debugConfig() {
    echo "🔧 Loxo API Configuration Debug\n";
    echo "===============================\n";
    echo "Domain: " . Config::get('loxo.domain') . "\n";
    echo "Agency Slug: " . Config::get('loxo.agency_slug') . "\n";
    echo "API Key: " . (Config::get('loxo.api_key') ? '***' . substr(Config::get('loxo.api_key'), -4) : 'NOT SET') . "\n";
    echo "Timeout: " . Config::get('loxo.timeout') . "s\n";
    echo "Retry Attempts: " . Config::get('loxo.retry_attempts') . "\n";
    echo "Retry Delay: " . Config::get('loxo.retry_delay') . "ms\n";
    echo "Base URL: " . Config::get('loxo.base_url') . "\n";
}

function measureTime($functionName) {
    $start = microtime(true);
    
    try {
        $result = call_user_func($functionName);
        $duration = round((microtime(true) - $start) * 1000, 2);
        echo "⏱️  Completed in {$duration}ms\n";
        return $result;
    } catch (Exception $e) {
        $duration = round((microtime(true) - $start) * 1000, 2);
        echo "❌ Failed in {$duration}ms: " . $e->getMessage() . "\n";
        throw $e;
    }
}

function quickTest() {
    echo "⚡ Quick Test - Loxo API Package\n";
    echo "================================\n";
    
    try {
        $api = new LoxoApiService();
        echo "✅ Service initialized\n";
        echo "🌐 Base URL: " . $api->getBaseUrl() . "\n";
        echo "🏢 Agency: " . $api->getAgencySlug() . "\n";
        
        // Test each endpoint quickly
        echo "\n🧪 Testing endpoints...\n";
        
        measureTime('testActivityTypes');
        measureTime('testAddressTypes');
        measureTime('testJobs');
        
        echo "\n🎉 Quick test completed!\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function testActivityTypes() {
    echo "🔄 Testing Activity Types endpoint...\n";
    
    try {
        $api = new LoxoApiService();
        $result = $api->getActivityTypes();
        
        $count = count($result['activity_types'] ?? []);
        echo "✅ Retrieved {$count} activity types\n";
        
        if (!empty($result['activity_types'])) {
            $first = $result['activity_types'][0];
            echo "📋 First: ID={$first['id']}, Name='{$first['name']}'\n";
        }
        
        return $result;
        
    } catch (LoxoApiException $e) {
        echo "❌ API Error: " . $e->getMessage() . "\n";
        if ($e->getResponse()) {
            echo "📄 Response: " . json_encode($e->getResponse()) . "\n";
        }
        throw $e;
    } catch (ConfigurationException $e) {
        echo "❌ Config Error: " . $e->getMessage() . "\n";
        throw $e;
    }
}

function testAddressTypes() {
    echo "🔄 Testing Address Types endpoint...\n";
    
    try {
        $api = new LoxoApiService();
        $result = $api->getAddressTypes();
        
        $count = count($result['address_types'] ?? []);
        echo "✅ Retrieved {$count} address types\n";
        
        if (!empty($result['address_types'])) {
            $first = $result['address_types'][0];
            echo "📋 First: ID={$first['id']}, Name='{$first['name']}'\n";
        }
        
        return $result;
        
    } catch (LoxoApiException $e) {
        echo "❌ API Error: " . $e->getMessage() . "\n";
        if ($e->getResponse()) {
            echo "📄 Response: " . json_encode($e->getResponse()) . "\n";
        }
        throw $e;
    } catch (ConfigurationException $e) {
        echo "❌ Config Error: " . $e->getMessage() . "\n";
        throw $e;
    }
}

function testJobs() {
    echo "🔄 Testing Jobs endpoint...\n";
    
    try {
        $api = new LoxoApiService();
        
        // Test basic request
        echo "📋 Testing basic jobs request...\n";
        $result = $api->getJobs(['per_page' => 5]);
        
        $count = count($result['jobs'] ?? []);
        echo "✅ Retrieved {$count} jobs\n";
        
        if (isset($result['meta'])) {
            $meta = $result['meta'];
            echo "📊 Total: {$meta['total']}, Page: {$meta['current_page']}, Per page: {$meta['per_page']}\n";
        }
        
        if (!empty($result['jobs'])) {
            $first = $result['jobs'][0];
            $published = ($first['published'] ?? false) ? 'Published' : 'Draft';
            echo "📄 First job: ID={$first['id']}, Title='{$first['title']}', Status={$published}\n";
        }
        
        // Test with search
        echo "\n🔍 Testing jobs search...\n";
        $searchResult = $api->getJobs([
            'query' => 'developer',
            'per_page' => 3,
            'published' => true
        ]);
        
        $searchCount = count($searchResult['jobs'] ?? []);
        echo "✅ Found {$searchCount} jobs matching 'developer'\n";
        
        if (!empty($searchResult['jobs'])) {
            foreach ($searchResult['jobs'] as $job) {
                echo "   📄 {$job['title']} (ID: {$job['id']})\n";
            }
        }
        
        // Test with advanced filters
        echo "\n🎯 Testing advanced filters...\n";
        $filteredResult = $api->getJobs([
            'published' => true,
            'remote_work_allowed' => true,
            'per_page' => 2
        ]);
        
        $filteredCount = count($filteredResult['jobs'] ?? []);
        echo "✅ Found {$filteredCount} published remote jobs\n";
        
        return $result;
        
    } catch (LoxoApiException $e) {
        echo "❌ API Error: " . $e->getMessage() . "\n";
        if ($e->getResponse()) {
            echo "📄 Response: " . json_encode($e->getResponse(), JSON_PRETTY_PRINT) . "\n";
        }
        throw $e;
    } catch (ConfigurationException $e) {
        echo "❌ Config Error: " . $e->getMessage() . "\n";
        throw $e;
    }
}

// If called directly, run quick test
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    quickTest();
}
