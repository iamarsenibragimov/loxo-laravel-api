# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.2] - 2025-01-28

### Fixed
- **Critical API Data Format Fixes:**
  - Fixed **ALL CREATE methods** to use form-encoded data instead of JSON:
    - `createPersonEvent()` - Person events creation
    - `createCompany()` - Company creation
    - `createWebhook()` - Webhook creation
    - `createJob()` - Job creation
    - `createPerson()` - Person/candidate creation
    - `createPersonEducationProfile()` - Education profile creation
    - `createSms()` - SMS creation
  - Fixed **ALL UPDATE methods** to use form-encoded data instead of JSON:
    - `updateWebhook()` - Webhook updates
    - `updateJobCandidate()` - Job candidate updates
    - `updatePerson()` - Person/candidate updates
  - **Root Cause:** Loxo API expects form-encoded data (`application/x-www-form-urlencoded`) for CREATE and UPDATE operations, not JSON
  - **Impact:** Resolved 422 errors across all create/update endpoints
- **New HTTP Method Support:**
  - Added `putForm()` method for form-encoded PUT requests
  - Enhanced API compatibility with Loxo platform requirements

### API Coverage
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 27 (18.8%)
- **Remaining:** 117 (81.3%)

## [Unreleased]

### Added
- **New SMS API Endpoints:**
  - `getSms()` - Get SMS messages with filtering and pagination support
  - `createSms()` - Send new SMS messages with full parameter support
  - `getSmsById()` - Get specific SMS message by ID
- **New Configuration API Endpoints:**
  - `getSeniorityLevels()` - Get seniority levels for the agency
  - `getScorecardVisibilityTypes()` - Get scorecard visibility types for the agency
  - `getScorecardTypes()` - Get scorecard types for the agency
  - `getPronouns()` - Get pronouns for person profiles
  - `getPhoneTypes()` - Get phone types for contact information
  - `getPersonTypes()` - Get person types for classification
  - `getPersonShareFieldTypes()` - Get person share field types for data sharing configuration
  - `getPersonLists()` - Get person lists for candidate organization
- **New Data Management API Endpoints:**
  - `getMerges()` - Get merge records with comprehensive filtering options (scroll_id, per_page, item_type, item_ids, created_before, created_after)
  - `getQuestionTypes()` - Get question types for the agency
  - `getSocialProfileTypes()` - Get social profile types for the agency
  - `getEducationTypes()` - Get education types for the agency
- **New Demographics & Diversity API Endpoints:**
  - `getGenders()` - Get gender options for the agency
  - `getEthnicities()` - Get ethnicity options for the agency
  - `getDiversityTypes()` - Get diversity types for the agency
  - `getDisabilityStatuses()` - Get disability status options for the agency
- **New Compensation & Financial API Endpoints:**
  - `getFeeTypes()` - Get fee types for the agency
  - `getEquityTypes()` - Get equity types for the agency
  - `getCompensationTypes()` - Get compensation types for the agency
- **New Email & Communication API Endpoints:**
  - `getEmailTypes()` - Get email types for the agency
  - `getEmailTracking()` - Get email tracking data with comprehensive filtering options (scroll_id, per_page, person_ids, created_at_start, created_at_end)
- **New Geography & Location API Endpoints:**
  - `getCountries()` - Get countries with search and pagination support (per_page, page, query)
  - `getStates()` - Get states for a specific country (page, per_page, query, country_id)
  - `getCities()` - Get cities for a specific country and state (scroll_id, per_page, country_id, state_id)
  - `getCurrencies()` - Get currencies for the agency
- **New Company Management API Endpoints:**
  - `getCompanyGlobalStatuses()` - Get company global statuses for the agency
  - `getCompanyTypes()` - Get company types for the agency
- **Enhanced Communication Support:**
  - Complete SMS messaging functionality for the Loxo platform
  - Support for SMS filtering by date range and pagination
  - Comprehensive SMS creation with job and person associations
- **Enhanced Configuration Support:**
  - Seniority levels management for job positions
  - Scorecard configuration and visibility settings
  - Complete scorecard types functionality
  - Pronouns management for inclusive person profiles
  - Phone types classification for contact information
  - Person types categorization for better organization
  - Person share field types for data sharing configuration
  - Person lists management for candidate organization
- **Enhanced Data Management Support:**
  - Merge records tracking with comprehensive filtering capabilities
  - Question type management for custom forms and surveys
  - Social profile types management for candidate social media profiles
  - Education types management for academic qualification classification
- **Enhanced Demographics & Diversity Support:**
  - Gender identity options for inclusive candidate profiles
  - Ethnicity classification for diversity tracking and reporting
  - Comprehensive diversity types for advanced diversity and inclusion initiatives
  - Disability status management for ADA compliance and accessibility tracking
- **Enhanced Compensation & Financial Support:**
  - Fee type management for recruitment agency billing structures
  - Equity compensation types for modern startup and corporate packages
  - Comprehensive compensation classification for salary and wage management
