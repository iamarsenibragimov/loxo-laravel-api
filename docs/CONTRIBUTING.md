# 🤝 Contributing to Loxo Laravel API Package (Unofficial)

> ⚠️ **Disclaimer:** This is an unofficial package not affiliated with Loxo.
> 
> 📚 **Official Resources:**
> - [Loxo Website](https://loxo.co) - Official Loxo platform
> - [Loxo API Documentation](https://loxo.readme.io/reference/loxo-api) - Official API reference

Welcome! We're excited that you want to contribute to the Loxo Laravel API package. This guide will help you get started with contributing code, documentation, or bug reports.

## 📋 Table of Contents

- [🚀 Getting Started](#-getting-started)
- [🏗️ Development Setup](#️-development-setup)
- [📊 Current Status](#-current-status)
- [🛠️ Adding New API Endpoints](#️-adding-new-api-endpoints)
- [🧪 Testing Guidelines](#-testing-guidelines)
- [📚 Documentation Standards](#-documentation-standards)
- [🔄 Development Workflow](#-development-workflow)
- [📝 Pull Request Process](#-pull-request-process)
- [🐛 Bug Reports](#-bug-reports)
- [💡 Feature Requests](#-feature-requests)

---

## 🚀 Getting Started

### Prerequisites

- **PHP 8.1+** with required extensions
- **Composer** for dependency management
- **Git** for version control
- **Loxo API credentials** for testing (domain, agency slug, API key)

### Fork and Clone

```bash
# Fork the repository on GitHub, then clone your fork
git clone https://github.com/YOUR-USERNAME/loxo-laravel-api.git
cd loxo-laravel-api

# Add upstream remote
git remote add upstream https://github.com/iamarsenibragimov/loxo-laravel-api.git
```

---

## 🏗️ Development Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Environment

Create `.env` file in the project root:

```bash
# .env
LOXO_DOMAIN=your-domain.app.loxo.co
LOXO_AGENCY_SLUG=your-agency-slug
LOXO_API_KEY=your-api-key-here
LOXO_TIMEOUT=30
```

### 3. Verify Setup

```bash
# Test basic functionality
make quick

# Run automated tests
make test

# Show available commands
make help
```

---

## 📊 Current Status

**API Coverage Progress:**
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 21 (14.6%)
- **Remaining:** 123 (85.4%)

**Implemented Endpoints:**
- ✅ `getActivityTypes()` - Activity types
- ✅ `getAddressTypes()` - Address types  
- ✅ `getBonusPaymentTypes()` - Bonus payment types
- ✅ `getBonusTypes()` - Bonus types
- ✅ `getCompanies()` - Companies listing
- ✅ `createCompany()` - Create company
- ✅ `getWorkflows()` - Workflows listing
- ✅ `getWorkflowStages()` - Workflow stages listing
- ✅ `getVeteranStatuses()` - Veteran statuses listing
- ✅ `getWebhooks()` - Webhooks listing
- ✅ `getWebhook()` - Get webhook by ID
- ✅ `createWebhook()` - Create webhook
- ✅ `updateWebhook()` - Update webhook
- ✅ `deleteWebhook()` - Delete webhook
- ✅ `getUsers()` - Users listing
- ✅ `getPersonEvents()` - Person events listing
- ✅ `createPersonEvent()` - Create person event
- ✅ `getJobs()` - Jobs listing
- ✅ `getJobCandidates()` - Job candidates listing
- ✅ `getJobCandidate()` - Get job candidate by ID
- ✅ `getPeople()` - People/candidates listing
- ✅ `createPerson()` - Create person/candidate
- ✅ `applyToJob()` - Apply to job

**Priority Areas for Contribution:**
1. **Companies API** (34+ endpoints) - High priority
2. **People/Candidates API** (30+ endpoints) - Extend existing
3. **Jobs API** (20+ endpoints) - Extend existing
4. **Dynamic Fields** (6 endpoints) - Medium priority
5. **Geography** (Countries, States, Cities) - Medium priority

See [API_COVERAGE.md](../docs/API_COVERAGE.md) for detailed status.

---

## 🛠️ Adding New API Endpoints

### Step-by-Step Process

#### 1. Choose an Endpoint

Refer to [API_COVERAGE.md](../docs/API_COVERAGE.md) to select an unimplemented endpoint. Always check the [official Loxo API documentation](https://loxo.readme.io/reference/loxo-api) for the most up-to-date endpoint details.

Start with high-priority areas:

```markdown
Companies API (Priority 1):
- getCompanies() - GET /{agency_slug}/companies
- createCompany() - POST /{agency_slug}/companies
- getCompany() - GET /{agency_slug}/companies/{id}
```

#### 2. Add Method to Interface

```php
// src/Contracts/LoxoApiInterface.php

/**
 * Get companies for the agency
 *
 * @param array $params Query parameters
 * @return array
 * @throws LoxoApiException
 */
public function getCompanies(array $params = []): array;
```

#### 3. Implement in Service

```php
// src/Services/LoxoApiService.php

/**
 * Get companies for the agency
 *
 * @param array $params Query parameters:
 *  - per_page (int): Number of results per page
 *  - page (int): Page number starting at 1
 *  - query (string): Search query with Lucene syntax
 *  - include_related_agencies (bool): Include related agencies
 *  - company_global_status_id (int): Filter by status
 *  - serialization_set (string): Serialization set
 * @return array
 * @throws LoxoApiException
 */
public function getCompanies(array $params = []): array
{
    return $this->get('companies', $params);
}
```

#### 4. Write Tests

```php
// tests/Unit/LoxoApiServiceTest.php

public function test_get_companies_makes_correct_request()
{
    // Mock HTTP client
    $mockClient = $this->createMock(Client::class);
    
    // Set up expectations
    $mockResponse = $this->createMock(ResponseInterface::class);
    $mockResponse->method('getStatusCode')->willReturn(200);
    $mockResponse->method('getBody')->willReturn(json_encode(['companies' => []]));
    
    $mockClient->expects($this->once())
        ->method('request')
        ->with('GET', 'companies', ['query' => ['per_page' => 20]])
        ->willReturn($mockResponse);
    
    // Test the method
    $service = new LoxoApiService();
    $this->setClientProperty($service, $mockClient);
    
    $result = $service->getCompanies(['per_page' => 20]);
    
    $this->assertIsArray($result);
    $this->assertArrayHasKey('companies', $result);
}
```

#### 5. Update Documentation

**Update these files:**
- `README.md` - Add usage examples
- `docs/API_COVERAGE.md` - Change status from ❌ to ✅
- `CHANGELOG.md` - Add to unreleased section

#### 6. Test Your Implementation

```bash
# Test specific endpoint (if applicable)
make companies

# Test all endpoints
make quick

# Run automated tests
make test
```

### Endpoint Implementation Patterns

#### Simple GET Endpoint
```php
public function getCountries(array $params = []): array
{
    return $this->get('countries', $params);
}
```

#### POST with Data
```php
public function createCompany(array $companyData): array
{
    return $this->post('companies', $companyData);
}
```

#### Endpoints with ID Parameters
```php
public function getCompany(int $companyId): array
{
    return $this->get("companies/{$companyId}");
}

public function updateCompany(int $companyId, array $companyData): array
{
    return $this->put("companies/{$companyId}", $companyData);
}

public function deleteCompany(int $companyId): array
{
    return $this->delete("companies/{$companyId}");
}
```

#### File Upload Endpoints
```php
public function uploadDocument(int $companyId, array $documentData): array
{
    $multipartData = [];
    
    foreach ($documentData as $key => $value) {
        if ($key === 'document' && is_resource($value)) {
            $multipartData[] = [
                'name' => $key, 
                'contents' => $value, 
                'filename' => 'document.pdf'
            ];
        } else {
            $multipartData[] = ['name' => $key, 'contents' => (string) $value];
        }
    }
    
    return $this->postMultipart("companies/{$companyId}/documents", $multipartData);
}
```

---

## 🧪 Testing Guidelines

### Testing Approaches

**1. Development Testing (Real API)**
```bash
# Quick test during development
make quick

# Test specific endpoints
make activity-types
make address-types
make jobs
```

**2. Automated Testing (Mock Data)**
```bash
# Unit tests with mocks
make test

# Specific test file
vendor/bin/phpunit tests/Unit/LoxoApiServiceTest.php
```

### Writing Tests

**Test Structure:**
```php
public function test_method_name_behavior()
{
    // Arrange - Set up mocks and data
    $mockClient = $this->createMock(Client::class);
    
    // Act - Execute the method
    $result = $this->service->methodName($params);
    
    // Assert - Verify expectations
    $this->assertIsArray($result);
    $this->assertEquals($expected, $result);
}
```

**Testing Guidelines:**
- Test successful responses
- Test error conditions
- Test parameter validation
- Mock external dependencies
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)

---

## 📚 Documentation Standards

### Code Documentation

**PHPDoc Standards:**
```php
/**
 * Brief method description
 *
 * @param array $params Query parameters:
 *  - param_name (type): Parameter description
 *  - another_param (type): Another description
 * @return array
 * @throws LoxoApiException
 */
public function methodName(array $params = []): array
```

**Comments in English:**
- Write all comments and documentation in English
- Use clear, concise language
- Include parameter descriptions and types
- Document exceptions that may be thrown

### File Documentation

**When adding new endpoints, update:**
1. **Method docblocks** with parameter details
2. **README.md** with usage examples
3. **API_COVERAGE.md** with implementation status
4. **CHANGELOG.md** with changes

---

## 🔄 Development Workflow

### Daily Development

```bash
# 1. Start with latest code
git checkout main
git pull upstream main

# 2. Create feature branch
git checkout -b feature/add-companies-api

# 3. Make changes and test frequently
# Edit files...
make quick                    # Test with real API
make test                    # Run automated tests

# 4. Commit changes
git add .
git commit -m "feat: add companies API endpoints

- Add getCompanies() method
- Add createCompany() method  
- Add getCompany() method
- Update API coverage documentation
- Add comprehensive tests"

# 5. Push and create PR
git push origin feature/add-companies-api
```

### Code Quality

**Follow these standards:**
- **PSR-12** coding standards
- **DRY principles** - Don't repeat yourself
- **SOLID principles** - Clean, maintainable code
- **Type hints** - Use strict typing
- **Error handling** - Proper exception handling

**Code Formatting:**
```bash
# Check code style (if available)
make lint

# Format code manually to follow PSR-12
```

---

## 📝 Pull Request Process

### Before Submitting

**Checklist:**
- [ ] All tests pass (`make test`)
- [ ] Real API testing works (`make quick`)
- [ ] Documentation updated
- [ ] API coverage updated
- [ ] CHANGELOG.md updated
- [ ] Code follows PSR-12 standards
- [ ] Commit messages are descriptive

### PR Template

```markdown
## 🎯 Purpose
Brief description of what this PR adds/fixes.

## 📊 API Coverage
- ✅ New endpoints implemented: X
- 📈 Coverage increased from X% to Y%

## 🧪 Testing
- [ ] Unit tests added/updated
- [ ] Manual testing with real API completed
- [ ] All existing tests still pass

## 📚 Documentation
- [ ] README.md updated with examples
- [ ] API_COVERAGE.md updated
- [ ] CHANGELOG.md updated
- [ ] Code comments added

## 🔍 Changes
- List of specific changes made
- New methods added
- Files modified

## 🎬 Demo
```php
// Example usage of new functionality
$companies = Loxo::getCompanies(['per_page' => 20]);
```
```

### Review Process

1. **Automated checks** - GitHub Actions will run tests
2. **Code review** - Maintainer will review code quality
3. **Manual testing** - Verify functionality works
4. **Documentation review** - Ensure docs are complete
5. **Merge** - Once approved, changes will be merged

---

## 🐛 Bug Reports

### How to Report

**Use GitHub Issues with this template:**

```markdown
## 🐛 Bug Description
Clear description of the bug.

## 🔄 Steps to Reproduce
1. Step 1
2. Step 2
3. See error

## 💭 Expected Behavior
What should happen.

## 🔍 Actual Behavior
What actually happens.

## 🌍 Environment
- PHP Version: X.X
- Laravel Version: X.X
- Package Version: X.X
- OS: macOS/Linux/Windows

## 📋 Additional Context
- Error messages
- Stack traces
- Configuration details
```

### Security Issues

**For security vulnerabilities:**
- Do NOT create public issues
- Email: iamarsenibragimov@gmail.com
- Include detailed reproduction steps
- We'll respond within 48 hours

---

## 💡 Feature Requests

### How to Request

**Use GitHub Issues with this template:**

```markdown
## 🚀 Feature Request
Brief description of the feature.

## 🎯 Problem Statement
What problem does this solve?

## 💡 Proposed Solution
How should it work?

## 🔄 Alternative Solutions
Other ways to solve this.

## 📊 API Priority
Which Loxo API endpoints are involved?

## 🎬 Example Usage
```php
// How the feature would be used
$result = Loxo::newFeature($params);
```
```

### Feature Priority

**We prioritize features based on:**
1. **API coverage gaps** - Missing high-priority endpoints
2. **Community demand** - Requested by multiple users
3. **Development complexity** - Easier features first
4. **Loxo API stability** - Stable endpoints preferred

---

## 🏆 Recognition

### Contributors Wall

We recognize all contributors in:
- **README.md** contributors section
- **CHANGELOG.md** for their contributions
- **Release notes** for major contributions

### Types of Contributions

**All contributions are valued:**
- 🔧 **Code** - New endpoints, bug fixes, improvements
- 📚 **Documentation** - README, guides, examples
- 🧪 **Testing** - Test cases, validation, QA
- 🐛 **Bug Reports** - Finding and reporting issues
- 💡 **Ideas** - Feature requests, suggestions
- 🎯 **API Research** - Exploring [Loxo API documentation](https://loxo.readme.io/reference/loxo-api)

---

## 📞 Getting Help

### Communication Channels

**GitHub Issues:**
- Bug reports
- Feature requests
- General questions

**Email Support:**
- iamarsenibragimov@gmail.com
- Response within 48 hours

### Common Questions

**Q: How do I get Loxo API credentials for testing?**
A: You need a [Loxo account](https://loxo.co) with API access. Check the [official API documentation](https://loxo.readme.io/reference/loxo-api) or contact Loxo support for API key access.

**Q: Which endpoints should I work on first?**
A: Check [API_COVERAGE.md](../docs/API_COVERAGE.md) for priority areas. Companies API is highest priority.

**Q: How do I test without affecting production data?**
A: Use a Loxo sandbox/development environment if available, or create test data carefully.

**Q: What if I break existing functionality?**
A: Our automated tests catch most issues. Always run `make test` before submitting.

---

## 🎉 Thank You!

Thank you for contributing to the Loxo Laravel API package! Your contributions help the Laravel community integrate better with Loxo's recruitment platform.

**Happy coding!** 🚀

---

*Last updated: 2024-12-19*
