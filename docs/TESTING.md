# 🚀 Testing Loxo API Package During Development (Unofficial)

> ⚠️ **Disclaimer:** This is an unofficial package not affiliated with Loxo.

Instead of creating a separate Laravel project, you can test your package directly during development using two complementary approaches:

## ⚡ Development Testing with Real API

### Quick Setup
1. Create `.env` file in the project root with your API credentials:
```bash
# .env
LOXO_DOMAIN=your-domain.app.loxo.co
LOXO_AGENCY_SLUG=your-agency-slug
LOXO_API_KEY=your-api-key-here
LOXO_TIMEOUT=30
```

### Usage
```bash
make quick

# Test specific endpoints via make commands
make activity-types
make address-types
make debug-config

**Use for:** Daily development, quick checks, debugging real API responses
```

## 🧪 Automated Testing with Mock Data

```bash
# Run unit tests with mock data
vendor/bin/phpunit tests/SimpleTest.php

# Or via make
make test
```

**Use for:** Continuous integration, automated verification, testing without API credentials

---

## 🚀 Complete Development Workflow

```bash
# 1. Make code changes
vim src/Services/LoxoApiService.php

# 2. Test with real API (immediate feedback)
make quick

# 3. Test specific endpoints if needed
make activity-types
make address-types

# 4. Run automated tests (before commit)
make test

# 5. Debug configuration if issues
make debug-config
```

---

## 📊 Testing Types Comparison

| Testing Type        | Speed | Setup | Real API | Purpose                      |
| ------------------- | ----- | ----- | -------- | ---------------------------- |
| Development testing | ⚡⚡⚡   | ⭐⭐⭐   | ✅        | Daily development, debugging |
| Automated testing   | ⚡⚡    | ⭐⭐⭐   | ❌        | CI/CD, verification          |

---
