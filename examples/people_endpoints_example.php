<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Loxo\LaravelApi\Services\LoxoApiService;
use Loxo\LaravelApi\Exceptions\LoxoApiException;
use Loxo\LaravelApi\Exceptions\ConfigurationException;

/**
 * Example usage of the new Person endpoints
 * 
 * This example demonstrates:
 * - Getting a specific person by ID
 * - Updating a person's information
 */

// Load environment configuration
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Set configuration using environment variables
putenv('LOXO_DOMAIN=' . ($_ENV['LOXO_DOMAIN'] ?? 'your-domain.app.loxo.co'));
putenv('LOXO_AGENCY_SLUG=' . ($_ENV['LOXO_AGENCY_SLUG'] ?? 'your-agency-slug'));
putenv('LOXO_API_KEY=' . ($_ENV['LOXO_API_KEY'] ?? 'your-api-key'));
putenv('LOXO_TIMEOUT=' . ($_ENV['LOXO_TIMEOUT'] ?? '30'));

try {
    echo "🚀 Loxo Person Endpoints Example\n";
    echo "=================================\n\n";

    // Initialize the service
    $loxo = new LoxoApiService();

    echo "📋 Configuration:\n";
    echo "- Domain: " . explode('.', $loxo->getDomain())[0] . ".***\n";
    echo "- Agency: " . $loxo->getAgencySlug() . "\n";
    echo "- Base URL: " . $loxo->getBaseUrl() . "\n\n";

    // First, let's get a list of people to find a valid ID
    echo "👥 Getting list of people...\n";
    $people = $loxo->getPeople(['per_page' => 1]);

    if (empty($people['people'])) {
        echo "❌ No people found in your database. Please create a person first.\n";
        exit(1);
    }

    $personId = $people['people'][0]['id'];
    echo "✅ Found person with ID: {$personId}\n\n";

    // Example 1: Get a specific person by ID
    echo "🔍 Getting person by ID ({$personId})...\n";
    echo "----------------------------\n";

    $person = $loxo->getPerson($personId);

    echo "✅ Person retrieved successfully!\n";
    echo "📋 Person details:\n";
    echo "- ID: " . $person['person']['id'] . "\n";
    echo "- Name: " . ($person['person']['name'] ?? 'N/A') . "\n";
    echo "- Email: " . ($person['person']['email'] ?? 'N/A') . "\n";
    echo "- Title: " . ($person['person']['title'] ?? 'N/A') . "\n";
    echo "- Company: " . ($person['person']['company'] ?? 'N/A') . "\n";
    echo "- Location: " . ($person['person']['location'] ?? 'N/A') . "\n\n";

    // Example 2: Update the person's information
    echo "✏️ Updating person information...\n";
    echo "--------------------------------\n";

    $updateData = [
        'person' => [
            'description' => 'Updated via Loxo Laravel API package - ' . date('Y-m-d H:i:s'),
            'title' => ($person['person']['title'] ?? 'Software Engineer') . ' (Updated)',
        ]
    ];

    $updatedPerson = $loxo->updatePerson($personId, $updateData);

    echo "✅ Person updated successfully!\n";
    echo "📋 Updated details:\n";
    echo "- ID: " . $updatedPerson['person']['id'] . "\n";
    echo "- Name: " . ($updatedPerson['person']['name'] ?? 'N/A') . "\n";
    echo "- Title: " . ($updatedPerson['person']['title'] ?? 'N/A') . "\n";
    echo "- Description: " . ($updatedPerson['person']['description'] ?? 'N/A') . "\n";
    echo "- Updated at: " . ($updatedPerson['person']['updated_at'] ?? 'N/A') . "\n\n";

    // Example 3: Update with more comprehensive data
    echo "🔧 Updating with comprehensive data...\n";
    echo "------------------------------------\n";

    $comprehensiveUpdateData = [
        'person' => [
            'location' => 'San Francisco, CA (Updated)',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'United States',
            'compensation_notes' => 'Salary expectations updated via API',
            'all_raw_tags' => ['api-updated', 'laravel', 'loxo'],
        ]
    ];

    $finalPerson = $loxo->updatePerson($personId, $comprehensiveUpdateData);

    echo "✅ Comprehensive update successful!\n";
    echo "📋 Final details:\n";
    echo "- Location: " . ($finalPerson['person']['location'] ?? 'N/A') . "\n";
    echo "- City: " . ($finalPerson['person']['city'] ?? 'N/A') . "\n";
    echo "- State: " . ($finalPerson['person']['state'] ?? 'N/A') . "\n";
    echo "- Country: " . ($finalPerson['person']['country'] ?? 'N/A') . "\n";
    echo "- Compensation Notes: " . ($finalPerson['person']['compensation_notes'] ?? 'N/A') . "\n";
    echo "- Tags: " . (isset($finalPerson['person']['all_raw_tags']) ? implode(', ', $finalPerson['person']['all_raw_tags']) : 'N/A') . "\n\n";

    echo "🎉 Person endpoints example completed successfully!\n";
    echo "\n📚 Available Person methods:\n";
    echo "- getPeople(\$params) - Get list of people with filters\n";
    echo "- createPerson(\$personData) - Create a new person\n";
    echo "- getPerson(\$id) - Get a specific person by ID\n";
    echo "- updatePerson(\$id, \$personData) - Update a person's information\n";
} catch (ConfigurationException $e) {
    echo "❌ Configuration Error: " . $e->getMessage() . "\n";
    echo "\n🔧 Please check your configuration:\n";
    echo "- Set LOXO_DOMAIN in your .env file\n";
    echo "- Set LOXO_AGENCY_SLUG in your .env file\n";
    echo "- Set LOXO_API_KEY in your .env file\n";
    exit(1);
} catch (LoxoApiException $e) {
    echo "❌ API Error: " . $e->getMessage() . "\n";
    echo "- Status Code: " . $e->getStatusCode() . "\n";

    if (!empty($e->getResponseData())) {
        echo "- Response Data: " . json_encode($e->getResponseData(), JSON_PRETTY_PRINT) . "\n";
    }

    exit(1);
} catch (Exception $e) {
    echo "❌ Unexpected Error: " . $e->getMessage() . "\n";
    exit(1);
}
