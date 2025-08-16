<?php

/**
 * Education Profiles Endpoints Example
 * 
 * This example demonstrates how to use the new education profiles endpoints
 * for managing person education data in the Loxo API.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Loxo\LaravelApi\Services\LoxoApiService;
use Loxo\LaravelApi\Exceptions\LoxoApiException;

// Load environment variables from the project root
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
    }
}

// Set default configuration from environment variables
putenv('LOXO_DOMAIN=' . ($_ENV['LOXO_DOMAIN'] ?? 'your-domain'));
putenv('LOXO_AGENCY_SLUG=' . ($_ENV['LOXO_AGENCY_SLUG'] ?? 'your-agency'));
putenv('LOXO_API_KEY=' . ($_ENV['LOXO_API_KEY'] ?? 'your-api-key'));
putenv('LOXO_TIMEOUT=' . ($_ENV['LOXO_TIMEOUT'] ?? '30'));

try {
    echo "🎓 Loxo Education Profiles Example\n";
    echo "==================================\n\n";

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
    $personName = $people['people'][0]['name'] ?? 'Unknown';
    echo "✅ Found person: {$personName} (ID: {$personId})\n\n";

    // Example 1: Get existing education profiles for the person
    echo "📚 Getting education profiles for person {$personId}...\n";
    echo "------------------------------------------------\n";

    $educationProfiles = $loxo->getPersonEducationProfiles($personId);

    if (empty($educationProfiles['education_profiles'])) {
        echo "ℹ️ No education profiles found for this person.\n\n";
    } else {
        echo "✅ Found " . count($educationProfiles['education_profiles']) . " education profile(s):\n";
        foreach ($educationProfiles['education_profiles'] as $profile) {
            echo "- ID: " . $profile['id'] . "\n";
            echo "  Degree: " . ($profile['degree'] ?? 'N/A') . "\n";
            echo "  School: " . ($profile['school'] ?? 'N/A') . "\n";
            echo "  Year: " . ($profile['year'] ?? 'N/A') . "\n";
            echo "  Description: " . ($profile['description'] ?? 'N/A') . "\n";
            echo "\n";
        }
    }

    // Example 2: Create a new education profile
    echo "➕ Creating a new education profile...\n";
    echo "-------------------------------------\n";

    $newEducationData = [
        'degree' => 'Bachelor of Science in Computer Science',
        'school' => 'MIT',
        'month' => 6,
        'year' => 2022,
        'education_type_id' => 1,
        'description' => 'Computer Science degree with specialization in Machine Learning and AI'
    ];

    echo "📋 Education data to create:\n";
    echo "- Degree: " . $newEducationData['degree'] . "\n";
    echo "- School: " . $newEducationData['school'] . "\n";
    echo "- Graduation: " . $newEducationData['month'] . "/" . $newEducationData['year'] . "\n";
    echo "- Description: " . $newEducationData['description'] . "\n\n";

    $createdProfile = $loxo->createPersonEducationProfile($personId, $newEducationData);

    echo "✅ Education profile created successfully!\n";
    echo "📋 Created profile details:\n";
    if (isset($createdProfile['education_profile'])) {
        $profile = $createdProfile['education_profile'];
        echo "- ID: " . $profile['id'] . "\n";
        echo "- Degree: " . ($profile['degree'] ?? 'N/A') . "\n";
        echo "- School: " . ($profile['school'] ?? 'N/A') . "\n";
        echo "- Year: " . ($profile['year'] ?? 'N/A') . "\n";
        echo "- Education Type ID: " . ($profile['education_type_id'] ?? 'N/A') . "\n";
    }
    echo "\n";

    // Example 3: Get updated list of education profiles
    echo "🔄 Getting updated education profiles list...\n";
    echo "---------------------------------------------\n";

    $updatedProfiles = $loxo->getPersonEducationProfiles($personId);

    echo "✅ Total education profiles: " . count($updatedProfiles['education_profiles']) . "\n";
    foreach ($updatedProfiles['education_profiles'] as $index => $profile) {
        echo "Profile " . ($index + 1) . ":\n";
        echo "- ID: " . $profile['id'] . "\n";
        echo "- Degree: " . ($profile['degree'] ?? 'N/A') . "\n";
        echo "- School: " . ($profile['school'] ?? 'N/A') . "\n";
        echo "- Year: " . ($profile['year'] ?? 'N/A') . "\n";
        echo "\n";
    }

    echo "🎉 Education profiles example completed successfully!\n";
} catch (LoxoApiException $e) {
    echo "❌ Loxo API Error: " . $e->getMessage() . "\n";
    echo "HTTP Status Code: " . $e->getCode() . "\n";

    $responseData = $e->getResponseData();
    if (!empty($responseData)) {
        echo "Response Details:\n";
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }

    exit(1);
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
    exit(1);
}