- **Enhanced Email & Communication Support:**
  - Email type classification for contact management
  - Advanced email tracking with open/click analytics and comprehensive filtering
- **Enhanced Geography & Location Support:**
  - Complete geographical hierarchy support (countries → states → cities)
  - Advanced search capabilities with Lucene query syntax
  - Comprehensive currency management for international operations
  - Hierarchical location data with coordinates for cities
- **Enhanced Company Management Support:**
  - Company global status tracking for recruitment pipeline management
  - Company type classification for client relationship management
  - Status-based workflow automation and reporting capabilities
- **Testing:**
  - Added comprehensive unit tests for all SMS endpoints
  - Added unit tests for configuration endpoints (seniority levels, scorecard types, pronouns, phone types, person types, person share field types, person lists)
  - Added comprehensive unit tests for data management endpoints (merges, question types, social profile types, education types)
  - Added comprehensive unit tests for demographics & diversity endpoints (genders, ethnicities, diversity types, disability statuses)
  - Added comprehensive unit tests for compensation & financial endpoints (fee types, equity types, compensation types)
  - Added comprehensive unit tests for email & communication endpoints (email types, email tracking)
  - Added comprehensive unit tests for geography & location endpoints (countries, states, cities, currencies)
  - Added comprehensive unit tests for company management endpoints (company global statuses, company types)
  - All tests include success scenarios, parameter validation, and error handling
  - Total test coverage: 163 tests with 202 assertions
- **Documentation:**
  - New SMS example file (`examples/sms_example.php`)
  - Updated README.md with SMS and configuration usage examples
  - Updated API coverage from 54 to 56 endpoints (38.9% coverage)
  - Updated CONTRIBUTING.md with current implementation status
- **Development Tools:**
  - Updated Makefile with individual test commands for all new endpoints
  - Added grouped test commands for endpoint categories (geography, company, demographics, etc.)
  - Added `test-new-endpoints` command to test all newly implemented endpoints at once

## [1.1.1] - 2025-01-19

### Added
- **New API Endpoint:**
  - `createJob()` - Create new job positions with full parameter support
- **Enhanced Jobs Support:**
  - Complete POST `/api/{agency_slug}/jobs` endpoint implementation
  - Support for all job creation parameters including custom fields
  - Comprehensive validation and error handling
- **Documentation:**
  - New jobs example file (`examples/jobs_example.php`)
  - Updated API coverage documentation (18.8% coverage)
- **Testing:**
  - 5 new comprehensive unit tests for job creation functionality
  - Tests cover basic creation, full data, custom fields, and error scenarios

### Changed
- Jobs endpoint status updated from "partially implemented" to "fully implemented"
- API coverage increased from 26 to 27 implemented endpoints (18.1% → 18.8%)

### API Coverage
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 27 (18.8%) - **+1 new endpoint**
- **Remaining:** 117 (81.3%)

## [1.1.0] - 2025-01-15

### Added
- **New API Endpoints (5 new endpoints implemented):**
  - `getUsers()` - Users listing
  - `getPersonEvents()` - Person events listing  
  - `createPersonEvent()` - Create person event
  - `getJobCandidates()` - Job candidates listing
  - `getJobCandidate()` - Get job candidate by ID
  - `getJobs()` - Jobs listing
  - `getPeople()` - People/candidates listing
  - `createPerson()` - Create person/candidate
  - `applyToJob()` - Apply to job
  - `createPersonEducationProfile()` - Create education profile
- Simple development testing tools
- Makefile with quick commands (`make quick`, `make help`, etc.)
- Standalone test script (`test-standalone.php`)
- Development bootstrap script (`dev-bootstrap.php`)
- Simple PHPUnit tests without Laravel dependencies
- Developer cheatsheet and simple testing guide
- New endpoint guide for contributors
- Comprehensive examples for people and education profiles endpoints

### Changed
- Renamed facade from `LoxoApi` to `Loxo` for simplicity
- Updated all documentation to use shorter facade name
- Enhanced API coverage documentation with current implementation status
- Updated `.gitignore` to include `.phpunit.cache` for improved build management

### Developer Experience
- Testing time reduced from 2-3 minutes to 5 seconds
- No need for separate Laravel test project
- 4 different testing methods for different use cases
- Instant feedback during development

### API Coverage
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 26 (18.1%) - **+5 new endpoints**
- **Remaining:** 118 (81.9%)

## [1.0.0] - 2025-01-14

### Added
- Initial release of Loxo Laravel API Package (Unofficial)
- Support for Loxo API authentication
- `LoxoApiService` for API interactions
- `LoxoApi` facade for easy access
- Activity types endpoint support
- Address types endpoint support
- Generic HTTP methods (GET, POST, PUT, DELETE)
- Comprehensive error handling with custom exceptions
- Retry logic for failed requests
- Configuration file for customization
- PHPUnit tests
- Complete documentation

### Features
- Easy Laravel integration via ServiceProvider
- Configurable timeout and retry settings
- Type-safe API with contracts
- Comprehensive error handling
- Built-in retry mechanism for reliability
