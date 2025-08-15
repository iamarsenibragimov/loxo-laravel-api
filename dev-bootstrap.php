<?php

// Create mock for Laravel Config facade BEFORE autoloading
namespace Illuminate\Support\Facades {
    class Config
    {
        public static function get($key, $default = null)
        {
            global $config;
            return $config[$key] ?? $default;
        }
    }
}

namespace {
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

    function debugConfig()
    {
        echo "🔧 Loxo API Configuration Debug\n";
        echo "===============================\n";
        echo "Domain: " . \Illuminate\Support\Facades\Config::get('loxo.domain') . "\n";
        echo "Agency Slug: " . \Illuminate\Support\Facades\Config::get('loxo.agency_slug') . "\n";
        echo "API Key: " . (\Illuminate\Support\Facades\Config::get('loxo.api_key') ? '***' . substr(\Illuminate\Support\Facades\Config::get('loxo.api_key'), -4) : 'NOT SET') . "\n";
        echo "Timeout: " . \Illuminate\Support\Facades\Config::get('loxo.timeout') . "s\n";
        echo "Retry Attempts: " . \Illuminate\Support\Facades\Config::get('loxo.retry_attempts') . "\n";
        echo "Retry Delay: " . \Illuminate\Support\Facades\Config::get('loxo.retry_delay') . "ms\n";
        echo "Base URL: " . \Illuminate\Support\Facades\Config::get('loxo.base_url') . "\n";
    }

