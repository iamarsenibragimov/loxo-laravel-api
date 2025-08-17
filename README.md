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

> **📊 API Coverage Status:** Currently 29 out of 144+ Loxo API endpoints are implemented (20.1%). See [API_COVERAGE.md](docs/API_COVERAGE.md) for complete endpoint coverage status and details.
> 
> 📖 **Reference:** All endpoints are based on the [official Loxo API documentation](https://loxo.readme.io/reference/loxo-api).

#### Quick Examples

```php
// Get all people (candidates)
$people = Loxo::getPeople();

// Search for people with filters
$people = Loxo::getPeople([
    'query' => 'software engineer',
    'person_global_status_id' => 1,
    'per_page' => 20
]);

// Create a new person/candidate
$newPerson = Loxo::createPerson([
    'person' => [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'title' => 'Software Engineer'
    ]
]);

// Get all companies
$companies = Loxo::getCompanies();

// Get jobs and their candidates
$jobs = Loxo::getJobs();
$jobCandidates = Loxo::getJobCandidates(456);

// Create person events
$newEvent = Loxo::createPersonEvent([
    'person_event' => [
        'activity_type_id' => 1,
        'person_id' => 456,
        'notes' => 'Interview completed'
    ]
]);

// Manage webhooks
$webhooks = Loxo::getWebhooks();
$newWebhook = Loxo::createWebhook([
    'webhook' => [
        'item_type' => 'candidate',
        'action' => 'create',
        'endpoint_url' => 'https://myapp.com/webhooks/candidate-created'
    ]
]);
```

```php
// SMS Messages
$smsMessages = Loxo::getSms([
    'per_page' => 10,
    'created_at_start' => '2024-01-01T00:00:00Z'
]);

// Send SMS
$newSms = Loxo::createSms([
    'from_number' => '+1234567890',
    'to_number' => '+0987654321',
    'body' => 'Hello! This is a test SMS.',
    'job_id' => 123,
    'person_id' => 456
]);

// Get specific SMS
$sms = Loxo::getSmsById(123);
```

For a complete list of all available methods, filters, and parameters, please see [API_COVERAGE.md](docs/API_COVERAGE.md).

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
- **[📊 API Coverage](docs/API_COVERAGE.md)** - Track implemented vs available endpoints

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.
