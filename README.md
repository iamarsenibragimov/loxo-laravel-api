# Loxo Laravel API Package (Unofficial)

An **unofficial** Laravel package for easy integration with the [Loxo API](https://loxo.co). This package provides a simple and elegant way to interact with Loxo's recruitment platform API.

> ⚠️ **Disclaimer:** This is an unofficial package and is not affiliated with, endorsed by, or supported by Loxo. It is developed and maintained independently.
> 
> 📚 **Official Resources:**
> - [Loxo Website](https://loxo.co) - Official Loxo platform
> - [Loxo API Documentation](https://loxo.readme.io/reference/loxo-api) - Official API reference

## Installation

Install the package via Composer:

```bash
composer require iamarsenibragimov/loxo-laravel-api
```

## Configuration

### 1. Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=loxo-config
```

### 2. Add your Loxo API credentials to your `.env` file:

```env
LOXO_DOMAIN=your-domain.app.loxo.co
LOXO_AGENCY_SLUG=your-agency-slug
LOXO_API_KEY=your-api-key-here
```

### Optional Configuration

You can also configure these optional settings in your `.env` file:

```env
LOXO_TIMEOUT=30
LOXO_RETRY_ATTEMPTS=3
LOXO_RETRY_DELAY=1000
```

## Usage

### Using the Facade

The easiest way to use the package is through the `Loxo` facade:

```php
use Loxo\LaravelApi\Facades\Loxo;

// Get activity types
$activityTypes = Loxo::getActivityTypes();

// Get activity types with parameters
$activityTypes = Loxo::getActivityTypes([
    'workflow_id' => 123,
    'show_hidden' => true
]);

// Get address types
$addressTypes = Loxo::getAddressTypes();

// Make custom GET request
$data = Loxo::get('custom-endpoint', ['param' => 'value']);

// Make custom POST request
$result = Loxo::post('custom-endpoint', ['data' => 'value']);
```

### Using Dependency Injection

You can also inject the service directly into your classes:

```php
use Loxo\LaravelApi\Contracts\LoxoApiInterface;

class YourController extends Controller
{
    public function __construct(
        private LoxoApiInterface $loxoApi
    ) {}

    public function getActivityTypes()
    {
        try {
            $activityTypes = $this->loxoApi->getActivityTypes();
            return response()->json($activityTypes);
        } catch (\Loxo\LaravelApi\Exceptions\LoxoApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'response' => $e->getResponse()
            ], $e->getCode());
        }
    }
}
```

### Available Methods

> **📊 API Coverage Status:** Currently 20 out of 144+ Loxo API endpoints are implemented (13.9%). See [API_COVERAGE.md](docs/API_COVERAGE.md) for detailed progress tracking.
> 
> 📖 **Reference:** All endpoints are based on the [official Loxo API documentation](https://loxo.readme.io/reference/loxo-api).

#### Activity Types
```php
// Get all activity types
$activityTypes = Loxo::getActivityTypes();

// Get activity types with filters
$activityTypes = Loxo::getActivityTypes([
    'workflow_id' => 123,
    'show_hidden' => true
]);
```

#### Address Types
```php
// Get all address types
$addressTypes = Loxo::getAddressTypes();
```

#### Bonus Payment Types
```php
// Get all bonus payment types
$bonusPaymentTypes = Loxo::getBonusPaymentTypes();
```

#### Bonus Types
```php
// Get all bonus types
$bonusTypes = Loxo::getBonusTypes();
```

#### Companies
```php
// Get all companies
$companies = Loxo::getCompanies();

// Get companies with search and filters
$companies = Loxo::getCompanies([
    'query' => 'tech startup',
    'company_type_id' => 1,
    'company_global_status_id' => 1,
    'list_id' => 5
]);

// Get companies with pagination
$companies = Loxo::getCompanies([
    'scroll_id' => 'cursor_123'
]);

// Create a new company
$newCompany = Loxo::createCompany([
    'company' => [
        'name' => 'New Tech Company',
        'url' => 'https://newtechcompany.com',
        'description' => 'A newly created tech company',
        'culture' => 'Innovation-focused culture',
        'company_type_id' => 1,
        'owner_email' => 'owner@newtechcompany.com',
        'emails' => ['contact@newtechcompany.com'],
        'phones' => ['+1234567890']
    ]
]);
```

#### Workflows
```php
// Get all workflows
$workflows = Loxo::getWorkflows();
```

#### Workflow Stages
```php
// Get all workflow stages
$workflowStages = Loxo::getWorkflowStages();
```

#### Veteran Statuses
```php
// Get all veteran statuses
$veteranStatuses = Loxo::getVeteranStatuses();
```

#### Webhooks
```php
// Get all webhooks
$webhooks = Loxo::getWebhooks();

// Get a specific webhook
$webhook = Loxo::getWebhook(1);

// Create a new webhook
$newWebhook = Loxo::createWebhook([
    'webhook' => [
        'item_type' => 'candidate',
        'action' => 'create',
        'endpoint_url' => 'https://myapp.com/webhooks/candidate-created'
    ]
]);

// Update a webhook
$updatedWebhook = Loxo::updateWebhook(1, [
    'webhook' => [
        'item_type' => 'candidate',
        'action' => 'update',
        'endpoint_url' => 'https://myapp.com/webhooks/candidate-updated'
    ]
]);

// Delete a webhook
$result = Loxo::deleteWebhook(1);

// Available item types:
// - candidate, company, deal, job, person_education_profile, 
//   person_event, person_job_profile, person, placement_split, placement
//
// Available actions:
// - create, update, destroy
```

#### Users
```php
// Get all users
$users = Loxo::getUsers();
```

#### People (Candidates)
```php
// Get all people
$people = Loxo::getPeople();

// Get people with pagination using scroll_id
$people = Loxo::getPeople([
    'scroll_id' => 'cursor_123',
    'per_page' => 20
]);

// Search for people with advanced filters
$people = Loxo::getPeople([
    'query' => 'software engineer',
    'person_global_status_id' => 1,
    'person_type_id' => 2,
    'active_job_stage_id' => 5,
    'include_related_agencies' => true,
    'list_id' => 3,
    'created_at_sort' => 'desc'
]);

// Create a new person/candidate
$newPerson = Loxo::createPerson([
    'person' => [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'phone' => '+1234567890',
        'title' => 'Software Engineer',
        'company' => 'Tech Corp',
        'location' => 'San Francisco, CA',
        'linkedin_url' => 'https://linkedin.com/in/johndoe',
        'person_type_id' => 1,
        'compensation' => 120000.0,
        'salary' => 120000.0,
        'all_raw_tags' => ['python', 'javascript', 'react']
    ]
]);
```

#### Person Events
```php
// Get all person events
$personEvents = Loxo::getPersonEvents();

// Get person events with filters
$personEvents = Loxo::getPersonEvents([
    'person_id' => 123,
    'activity_type_ids' => [1, 2],
    'created_by_ids' => [1, 3],
    'job_ids' => [456, 789],
    'company_id' => 101,
    'query' => 'interview',
    'created_at_start' => '2024-12-01T00:00:00.000Z',
    'created_at_end' => '2024-12-31T23:59:59.000Z',
    'created_at_sort' => 'desc',
    'per_page' => 20
]);

// Create a new person event
$newEvent = Loxo::createPersonEvent([
    'person_event' => [
        'activity_type_id' => 1,
        'person_id' => 456,
        'job_id' => 789,
        'company_id' => 101,
        'notes' => 'Phone screening completed successfully',
        'pinned' => false,
        'created_by_id' => 1
    ]
]);
```

#### Jobs
```php
// Get all jobs
$jobs = Loxo::getJobs();

// Get jobs with pagination
$jobs = Loxo::getJobs([
    'per_page' => 20,
    'page' => 1
]);

// Search for jobs with query
$jobs = Loxo::getJobs([
    'query' => 'software engineer',
    'published' => true
]);

// Filter jobs with advanced parameters
$jobs = Loxo::getJobs([
    'per_page' => 50,
    'page' => 1,
    'query' => 'developer',
    'published' => true,
    'remote_work_allowed' => true,
    'job_category_ids' => [1, 2, 3],
    'owned_by_ids' => [5, 10],
    'country_id' => 1,
    'state_id' => 5,
    'city' => 'New York',
    'job_status_id' => 1,
    'job_type_id' => 2,
    'published_at_sort' => 'desc',
    'rank_sort' => 'asc'
]);

// Get candidates for a specific job
$jobCandidates = Loxo::getJobCandidates(456);

// Get job candidates with filters
$jobCandidates = Loxo::getJobCandidates(456, [
    'per_page' => 20,
    'scroll_id' => 'cursor_123',
    'query' => 'senior developer',
    'activity_type_id' => 1,
    'person_id' => 123
]);

// Get a specific candidate for a job
$jobCandidate = Loxo::getJobCandidate(456, 1);
```

#### Generic HTTP Methods
```php
// GET request
$data = Loxo::get('endpoint', ['query' => 'params']);

// POST request
$result = Loxo::post('endpoint', ['data' => 'to-send']);

// PUT request
$result = Loxo::put('endpoint', ['data' => 'to-update']);

// DELETE request
$result = Loxo::delete('endpoint');
```

#### Utility Methods
```php
// Get base API URL
$baseUrl = Loxo::getBaseUrl();

// Get agency slug
$agencySlug = Loxo::getAgencySlug();

// Get domain
$domain = Loxo::getDomain();
```

## Error Handling

The package provides custom exceptions for better error handling:

### LoxoApiException

Thrown when API requests fail:

```php
use Loxo\LaravelApi\Exceptions\LoxoApiException;

try {
    $data = Loxo::getActivityTypes();
} catch (LoxoApiException $e) {
    // Get the error message
    $message = $e->getMessage();
    
    // Get the HTTP status code
    $statusCode = $e->getCode();
    
    // Get the API response (if available)
    $response = $e->getResponse();
    
    Log::error('Loxo API Error', [
        'message' => $message,
        'code' => $statusCode,
        'response' => $response
    ]);
}
```

### ConfigurationException

Thrown when required configuration is missing:

```php
use Loxo\LaravelApi\Exceptions\ConfigurationException;

try {
    $data = Loxo::getActivityTypes();
} catch (ConfigurationException $e) {
    // Handle missing configuration
    Log::error('Loxo API Configuration Error: ' . $e->getMessage());
}
```

## Advanced Usage

### Retry Logic

The package includes built-in retry logic for failed requests. You can configure the retry behavior in your config file:

```php
// config/loxo.php
return [
    'retry_attempts' => 3,      // Number of retry attempts
    'retry_delay' => 1000,      // Delay between retries in milliseconds
];
```

### Timeout Configuration

You can configure the request timeout:

```php
// config/loxo.php
return [
    'timeout' => 30, // Timeout in seconds
];
```

## Quick Development Testing

For rapid testing during development, create a `.env` file in the package root:

```bash
# .env
LOXO_DOMAIN=your-domain.app.loxo.co
LOXO_AGENCY_SLUG=your-agency-slug
LOXO_API_KEY=your-api-key-here
```

Then use these make commands:

```bash
# Quick test all endpoints
make quick

# Test specific endpoints
make activity-types
make address-types

# Debug configuration
make debug-config

# Run unit tests
make test
```

Or run the test script directly:

```bash
php dev-bootstrap.php
```

### Available Make Commands

```bash
make help           # Show all available commands
make quick          # Quick test all endpoints  
make activity-types # Test activity types endpoint
make address-types  # Test address types endpoint
make debug-config   # Show current configuration
make test           # Run automated tests
make install        # Install dependencies
make clean          # Clean vendor directory
make lint           # Check code style
```

## Testing in Laravel Applications

The package is designed to be easily testable. You can mock the `LoxoApiInterface` in your tests:

```php
use Loxo\LaravelApi\Contracts\LoxoApiInterface;
use Mockery;

public function test_something()
{
    $mockLoxoApi = Mockery::mock(LoxoApiInterface::class);
    $mockLoxoApi->shouldReceive('getActivityTypes')
        ->once()
        ->andReturn(['activity_types' => []]);

    $this->app->instance(LoxoApiInterface::class, $mockLoxoApi);

    // Your test code here
}
```

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Guzzle HTTP client 7.0 or higher

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Please see [CONTRIBUTING.md](docs/CONTRIBUTING.md) for details on how to contribute to this project.

## Support

If you discover any security vulnerabilities or bugs, please create an issue on GitHub.

## 📚 Documentation

- **[🚀 Quick Start Guide](docs/QUICK_START.md)** - Get started in 5 minutes
- **[🧪 Testing Guide](docs/TESTING.md)** - Development and automated testing
- **[🤝 Contributing Guide](docs/CONTRIBUTING.md)** - How to contribute to development
- **[💡 Examples](examples/)** - Usage examples and code samples
- **[📊 API Coverage](docs/API_COVERAGE.md)** - Track implemented vs available endpoints

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.