    function measureTime($functionName)
    {
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

    function quickTest()
    {
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
            measureTime('testPeople');
            measureTime('testCreatePerson');

            echo "\n🎉 Quick test completed!\n";
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }

    function testActivityTypes()
    {
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

    function testAddressTypes()
    {
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

    function testJobs()
    {
        echo "🔄 Testing Jobs endpoint...\n";

        try {
            $api = new LoxoApiService();

            // Test basic request
            echo "📋 Testing basic jobs request...\n";
            $result = $api->getJobs(['per_page' => 5]);

            $count = count($result['results'] ?? []);
            echo "✅ Retrieved {$count} jobs\n";

            if (isset($result['total_count'])) {
                echo "📊 Total: {$result['total_count']}, Page: {$result['current_page']}, Per page: {$result['per_page']}\n";
            }

            if (!empty($result['results'])) {
                $first = $result['results'][0];
                $published = ($first['published'] ?? false) ? 'Published' : 'Draft';
                echo "📄 First job: ID={$first['id']}, Title='{$first['title']}', Status={$published}\n";
            }

            // Test with search
            echo "\n🔍 Testing jobs search...\n";
            $searchResult = $api->getJobs([
                'query' => 'developer',
                'per_page' => 3
            ]);

            $searchCount = count($searchResult['results'] ?? []);
            echo "✅ Found {$searchCount} jobs matching 'developer'\n";

            if (!empty($searchResult['results'])) {
                foreach ($searchResult['results'] as $job) {
                    echo "   📄 {$job['title']} (ID: {$job['id']})\n";
                }
            }

            // Test with advanced filters
            echo "\n🎯 Testing advanced filters...\n";
            $filteredResult = $api->getJobs([
                'per_page' => 2
            ]);

            $filteredCount = count($filteredResult['results'] ?? []);
            echo "✅ Found {$filteredCount} jobs with basic filters\n";

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

    function testPeople()
    {
        echo "🔄 Testing People endpoint...\n";

        try {
            $api = new LoxoApiService();

            // Test basic request
            echo "👥 Testing basic people request...\n";
            $result = $api->getPeople(['per_page' => 5]);

            $count = count($result['people'] ?? []);
            echo "✅ Retrieved {$count} people\n";

            if (isset($result['total_count'])) {
                $scrollInfo = isset($result['scroll_id']) ? " (scroll_id available)" : "";
                echo "📊 Total: {$result['total_count']}, Retrieved: {$count}{$scrollInfo}\n";
            }

            if (!empty($result['people'])) {
                $first = $result['people'][0];
                $name = $first['name'] ?? 'No name';
                $email = !empty($first['emails']) ? $first['emails'][0]['value'] : 'No email';
                echo "👤 First person: ID={$first['id']}, Name='{$name}', Email={$email}\n";
            }

            // Test with search
            echo "\n🔍 Testing people search...\n";
            $searchResult = $api->getPeople([
                'query' => 'john',
                'per_page' => 3
            ]);

            $searchCount = count($searchResult['people'] ?? []);
            echo "✅ Found {$searchCount} people matching 'john'\n";

            if (!empty($searchResult['people'])) {
                foreach ($searchResult['people'] as $person) {
                    $name = $person['name'] ?? 'No name';
                    echo "   👤 {$name} (ID: {$person['id']})\n";
                }
            }

            // Test with filters
            echo "\n🎯 Testing advanced filters...\n";
            $filteredResult = $api->getPeople([
                'per_page' => 2,
                'created_at_sort' => 'desc'
            ]);

            $filteredCount = count($filteredResult['people'] ?? []);
            echo "✅ Found {$filteredCount} people with recent sort\n";

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

    function testCreatePerson()
    {
        echo "🔄 Testing Create Person endpoint...\n";

        try {
            $api = new LoxoApiService();

            // Test creating a basic person
            echo "👤 Testing basic person creation...\n";

            $timestamp = date('Y-m-d_H-i-s');
            $personData = [
                'person' => [
                    'name' => 'Test User ' . $timestamp,
                    'email' => 'test-' . $timestamp . '@example.com',
                    'title' => 'Test Engineer',
                    'company' => 'Test Company Inc',
                    'location' => 'San Francisco, CA',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'United States',
                    'linkedin_url' => 'https://linkedin.com/in/testuser',
                    'description' => 'This is a test user created by the Loxo Laravel API package',
                    'all_raw_tags' => ['api-test', 'laravel', 'loxo']
                ]
            ];

            $result = $api->createPerson($personData);

            if (isset($result['person'])) {
                $person = $result['person'];
                echo "✅ Person created successfully!\n";
                echo "📄 ID: {$person['id']}, Name: '{$person['name']}'\n";

                if (isset($person['email'])) {
                    echo "📧 Email: {$person['email']}\n";
                }

                if (isset($person['created_at'])) {
                    echo "📅 Created: {$person['created_at']}\n";
                }
            } else {
                echo "⚠️  Person created but unexpected response format\n";
                echo "📄 Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
            }

            // Test creating person with more complex data
            echo "\n👥 Testing advanced person creation...\n";

            $advancedPersonData = [
                'person' => [
                    'name' => 'Advanced Test User ' . $timestamp,
                    'email' => 'advanced-test-' . $timestamp . '@example.com',
                    'title' => 'Senior Software Engineer',
                    'company' => 'Tech Startup',
                    'location' => 'New York, NY',
                    'city' => 'New York',
                    'state' => 'NY',
                    'zip' => '10001',
                    'country' => 'United States',
                    'linkedin_url' => 'https://linkedin.com/in/advancedtestuser',
                    'website' => 'https://example.com',
                    'description' => 'Advanced test user with full profile data',
                    'compensation' => 150000.0,
                    'salary' => 150000.0,
                    'all_raw_tags' => ['senior', 'full-stack', 'react', 'node.js', 'api-test']
                ]
            ];

            $advancedResult = $api->createPerson($advancedPersonData);

            if (isset($advancedResult['person'])) {
                $advancedPerson = $advancedResult['person'];
                echo "✅ Advanced person created successfully!\n";
                echo "📄 ID: {$advancedPerson['id']}, Name: '{$advancedPerson['name']}'\n";

                if (isset($advancedPerson['compensation'])) {
                    echo "💰 Compensation: \${$advancedPerson['compensation']}\n";
                }
            } else {
                echo "⚠️  Advanced person created but unexpected response format\n";
            }

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
}
