# Loxo Laravel API - Quick Start (Unofficial)

> ⚠️ **Disclaimer:** This is an unofficial package not affiliated with Loxo.

## 🚀 Installation

```bash
composer require iamarsenibragimov/loxo-laravel-api
```

## ⚙️ Configuration

Add to your `.env` file:

```env
LOXO_DOMAIN=your-domain.app.loxo.co
LOXO_AGENCY_SLUG=your-agency-slug
LOXO_API_KEY=your-api-key-here
```

## 📖 Usage

### Basic Methods

```php
use Loxo\LaravelApi\Facades\Loxo;

// Get activity types
$activityTypes = Loxo::getActivityTypes();

// Get address types
$addressTypes = Loxo::getAddressTypes();

// Custom GET request
$data = Loxo::get('endpoint', ['param' => 'value']);

// Custom POST request
$result = Loxo::post('endpoint', ['data' => 'value']);
```

### With Parameters

```php
// Get activity types with filters
$activityTypes = Loxo::getActivityTypes([
    'workflow_id' => 123,
    'show_hidden' => true
]);
```

### Error Handling

```php
use Loxo\LaravelApi\Exceptions\LoxoApiException;

try {
    $data = Loxo::getActivityTypes();
} catch (LoxoApiException $e) {
    // Handle API error
    $error = $e->getMessage();
    $statusCode = $e->getCode();
    $response = $e->getResponse();
}
```

## 🔧 Additional Settings

Publish config for customization (optional):

```bash
php artisan vendor:publish --tag=loxo-config
```

Available settings in `config/loxo.php`:
- `timeout` - request timeout (default 30 seconds)
- `retry_attempts` - number of retry attempts (default 3)
- `retry_delay` - delay between retries (default 1000 ms)

## 📚 Full Documentation

See [README.md](README.md) for detailed documentation.
