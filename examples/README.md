# 💡 Loxo Laravel API Package Examples

This directory contains practical examples of how to use the **iamarsenibragimov/loxo-laravel-api** package in real Laravel applications.

## 📁 Available Examples

### [example.php](example.php)
Complete examples showing:
- **Controller usage** with facade and dependency injection
- **Error handling** best practices
- **Artisan commands** for data synchronization
- **Logging and monitoring** implementation
- **Parameter filtering** and validation

## 🚀 How to Use Examples

1. **Copy the example code** you need
2. **Adapt to your use case** (update parameters, add validation, etc.)
3. **Test in your application**

## 💡 Example Categories

### Basic Usage
```php
use Loxo\LaravelApi\Facades\Loxo;

// Simple API calls
$activityTypes = Loxo::getActivityTypes();
$addressTypes = Loxo::getAddressTypes();
```

### With Parameters
```php
// Filtered requests
$activityTypes = Loxo::getActivityTypes([
    'workflow_id' => 123,
    'show_hidden' => true
]);
```

### Error Handling
```php
try {
    $data = Loxo::getActivityTypes();
} catch (LoxoApiException $e) {
    Log::error('Loxo API Error', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'response' => $e->getResponse()
    ]);
}
```

### Dependency Injection
```php
class YourController extends Controller
{
    public function __construct(
        private LoxoApiInterface $loxoApi
    ) {}

    public function getData()
    {
        return $this->loxoApi->getActivityTypes();
    }
}
```

## 📚 Related Documentation

- **[Quick Start Guide](../docs/QUICK_START.md)** - Basic setup and usage
- **[Main README](../README.md)** - Complete API reference
- **[Development Guide](../docs/DEVELOPMENT.md)** - Testing and development setup

## 🤝 Contributing Examples

Have a useful example? Feel free to contribute by:
1. Adding your example to this directory
2. Documenting it clearly
3. Creating a pull request

Happy coding! 🚀

